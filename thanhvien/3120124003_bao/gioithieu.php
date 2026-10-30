<?php
/**
 * thanhvien/3120124003_bao/gioithieu.php — Hồ sơ cá nhân: Lê Hoàng Bảo (MSSV: 3120124003)
 * Học phần: Thiết kế và Lập trình Web — Bài tập nhóm số 6 (Phần B)
 * Bảng riêng CSDL MySQL: tv_luu_but (id, mssv, ten_khach, loi_nhan, thoi_gian)
 * 
 * Cách kiểm thử các chức năng Phần B:
 * 1. Gửi lời nhắn trống hoặc quá ngắn (< 2 ký tự tên, < 5 ký tự lời nhắn) -> Báo lỗi validation.
 * 2. Gửi lời nhắn hợp lệ -> Lưu vào bảng MySQL tv_luu_but, áp dụng PRG (F5 không gửi lại).
 * 3. Kiểm tra phân trang: 5 lời nhắn/trang (ORDER BY thoi_gian DESC, id DESC LIMIT 5 OFFSET ?);
 *    bấm số trang "1", "2" để chuyển trang và quan sát dữ liệu phân trang.
 * 4. Kiểm tra phân quyền chủ trang xóa lời nhắn:
 *    - Khách vãng lai / Tài khoản khác (ví dụ: lan@ued.vn): KHÔNG thấy nút Xóa.
 *    - Chủ trang đăng nhập (bao@ued.vn / mật khẩu matkhau123): Xuất hiện nút "🗑️ Xóa".
 *    - Thao tác xóa bảo mật qua POST máy chủ: DELETE FROM tv_luu_but WHERE id = ? AND mssv = '3120124003'.
 */

require_once __DIR__ . '/../../inc/config.php';

const MSSV_BAO = '3120124003';
$goc = '../../';
$tieu_de_trang  = 'Hồ sơ cá nhân: Lê Hoàng Bảo';
$meta_mo_ta     = 'Trang giới thiệu cá nhân của Lê Hoàng Bảo - Trưởng nhóm dự án ITeduShare.';
$trang_hien_tai = '';

// ============================================================================
// 1. KIỂM TRA PHÂN QUYỀN CHỦ TRANG QUA BẢNG thanh_vien & nguoiDung()
// ============================================================================
$stTV = db()->prepare('SELECT ho_ten, nguoi_dung_id FROM thanh_vien WHERE mssv = ?');
$stTV->execute([MSSV_BAO]);
$thongTinTV = $stTV->fetch();

$userHienTai = nguoiDung();
$laChuTrang  = ($userHienTai !== null) && ((int)($userHienTai['id'] ?? 0) === (int)($thongTinTV['nguoi_dung_id'] ?? 0));

