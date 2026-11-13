<?php
/**
 * thanhvien/3120124003_bao/gioithieu.php — Hồ sơ cá nhân: Lê Hoàng Bảo (MSSV: 3120124003)
 * Học phần: Thiết kế và Lập trình Web — Bài tập nhóm số 6 (Phần B)
 * Bảng riêng CSDL MySQL:
 *   1. tv_luu_but (id, mssv, ten_khach, loi_nhan, thoi_gian)
 *   2. tv_binh_chon (id, mssv, lua_chon, so_phieu)
 * 
 * Cách kiểm thử các chức năng Phần B:
 * 1. Gửi lời nhắn trống hoặc quá ngắn (< 2 ký tự tên, < 5 ký tự lời nhắn) -> Báo lỗi validation.
 * 2. Gửi lời nhắn hợp lệ -> Lưu vào bảng MySQL tv_luu_but, áp dụng PRG (F5 không gửi lại).
 * 3. Kiểm tra phân trang: 5 lời nhắn/trang (ORDER BY thoi_gian DESC, id DESC LIMIT 5 OFFSET ?);
 *    bấm số trang "1", "2" để chuyển trang và quan sát dữ liệu phân trang.
 * 4. Kiểm tra phân quyền chủ trang xóa lời nhắn:
 *    - Khách vãng lai / Tài khoản khác (ví dụ: lan@ued.vn): KHÔNG thấy nút Xóa.
 *    - Chủ trang đăng nhập (bao@ued.vn / mật khẩu matkhau123): Xuất hiện nút "Xóa".
 *    - Thao tác xóa bảo mật qua POST máy chủ: DELETE FROM tv_luu_but WHERE id = ? AND mssv = '3120124003'.
 * 5. Khảo sát / Bình chọn công nghệ: Chọn ngôn ngữ -> Bấm Gửi -> Lưu trực tiếp vào MySQL bảng tv_binh_chon
 *    bằng câu lệnh INSERT ... ON DUPLICATE KEY UPDATE so_phieu = so_phieu + 1 (PRG tránh cộng trùng khi F5).
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
$laChuTrang  = ($userHienTai !== null) && !empty($thongTinTV['nguoi_dung_id']) && ((int)($userHienTai['id'] ?? 0) === (int)$thongTinTV['nguoi_dung_id']);

// ============================================================================
// 2. XỬ LÝ CÁC YÊU CẦU POST (THÊM / XÓA LƯU BÚT, BÌNH CHỌN)
// ============================================================================
$loiLuuBut = [];
$duLuuBut  = [
    'ten'      => '',
    'loi_nhan' => ''
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = chuoi($_POST['action'] ?? '');

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
        $ten  = trim(chuoi($_POST['ten'] ?? ''));
        $nhan = trim(chuoi($_POST['loi_nhan'] ?? ''));

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

    // CHỨC NĂNG CSDL 2: KHẢO SÁT / BÌNH CHỌN CÔNG NGHỆ LƯU MYSQL (tv_binh_chon)
    if ($action === 'binh_chon') {
        $cacTuyChon = ['PHP', 'Python', 'JavaScript', 'C++'];
        $luaChon    = trim(chuoi($_POST['ngon_ngu'] ?? ''));

        if (in_array($luaChon, $cacTuyChon, true)) {
            $stBC = db()->prepare('
                INSERT INTO tv_binh_chon (mssv, lua_chon, so_phieu)
                VALUES (?, ?, 1)
                ON DUPLICATE KEY UPDATE so_phieu = so_phieu + 1
            ');
            $stBC->execute([MSSV_BAO, $luaChon]);
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

// Đọc kết quả khảo sát từ bảng MySQL tv_binh_chon
$cacTuyChonBC   = ['PHP', 'Python', 'JavaScript', 'C++'];
$stDocBC        = db()->prepare('SELECT lua_chon, so_phieu FROM tv_binh_chon WHERE mssv = ?');
$stDocBC->execute([MSSV_BAO]);
$ketQuaBinhChon = [];
foreach ($stDocBC->fetchAll() as $rowBC) {
    $ketQuaBinhChon[(string)$rowBC['lua_chon']] = (int)$rowBC['so_phieu'];
}
$tongPhieuBC    = array_sum($ketQuaBinhChon);
$flashBC        = $_SESSION['flash_binhchon'] ?? '';
unset($_SESSION['flash_binhchon']);
$flashBCLoi     = $_SESSION['flash_binhchon_loi'] ?? '';
unset($_SESSION['flash_binhchon_loi']);

require_once __DIR__ . '/../../inc/header.php';
?>

<!-- Link CSS cá nhân -->
<link rel="stylesheet" href="css/canhan-bao.css">

<!-- Thanh điều hướng liên kết nhanh -->
<nav class="thanh-dieu-huong bao-dieu-huong" aria-label="Menu cá nhân">
  <ul class="menu-chinh bao-menu-phu">
    <li><a href="../../index.php" class="bao-menu-phu__link">&larr; Về Trang chủ</a></li>
    <li><a href="../../ve-chung-toi.php" class="bao-menu-phu__link">&larr; Danh sách thành viên nhóm</a></li>
  </ul>
</nav>

<main class="trang__chinh phan-lien-he">
  <h1 class="phan-lien-he__tieu-de can-giua-chu">Hồ sơ thành viên: Lê Hoàng Bảo</h1>

  <!-- Banner thông báo vai trò hiện tại -->
  <?php if ($laChuTrang): ?>
      <div role="status" class="bao-banner-vai-tro bao-banner-vai-tro--chu">
          <strong>Bạn đang đăng nhập với tư cách Chủ trang (Lê Hoàng Bảo)!</strong> Bạn có toàn quyền quản trị và thực hiện xóa các lời nhắn lưu bút.
      </div>
  <?php else: ?>
      <div class="bao-banner-vai-tro bao-banner-vai-tro--khach">
          <em>Bạn đang xem trang với tư cách Khách. Chỉ khi chủ trang (tài khoản Lê Hoàng Bảo: <code>bao@ued.vn</code>) đăng nhập thì mới có quyền xóa lời nhắn lưu bút.</em>
      </div>
  <?php endif; ?>

  <div class="bao-luoi-hai-cot">
    <!-- CỘT TRÁI: GIỚI THIỆU BẢN THÂN -->
    <section class="bieu-mau bao-cot">
      <h2>Giới thiệu bản thân</h2>
      <div class="gioi-thieu-grid">
          <div class="anh-dai-dien">
             <img src="avatar-bao.jpg" alt="Ảnh chân dung của Lê Hoàng Bảo" class="anh-chan-dung anh-chan-dung--bao">
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

      <article class="bao-vai-tro-box">
        <h3>Vai trò trong dự án ITeduShare</h3>
        <p>Phụ trách khởi tạo CSDL, thiết kế cấu trúc bảng và dữ liệu mẫu, xây dựng hàm kết nối Singleton PDO, đảm nhiệm Chức năng 1 (Trang chủ, Chi tiết giáo án, Giỏ hàng chống N+1, Lưu liên hệ) và quản lý tiến độ chung của nhóm.</p>
        <button type="button" class="nut-thao-tac nut-thao-tac--xem nut-lien-he-ca-nhan">Kết nối với tôi</button>
      </article>
    </section>

    <!-- CỘT PHẢI: 2 CHỨC NĂNG ĐỘNG (SỔ LƯU BÚT MYSQL + KHẢO SÁT) -->
    <div class="bao-cot bao-cot--phai">
      
      <!-- ================================================================= -->
      <!-- CHỨC NĂNG CSDL 1 & 2: SỔ LƯU BÚT PHÂN TRANG & PHÂN QUYỀN CHỦ TRANG -->
      <!-- ================================================================= -->
      <section class="bieu-mau bao-the-luu-but">
        <div class="bao-the-luu-but__dau">
            <h2 class="bao-the-luu-but__tieu-de">Sổ Lưu Bút Khách Thăm</h2>
            <span class="bao-huy-hieu-tong">
                Tổng: <?= $tongLoiNhan ?> lời nhắn
            </span>
        </div>
        <p class="bao-chu-thich-nho">
            Dữ liệu lưu trữ trực tiếp vào CSDL MySQL (bảng <code>tv_luu_but</code>).
        </p>

        <!-- Thông báo Flash Sổ lưu bút -->
        <?php if ($flashLuuBut): ?>
            <div role="alert" class="bao-flash bao-flash--ok">
                <?= e($flashLuuBut) ?>
            </div>
        <?php endif; ?>
        <?php if ($flashLuuButLoi): ?>
            <div role="alert" class="bao-flash bao-flash--loi">
                <?= e($flashLuuButLoi) ?>
            </div>
        <?php endif; ?>

        <!-- Form gửi lời nhắn lưu bút -->
        <form method="POST" action="gioithieu.php" novalidate>
            <?= csrfField() ?>
            <input type="hidden" name="action" value="luu_but">
            
            <div class="bao-nhom-o">
                <label for="ten_luubut" class="bao-nhan">
                    Họ và tên của bạn: <span class="bao-dau-sao">*</span>
                </label>
                <input type="text" id="ten_luubut" name="ten" 
                       class="bao-o-nhap <?= isset($loiLuuBut['ten']) ? 'bao-o-nhap--loi' : '' ?>" 
                       value="<?= e($duLuuBut['ten']) ?>" required maxlength="50">
                <?php if (isset($loiLuuBut['ten'])): ?>
                    <span class="bao-loi-o">
                        <?= e($loiLuuBut['ten']) ?>
                    </span>
                <?php endif; ?>
            </div>

            <div class="bao-nhom-o--cuoi">
                <label for="loi_nhan_luubut" class="bao-nhan">
                    Lời nhắn lưu bút: <span class="bao-dau-sao">*</span>
                </label>
                <textarea id="loi_nhan_luubut" name="loi_nhan" rows="3" 
                          class="bao-o-nhap <?= isset($loiLuuBut['loi_nhan']) ? 'bao-o-nhap--loi' : '' ?>" 
                          required maxlength="500"><?= e($duLuuBut['loi_nhan']) ?></textarea>
                <?php if (isset($loiLuuBut['loi_nhan'])): ?>
                    <span class="bao-loi-o">
                        <?= e($loiLuuBut['loi_nhan']) ?>
                    </span>
                <?php endif; ?>
            </div>

            <button type="submit" class="nut-thao-tac nut-thao-tac--sua bao-nut-day">
                Gửi lời nhắn lưu bút
            </button>
        </form>

        <hr class="bao-vach-ngan">

        <!-- Danh sách lời nhắn phân trang -->
        <h3 class="bao-tieu-de-phu">
            Danh sách lời nhắn (Trang <?= $trangHienTaiLB ?> / <?= $soTrang ?>):
        </h3>

        <?php if (empty($danhSachLuuBut)): ?>
            <p class="bao-trong-luu-but">Chưa có lời nhắn nào.</p>
        <?php else: ?>
            <ul class="bao-ds-luu-but">
                <?php foreach ($danhSachLuuBut as $lb): ?>
                    <li class="bao-muc-luu-but">
                        <div class="bao-muc-luu-but__dau">
                            <div>
                                <strong class="bao-muc-luu-but__ten"><?= e($lb['ten_khach']) ?></strong>
                                <span class="bao-muc-luu-but__gio">
                                    (<?= date('d/m/Y H:i', strtotime($lb['thoi_gian'])) ?>)
                                </span>
                            </div>

                            <!-- CHỨC NĂNG 2: NÚT XÓA CHỈ HIỆN KHI LÀ CHỦ TRANG -->
                            <?php if ($laChuTrang): ?>
                                <form method="POST" action="gioithieu.php" data-confirm="Bạn có chắc chắn muốn xóa lời nhắn này của khách?" class="bao-form-xoa">
                                    <?= csrfField() ?>
                                    <input type="hidden" name="action" value="xoa_luu_but">
                                    <input type="hidden" name="id" value="<?= (int)$lb['id'] ?>">
                                    <button type="submit" class="bao-nut-xoa" title="Chủ trang xóa lời nhắn này">
                                        Xóa
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                        <p class="bao-muc-luu-but__noi-dung">
                            <?= nl2br(e($lb['loi_nhan'])) ?>
                        </p>
                    </li>
                <?php endforeach; ?>
            </ul>

            <!-- ĐIỀU HƯỚNG PHÂN TRANG (PAGINATION) -->
            <?php if ($soTrang > 1): ?>
                <nav aria-label="Phân trang lưu bút" class="bao-phan-trang">
                    <?php if ($trangHienTaiLB > 1): ?>
                        <a href="gioithieu.php?trang=<?= $trangHienTaiLB - 1 ?>" 
                           class="nut-thao-tac bao-phan-trang__nut">
                            &laquo; Trước
                        </a>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $soTrang; $i++): ?>
                        <a href="gioithieu.php?trang=<?= $i ?>" 
                           class="bao-phan-trang__nut <?= $i === $trangHienTaiLB ? 'bao-phan-trang__nut--hien-tai' : '' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>

                    <?php if ($trangHienTaiLB < $soTrang): ?>
                        <a href="gioithieu.php?trang=<?= $trangHienTaiLB + 1 ?>" 
                           class="nut-thao-tac bao-phan-trang__nut">
                            Sau &raquo;
                        </a>
                    <?php endif; ?>
                </nav>
            <?php endif; ?>
        <?php endif; ?>
      </section>

      <!-- ================================================================= -->
      <!-- CHỨC NĂNG CSDL 2: KHẢO SÁT CÔNG NGHỆ YÊU THÍCH (MYSQL tv_binh_chon) -->
      <!-- ================================================================= -->
      <section class="bieu-mau bao-the-binh-chon">
        <h2 class="bao-the-binh-chon__tieu-de">Khảo sát Công Nghệ Yêu Thích</h2>
        <p class="bao-the-binh-chon__mo-ta">Ngôn ngữ lập trình bạn yêu thích nhất trong các môn Tin học? (Lưu vào bảng <code>tv_binh_chon</code>)</p>

        <?php if ($flashBC): ?>
            <div role="alert" class="bao-flash bao-flash--ok">
                <?= e($flashBC) ?>
            </div>
        <?php endif; ?>
        <?php if ($flashBCLoi): ?>
            <div role="alert" class="bao-flash bao-flash--loi">
                <?= e($flashBCLoi) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="gioithieu.php">
            <?= csrfField() ?>
            <input type="hidden" name="action" value="binh_chon">
            <div class="bao-danh-sach-tuy-chon">
                <?php foreach ($cacTuyChonBC as $tc): ?>
                    <label class="bao-tuy-chon-radio">
                        <input type="radio" name="ngon_ngu" value="<?= e($tc) ?>" required> <?= e($tc) ?>
                    </label>
                <?php endforeach; ?>
            </div>
            <button type="submit" class="nut-thao-tac nut-thao-tac--xem bao-nut-day">
                Gửi bình chọn
            </button>
        </form>

        <hr class="bao-vach-ngan bao-vach-ngan--xanh">

        <h3 class="bao-tieu-de-ket-qua">
            Kết quả bình chọn (<?= $tongPhieuBC ?> phiếu):
        </h3>
        <?php if ($tongPhieuBC > 0): ?>
            <div class="bao-ds-ket-qua">
                <?php foreach ($cacTuyChonBC as $tc): 
                    $soPhieu  = $ketQuaBinhChon[$tc] ?? 0;
                    $phanTram = $tongPhieuBC > 0 ? round(($soPhieu / $tongPhieuBC) * 100) : 0;
                ?>
                <div>
                    <div class="bao-hang-ket-qua">
                        <strong><?= e($tc) ?></strong>
                        <span><?= $phanTram ?>% (<?= $soPhieu ?> phiếu)</span>
                    </div>
                    <progress class="khung-tien-do-bc__thanh" value="<?= $phanTram ?>" max="100"><?= $phanTram ?>%</progress>
                </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="bao-trong-binh-chon">Chưa có lượt bình chọn nào.</p>
        <?php endif; ?>
      </section>

    </div>
  </div>

  <!-- BẢNG THỜI KHÓA BIỂU CÁ NHÂN -->
  <section class="thong-tin">
    <h2>Thời khóa biểu cá nhân</h2>
    <div class="khung-cuon-bang">
      <table class="bang-hoc-lieu bao-bang-tkb">
        <caption class="bang-hoc-lieu__chu-thich">Kế hoạch học tập và làm việc trong tuần của Lê Hoàng Bảo</caption>
        <thead>
          <tr>
            <th scope="col">Thứ</th>
            <th scope="col">Sáng</th>
            <th scope="col">Chiều</th>
            <th scope="col">Tối</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <th scope="row">Thứ Hai</th>
            <td>Học Thiết kế Web trên trường</td>
            <td>Tự học PHP &amp; MySQL PDO</td>
            <td>Dạy kèm tin học THPT</td>
          </tr>
          <tr>
            <th scope="row">Thứ Ba</th>
            <td>Nghiên cứu tài liệu GDPT 2018</td>
            <td>Học Phương pháp giảng dạy Tin học</td>
            <td>Họp tiến độ Nhóm 02</td>
          </tr>
          <tr>
            <th scope="row">Thứ Tư</th>
            <td>Học CSDL nâng cao trên trường</td>
            <td>Review code và kiểm thử W3C</td>
            <td>Lập trình hoàn thiện hệ thống</td>
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