// ============================================================================
// 2. XỬ LÝ CÁC YÊU CẦU POST (THÊM / XÓA LƯU BÚT, BÌNH CHỌN)
// ============================================================================
$loiLuuBut = [];
$duLuuBut  = [
    'ten'      => '',
    'loi_nhan' => ''
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // CHỨC NĂNG CSDL 2: CHỦ TRANG XÓA LỜI NHẮN
    if ($action === 'xoa_luu_but') {
        $idXoa = filter_var($_POST['id'] ?? '', FILTER_VALIDATE_INT);
        if ($idXoa && $laChuTrang) {
            // Bảo vệ nhiều lớp: Chỉ xóa đúng ID thuộc MSSV của Bảo
            $stXoa = db()->prepare('DELETE FROM tv_luu_but WHERE id = ? AND mssv = ?');
            $stXoa->execute([$idXoa, MSSV_BAO]);
            $_SESSION['flash_luubut'] = 'Đã xóa lời nhắn thành công khỏi CSDL!';
            chuyen_trang('gioithieu.php');
        } else {
            $_SESSION['flash_luubut_loi'] = 'Bạn không có quyền thực hiện xóa lời nhắn này!';
            chuyen_trang('gioithieu.php');
        }
    }

    // CHỨC NĂNG CSDL 1: THÊM LỜI NHẮN VÀO MYSQL (tv_luu_but)
    if ($action === 'luu_but') {
        $ten  = trim($_POST['ten'] ?? '');
        $nhan = trim($_POST['loi_nhan'] ?? '');

        $duLuuBut['ten']      = $ten;
        $duLuuBut['loi_nhan'] = $nhan;

        if ($ten === '' || mb_strlen($ten, 'UTF-8') < 2) {
            $loiLuuBut['ten'] = 'Họ tên phải có ít nhất 2 ký tự.';
        } elseif (mb_strlen($ten, 'UTF-8') > 50) {
            $loiLuuBut['ten'] = 'Họ tên không được vượt quá 50 ký tự.';
        }

        if ($nhan === '' || mb_strlen($nhan, 'UTF-8') < 5) {
            $loiLuuBut['loi_nhan'] = 'Lời nhắn quá ngắn (cần ít nhất 5 ký tự).';
        } elseif (mb_strlen($nhan, 'UTF-8') > 500) {
            $loiLuuBut['loi_nhan'] = 'Lời nhắn không được vượt quá 500 ký tự.';
        }

        if (empty($loiLuuBut)) {
            $stThem = db()->prepare('INSERT INTO tv_luu_but (mssv, ten_khach, loi_nhan) VALUES (?, ?, ?)');
            $stThem->execute([MSSV_BAO, $ten, $nhan]);

            $_SESSION['flash_luubut'] = 'Cảm ơn bạn đã ghi lại lời nhắn lưu bút!';
            chuyen_trang('gioithieu.php');
        }
    }

    // CHỨC NĂNG BÀI TẬP 5: KHẢO SÁT / BÌNH CHỌN CÔNG NGHỆ
    if ($action === 'binh_chon') {
        $cacTuyChon = ['PHP', 'Python', 'JavaScript', 'C++'];
        $luaChon    = $_POST['ngon_ngu'] ?? '';
        $fileBC     = __DIR__ . '/../../storage/3120124003_binhchon.json';

        if (in_array($luaChon, $cacTuyChon, true)) {
            if (!is_dir(dirname($fileBC))) {
                mkdir(dirname($fileBC), 0777, true);
            }
            $ketQua = file_exists($fileBC) ? json_decode((string)file_get_contents($fileBC), true) : [];
            if (!is_array($ketQua)) $ketQua = [];
            if (!isset($ketQua[$luaChon])) $ketQua[$luaChon] = 0;
            $ketQua[$luaChon]++;

            file_put_contents($fileBC, json_encode($ketQua, JSON_UNESCAPED_UNICODE));
            $_SESSION['flash_binhchon'] = 'Bình chọn công nghệ thành công!';
            chuyen_trang('gioithieu.php');
        } else {
            $_SESSION['flash_binhchon_loi'] = 'Vui lòng chọn một ngôn ngữ hợp lệ.';
            chuyen_trang('gioithieu.php');
        }
    }
}

// ============================================================================
// 3. TRUY VẤN DỮ LIỆU SỔ LƯU BÚT CÓ PHÂN TRANG (5 MỤC/TRANG)
// ============================================================================
$stDem = db()->prepare('SELECT COUNT(*) FROM tv_luu_but WHERE mssv = ?');
$stDem->execute([MSSV_BAO]);
$tongLoiNhan = (int)$stDem->fetchColumn();

$moiTrang = 5;
$soTrang  = max(1, (int)ceil($tongLoiNhan / $moiTrang));

$trangHienTaiLB = filter_var($_GET['trang'] ?? 1, FILTER_VALIDATE_INT, [
    'options' => ['min_range' => 1, 'default' => 1]
]);
if ($trangHienTaiLB > $soTrang) {
    $trangHienTaiLB = $soTrang;
}
$offset = ($trangHienTaiLB - 1) * $moiTrang;

$stLB = db()->prepare('SELECT id, ten_khach, loi_nhan, thoi_gian 
                       FROM tv_luu_but 
                       WHERE mssv = ? 
                       ORDER BY thoi_gian DESC, id DESC 
                       LIMIT ? OFFSET ?');
$stLB->bindValue(1, MSSV_BAO);
$stLB->bindValue(2, $moiTrang, PDO::PARAM_INT);
$stLB->bindValue(3, $offset, PDO::PARAM_INT);
$stLB->execute();
$danhSachLuuBut = $stLB->fetchAll();

// Đọc và xóa flash messages
$flashLuuBut    = $_SESSION['flash_luubut'] ?? '';
unset($_SESSION['flash_luubut']);
$flashLuuButLoi = $_SESSION['flash_luubut_loi'] ?? '';
unset($_SESSION['flash_luubut_loi']);

// Đọc kết quả khảo sát (Bài 5)
$fileBinhChon   = __DIR__ . '/../../storage/3120124003_binhchon.json';
$ketQuaBinhChon = file_exists($fileBinhChon) ? json_decode((string)file_get_contents($fileBinhChon), true) : [];
if (!is_array($ketQuaBinhChon)) $ketQuaBinhChon = [];
$tongPhieuBC    = array_sum($ketQuaBinhChon);
$cacTuyChonBC   = ['PHP', 'Python', 'JavaScript', 'C++'];
$flashBC        = $_SESSION['flash_binhchon'] ?? '';
unset($_SESSION['flash_binhchon']);
$flashBCLoi     = $_SESSION['flash_binhchon_loi'] ?? '';
unset($_SESSION['flash_binhchon_loi']);

require_once __DIR__ . '/../../inc/header.php';
?>

<!-- Link CSS cá nhân -->
<link rel="stylesheet" href="css/canhan-bao.css">

<!-- Thanh điều hướng liên kết nhanh -->
<nav class="thanh-dieu-huong" aria-label="Menu cá nhân" style="margin-bottom: 1.5rem;">
  <ul class="menu-chinh" style="display: flex; gap: 1rem; list-style: none; background: #f4f6f8; padding: 0.8rem 1.2rem; border-radius: 8px; flex-wrap: wrap;">
    <li><a href="../../index.php" style="text-decoration: none; font-weight: bold; color: var(--mau-chinh, #004085);">&larr; Về Trang chủ</a></li>
    <li><a href="../../ve-chung-toi.php" style="text-decoration: none; font-weight: bold; color: var(--mau-chinh, #004085);">&larr; Danh sách thành viên nhóm</a></li>
  </ul>
</nav>

<main class="trang__chinh phan-lien-he">
  <h1 class="phan-lien-he__tieu-de can-giua-chu">Hồ sơ thành viên: Lê Hoàng Bảo</h1>

  <!-- Banner thông báo vai trò hiện tại -->
  <?php if ($laChuTrang): ?>
      <div role="status" style="max-width: 1000px; margin: 0 auto 1.5rem auto; padding: 12px 18px; background: #d4edda; color: #155724; border: 1px solid #c3e6cb; border-radius: 8px; font-size: 0.95rem;">
          👑 <strong>Bạn đang đăng nhập với tư cách Chủ trang (Lê Hoàng Bảo)!</strong> Bạn có toàn quyền quản trị và thực hiện xóa các lời nhắn lưu bút.
      </div>
  <?php else: ?>
      <div style="max-width: 1000px; margin: 0 auto 1.5rem auto; padding: 12px 18px; background: #e8f4fd; color: #0c5460; border: 1px solid #bee5eb; border-radius: 8px; font-size: 0.9rem;">
          ℹ️ <em>Bạn đang xem trang với tư cách Khách. Chỉ khi chủ trang (tài khoản Lê Hoàng Bảo: <code>bao@ued.vn</code>) đăng nhập thì mới có quyền xóa lời nhắn lưu bút.</em>
      </div>
  <?php endif; ?>

  <div style="display: flex; flex-wrap: wrap; gap: 2rem; margin-bottom: 2rem;">
    <!-- CỘT TRÁI: GIỚI THIỆU BẢN THÂN -->
    <section class="bieu-mau" style="flex: 1; min-width: 320px;">
      <h2>Giới thiệu bản thân</h2>
      <div class="gioi-thieu-grid">
          <div class="anh-dai-dien">
             <img src="avatar-bao.jpg" alt="Ảnh chân dung của Lê Hoàng Bảo" class="anh-chan-dung" style="width: 200px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
          </div>
          <div class="noi-dung-gioi-thieu">
             <p>Xin chào, tôi là <strong>Lê Hoàng Bảo</strong> (MSSV: 3120124003), sinh viên Khoa Toán - Tin, Trường Đại học Sư phạm - Đại học Đà Nẵng. Tôi đảm nhận vai trò <strong>Trưởng nhóm 02</strong> trong dự án ITeduShare.</p>
             
             <h3>Danh sách kỹ năng nổi bật</h3>
             <ul class="danh-sach-ky-nang">
               <li>Lập trình PHP &amp; MySQL (PDO, Transaction)</li>
               <li>Lập trình Python (Pygame, Tkinter, OpenCV)</li>
               <li>Thiết kế CSDL quan hệ chuẩn hóa 3NF</li>
               <li>HTML5, CSS3 Semantic &amp; JavaScript Modern</li>
               <li>Quản lý mã nguồn với Git/GitHub</li>
             </ul>
          </div>
      </div>

      <article style="margin-top: 1.5rem; border-top: 1px solid #eee; padding-top: 1rem;">
        <h3>Vai trò trong dự án ITeduShare</h3>
        <p>Phụ trách khởi tạo CSDL, thiết kế cấu trúc bảng và dữ liệu mẫu, xây dựng hàm kết nối Singleton PDO, đảm nhiệm Chức năng 1 (Trang chủ, Chi tiết giáo án, Giỏ hàng chống N+1, Lưu liên hệ) và quản lý tiến độ chung của nhóm.</p>
        <button type="button" class="nut-thao-tac nut-thao-tac--xem nut-lien-he-ca-nhan">Kết nối với tôi</button>
      </article>
    </section>

    <!-- CỘT PHẢI: 2 CHỨC NĂNG ĐỘNG (SỔ LƯU BÚT MYSQL + KHẢO SÁT) -->
    <div style="flex: 1; min-width: 320px; display: flex; flex-direction: column; gap: 2rem;">
      
      <!-- ================================================================= -->
      <!-- CHỨC NĂNG CSDL 1 & 2: SỔ LƯU BÚT PHÂN TRANG & PHÂN QUYỀN CHỦ TRANG -->
      <!-- ================================================================= -->
      <section class="bieu-mau" style="background-color: #fffdf5; border-color: #ffeeba;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem; flex-wrap: wrap; gap: 0.5rem;">
            <h2 style="color: #856404; margin: 0;">📝 Sổ Lưu Bút Khách Thăm</h2>
            <span style="font-size: 0.85rem; background: #fff3cd; color: #856404; padding: 0.2rem 0.6rem; border-radius: 999px; border: 1px solid #ffeeba; font-weight: bold;">
                Tổng: <?= $tongLoiNhan ?> lời nhắn
            </span>
        </div>
        <p style="font-size: 0.9rem; color: #666; margin-bottom: 1rem;">
            Dữ liệu lưu trữ trực tiếp vào CSDL MySQL (bảng <code>tv_luu_but</code>).
        </p>

        <!-- Thông báo Flash Sổ lưu bút -->
        <?php if ($flashLuuBut): ?>
            <div role="alert" style="padding: 10px; background: #d4edda; color: #155724; margin-bottom: 12px; border-radius: 4px; border: 1px solid #c3e6cb;">
                <?= e($flashLuuBut) ?>
            </div>
        <?php endif; ?>
        <?php if ($flashLuuButLoi): ?>
            <div role="alert" style="padding: 10px; background: #f8d7da; color: #721c24; margin-bottom: 12px; border-radius: 4px; border: 1px solid #f5c6cb;">
                <?= e($flashLuuButLoi) ?>
            </div>
        <?php endif; ?>

        <!-- Form gửi lời nhắn lưu bút -->
        <form method="POST" action="gioithieu.php" novalidate>
            <input type="hidden" name="action" value="luu_but">
            
            <div style="margin-bottom: 10px;">
                <label for="ten_luubut" style="font-weight: bold; display: block; margin-bottom: 4px;">
                    Họ và tên của bạn: <span style="color: red;">*</span>
                </label>
                <input type="text" id="ten_luubut" name="ten" 
                       style="width: 100%; padding: 8px; border: 1px solid <?= isset($loiLuuBut['ten']) ? '#dc3545' : '#ccc' ?>; border-radius: 4px;" 
                       value="<?= e($duLuuBut['ten']) ?>" required maxlength="50">
                <?php if (isset($loiLuuBut['ten'])): ?>
                    <span style="color: #dc3545; font-size: 0.85rem; margin-top: 3px; display: block;">
                        <?= e($loiLuuBut['ten']) ?>
                    </span>
                <?php endif; ?>
            </div>

            <div style="margin-bottom: 12px;">
                <label for="loi_nhan_luubut" style="font-weight: bold; display: block; margin-bottom: 4px;">
                    Lời nhắn lưu bút: <span style="color: red;">*</span>
                </label>
                <textarea id="loi_nhan_luubut" name="loi_nhan" rows="3" 
                          style="width: 100%; padding: 8px; border: 1px solid <?= isset($loiLuuBut['loi_nhan']) ? '#dc3545' : '#ccc' ?>; border-radius: 4px;" 
                          required maxlength="500"><?= e($duLuuBut['loi_nhan']) ?></textarea>
                <?php if (isset($loiLuuBut['loi_nhan'])): ?>
                    <span style="color: #dc3545; font-size: 0.85rem; margin-top: 3px; display: block;">
                        <?= e($loiLuuBut['loi_nhan']) ?>
                    </span>
                <?php endif; ?>
            </div>

            <button type="submit" class="nut-thao-tac nut-thao-tac--sua" style="width: 100%; cursor: pointer;">
                ✍️ Gửi lời nhắn lưu bút
            </button>
        </form>

        <hr style="margin: 18px 0; border: 0; border-top: 1px solid #e0d0a8;">

        <!-- Danh sách lời nhắn phân trang -->
        <h3 style="font-size: 1.05rem; margin-bottom: 12px; color: #493910;">
            Danh sách lời nhắn (Trang <?= $trangHienTaiLB ?> / <?= $soTrang ?>):
        </h3>

        <?php if (empty($danhSachLuuBut)): ?>
            <p style="color: #666; font-style: italic; text-align: center; padding: 1rem;">Chưa có lời nhắn nào.</p>
        <?php else: ?>
            <ul style="list-style: none; padding: 0; margin: 0;">
                <?php foreach ($danhSachLuuBut as $lb): ?>
                    <li style="background: #ffffff; padding: 12px 14px; border: 1px solid #ebd9b0; border-radius: 6px; margin-bottom: 10px; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px; gap: 0.5rem;">
                            <div>
                                <strong style="color: #004085; font-size: 0.95rem;"><?= e($lb['ten_khach']) ?></strong>
                                <span style="font-size: 0.8rem; color: #888; margin-left: 0.4rem;">
                                    (<?= date('d/m/Y H:i', strtotime($lb['thoi_gian'])) ?>)
                                </span>
                            </div>

                            <!-- CHỨC NĂNG 2: NÚT XÓA CHỈ HIỆN KHI LÀ CHỦ TRANG -->
                            <?php if ($laChuTrang): ?>
                                <form method="POST" action="gioithieu.php" onsubmit="return confirm('Bạn có chắc chắn muốn xóa lời nhắn này của khách?');" style="margin: 0;">
                                    <input type="hidden" name="action" value="xoa_luu_but">
                                    <input type="hidden" name="id" value="<?= (int)$lb['id'] ?>">
                                    <button type="submit" style="background: #dc3545; color: white; border: none; padding: 3px 8px; border-radius: 4px; font-size: 0.8rem; cursor: pointer;" title="Chủ trang xóa lời nhắn này">
                                        🗑️ Xóa
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                        <p style="margin: 0; color: #333; line-height: 1.45; font-size: 0.95rem;">
                            <?= nl2br(e($lb['loi_nhan'])) ?>
                        </p>
                    </li>
                <?php endforeach; ?>
            </ul>

            <!-- ĐIỀU HƯỚNG PHÂN TRANG (PAGINATION) -->
            <?php if ($soTrang > 1): ?>
                <nav aria-label="Phân trang lưu bút" style="display: flex; justify-content: center; gap: 0.4rem; margin-top: 15px; align-items: center; flex-wrap: wrap;">
                    <?php if ($trangHienTaiLB > 1): ?>
                        <a href="gioithieu.php?trang=<?= $trangHienTaiLB - 1 ?>" 
                           class="nut-thao-tac" 
                           style="text-decoration: none; padding: 0.25rem 0.6rem; border-radius: 4px; border: 1px solid #ccc; background: #fff; color: #333; font-size: 0.85rem;">
                            &laquo; Trước
                        </a>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $soTrang; $i++): ?>
                        <a href="gioithieu.php?trang=<?= $i ?>" 
                           style="text-decoration: none; padding: 0.25rem 0.65rem; border-radius: 4px; border: 1px solid <?= $i === $trangHienTaiLB ? '#004085' : '#ccc' ?>; <?= $i === $trangHienTaiLB ? 'font-weight: bold; background: #004085; color: white;' : 'background: #fff; color: #333;' ?> font-size: 0.85rem;">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>

                    <?php if ($trangHienTaiLB < $soTrang): ?>
                        <a href="gioithieu.php?trang=<?= $trangHienTaiLB + 1 ?>" 
                           class="nut-thao-tac" 
                           style="text-decoration: none; padding: 0.25rem 0.6rem; border-radius: 4px; border: 1px solid #ccc; background: #fff; color: #333; font-size: 0.85rem;">
                            Sau &raquo;
                        </a>
                    <?php endif; ?>
                </nav>
            <?php endif; ?>
        <?php endif; ?>
      </section>

      <!-- ================================================================= -->
      <!-- CHỨC NĂNG BÀI TẬP 5: KHẢO SÁT CÔNG NGHỆ (BẢO TỒN TÍNH NĂNG CŨ)     -->
      <!-- ================================================================= -->
      <section class="bieu-mau" style="background-color: #f0f8ff; border-color: #b8daff;">
        <h2 style="color: #004085; margin-bottom: 0.5rem;">📊 Khảo sát Công Nghệ Yêu Thích</h2>
        <p style="font-size: 0.9rem; color: #555; margin-bottom: 1rem;">Ngôn ngữ lập trình bạn yêu thích nhất trong các môn Tin học?</p>

        <?php if ($flashBC): ?>
            <div role="alert" style="padding: 10px; background: #d4edda; color: #155724; margin-bottom: 12px; border-radius: 4px;">
                <?= e($flashBC) ?>
            </div>
        <?php endif; ?>
        <?php if ($flashBCLoi): ?>
            <div role="alert" style="padding: 10px; background: #f8d7da; color: #721c24; margin-bottom: 12px; border-radius: 4px;">
                <?= e($flashBCLoi) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="gioithieu.php">
            <input type="hidden" name="action" value="binh_chon">
            <div style="display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 15px;">
                <?php foreach ($cacTuyChonBC as $tc): ?>
                    <label style="background: #fff; padding: 6px 12px; border: 1px solid #ccc; border-radius: 4px; cursor: pointer; font-size: 0.9rem; display: flex; align-items: center; gap: 6px;">
                        <input type="radio" name="ngon_ngu" value="<?= e($tc) ?>" required> <?= e($tc) ?>
                    </label>
                <?php endforeach; ?>
            </div>
            <button type="submit" class="nut-thao-tac nut-thao-tac--xem" style="width: 100%; cursor: pointer;">
                🗳️ Gửi bình chọn
            </button>
        </form>

        <hr style="margin: 18px 0; border: 0; border-top: 1px solid #cfe2ff;">

        <h3 style="font-size: 1.05rem; margin-bottom: 10px; color: #004085;">
            Kết quả bình chọn (<?= $tongPhieuBC ?> phiếu):
        </h3>
        <?php if ($tongPhieuBC > 0): ?>
            <div style="display: flex; flex-direction: column; gap: 10px;">
                <?php foreach ($cacTuyChonBC as $tc): 
                    $soPhieu  = $ketQuaBinhChon[$tc] ?? 0;
                    $phanTram = $tongPhieuBC > 0 ? round(($soPhieu / $tongPhieuBC) * 100) : 0;
                ?>
                <div>
                    <div style="display: flex; justify-content: space-between; font-size: 0.85rem; margin-bottom: 4px;">
                        <strong><?= e($tc) ?></strong>
                        <span><?= $phanTram ?>% (<?= $soPhieu ?> phiếu)</span>
                    </div>
                    <div style="background: #e9ecef; height: 10px; border-radius: 5px; overflow: hidden;">
                        <div style="background: #28a745; height: 100%; width: <?= $phanTram ?>%;"></div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p style="color: #666; font-style: italic; text-align: center; font-size: 0.9rem;">Chưa có lượt bình chọn nào.</p>
        <?php endif; ?>
      </section>

    </div>
  </div>

  <!-- BẢNG THỜI KHÓA BIỂU CÁ NHÂN -->
  <section class="thong-tin">
    <h2>Thời khóa biểu cá nhân</h2>
    <div class="khung-cuon-bang">
      <table class="bang-hoc-lieu" style="width: 100%; border-collapse: collapse; text-align: left;">
        <caption class="bang-hoc-lieu__chu-thich">Kế hoạch học tập và làm việc trong tuần của Lê Hoàng Bảo</caption>
        <thead>
          <tr>
            <th scope="col" style="padding: 0.75rem; border: 1px solid #ddd; background: #004085; color: white;">Thứ</th>
            <th scope="col" style="padding: 0.75rem; border: 1px solid #ddd; background: #004085; color: white;">Sáng</th>
            <th scope="col" style="padding: 0.75rem; border: 1px solid #ddd; background: #004085; color: white;">Chiều</th>
            <th scope="col" style="padding: 0.75rem; border: 1px solid #ddd; background: #004085; color: white;">Tối</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <th scope="row" style="padding: 0.75rem; border: 1px solid #ddd; background: #f9f9f9;">Thứ Hai</th>
            <td style="padding: 0.75rem; border: 1px solid #ddd;">Học Thiết kế Web trên trường</td>
            <td style="padding: 0.75rem; border: 1px solid #ddd;">Tự học PHP &amp; MySQL PDO</td>
            <td style="padding: 0.75rem; border: 1px solid #ddd;">Dạy kèm tin học THPT</td>
          </tr>
          <tr>
            <th scope="row" style="padding: 0.75rem; border: 1px solid #ddd; background: #f9f9f9;">Thứ Ba</th>
            <td style="padding: 0.75rem; border: 1px solid #ddd;">Nghiên cứu tài liệu GDPT 2018</td>
            <td style="padding: 0.75rem; border: 1px solid #ddd;">Học Phương pháp giảng dạy Tin học</td>
            <td style="padding: 0.75rem; border: 1px solid #ddd;">Họp tiến độ Nhóm 02</td>
          </tr>
          <tr>
            <th scope="row" style="padding: 0.75rem; border: 1px solid #ddd; background: #f9f9f9;">Thứ Tư</th>
            <td style="padding: 0.75rem; border: 1px solid #ddd;">Học CSDL nâng cao trên trường</td>
            <td style="padding: 0.75rem; border: 1px solid #ddd;">Review code và kiểm thử W3C</td>
            <td style="padding: 0.75rem; border: 1px solid #ddd;">Lập trình hoàn thiện hệ thống</td>
          </tr>
        </tbody>
      </table>
    </div>
  </section>
</main>

<?php require_once __DIR__ . '/../../inc/footer.php'; ?>

<script type="module" src="js/canhan.js"></script>
<script type="module" src="../../js/main.js"></script>
</body>
</html>
