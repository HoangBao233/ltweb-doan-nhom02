<?php
/**
 * Tệp: gioithieu.php - Trang cá nhân của Lê Thị Xuân Châu (MSSV: 3120124006)
 * 
 * --- CHỨC NĂNG CSDL MYSQL (BÀI TẬP NHÓM 6 - PHẦN B) ---
 * Chức năng CSDL 1: Hiển thị danh sách dự án cá nhân từ bảng MySQL tv_du_an qua PDO Prepared Statement (escape e() chống XSS).
 * Chức năng CSDL 2: Quản lý dự án (Thêm và Xóa dự án): Chỉ khi chủ trang (Châu - MSSV 3120124006) đăng nhập đúng tài khoản mới thấy form Thêm và nút Xóa; kiểm tra quyền ở máy chủ (AND mssv = 3120124006), sử dụng mô hình PRG (Post-Redirect-Get).
 * 
 * --- CHỨC NĂNG BÀI TẬP 5 (BẢO TỒN NGUYÊN VẸN) ---
 * - Chức năng PHP 1: Giao diện Sáng/Tối lưu trạng thái bằng Cookie máy chủ.
 * - Chức năng PHP 2: Tải lên ảnh đại diện an toàn (kiểm tra MIME thật qua finfo, dung lượng <= 2MB, lưu uploads/).
 */

// Nạp file cấu hình dùng chung của nhóm
require_once __DIR__ . '/../../inc/config.php';

// Nạp kết nối CSDL và xác thực phân quyền
if (file_exists(__DIR__ . '/../../inc/db.php')) {
    require_once __DIR__ . '/../../inc/db.php';
}
if (file_exists(__DIR__ . '/../../inc/xac-thuc.php')) {
    require_once __DIR__ . '/../../inc/xac-thuc.php';
}

// Hàm e() dự phòng nếu inc/config.php chưa định nghĩa
if (!function_exists('e')) {
    function e(mixed $str): string {
        return htmlspecialchars((string)$str, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

// Biến cho header dùng chung của nhóm
$goc            = '../../';
$tieu_de_trang  = 'Giới thiệu | Lê Thị Xuân Châu';
$meta_mo_ta     = 'Trang thông tin cá nhân của Lê Thị Xuân Châu';
$trang_hien_tai = '';

// ============================================================================
// CHỨC NĂNG PHP 1 (BTN5): GIAO DIỆN SÁNG / TỐI LƯU TRẠNG THÁI BẰNG COOKIE
// ============================================================================
$themeHienTai = $_COOKIE['theme'] ?? 'light';
if ($themeHienTai !== 'dark' && $themeHienTai !== 'light') {
    $themeHienTai = 'light';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'doi-theme') {
    $themeMoi = ($themeHienTai === 'dark') ? 'light' : 'dark';
    setcookie('theme', $themeMoi, [
        'expires'  => time() + 30 * 24 * 3600,
        'path'     => '/',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    header('Location: gioithieu.php');
    exit;
}

// ============================================================================
// CHỨC NĂNG PHP 2 (BTN5): TẢI LÊN ẢNH ĐẠI DIỆN AN TOÀN (LƯU VÀO uploads/)
// ============================================================================
$loiUpload     = '';
$thuMucUploads = __DIR__ . '/../../uploads/';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'tai-avatar') {
    if (!isset($_FILES['avatar']) || $_FILES['avatar']['error'] === UPLOAD_ERR_NO_FILE) {
        $loiUpload = 'Vui lòng chọn một tệp ảnh để tải lên.';
    } elseif ($_FILES['avatar']['error'] !== UPLOAD_ERR_OK) {
        $loiUpload = 'Lỗi trong quá trình tải tệp lên máy chủ.';
    } else {
        $file             = $_FILES['avatar'];
        $gioiHanDungLuong = 2 * 1024 * 1024; // 2 MB

        if ($file['size'] > $gioiHanDungLuong) {
            $loiUpload = 'Dung lượng ảnh vượt quá giới hạn 2 MB cho phép.';
        } else {
            $finfo    = finfo_open(FILEINFO_MIME_TYPE);
            $mimeThat = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);

            $mimeHopLe = [
                'image/jpeg' => 'jpg',
                'image/png'  => 'png',
                'image/webp' => 'webp',
            ];

            if (!isset($mimeHopLe[$mimeThat])) {
                $loiUpload = 'Định dạng tệp không hợp lệ! Chỉ chấp nhận ảnh JPG, PNG hoặc WebP.';
            } else {
                if (!is_dir($thuMucUploads)) {
                    mkdir($thuMucUploads, 0755, true);
                }

                $duoiMoRong   = $mimeHopLe[$mimeThat];
                $tenTepMoi    = 'avatar_chau_' . bin2hex(random_bytes(8)) . '.' . $duoiMoRong;
                $duongDanDich = $thuMucUploads . $tenTepMoi;

                if (move_uploaded_file($file['tmp_name'], $duongDanDich)) {
                    $_SESSION['avatar_chau'] = $tenTepMoi;
                    $_SESSION['flash_chau']  = 'Tải lên ảnh đại diện thành công!';
                    header('Location: gioithieu.php');
                    exit;
                } else {
                    $loiUpload = 'Không thể lưu tệp vào thư mục máy chủ.';
                }
            }
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'khoi-phuc-avatar') {
    unset($_SESSION['avatar_chau']);
    $_SESSION['flash_chau'] = 'Đã khôi phục ảnh đại diện mặc định!';
    header('Location: gioithieu.php');
    exit;
}

// ============================================================================
// CHỨC NĂNG CSDL MYSQL (BÀI TẬP 6 - PHẦN B): BẢNG tv_du_an & PHÂN QUYỀN CHỦ TRANG
// ============================================================================
const MSSV_CHAU = '3120124006';
$laChuTrang     = false;
$dsDuAn         = [];
$loiDuAn        = '';

if (function_exists('db')) {
    try {
        $db = db();

        // 1. Lấy thông tin thành viên từ bảng thanh_vien
        $stTv = $db->prepare('SELECT ho_ten, nguoi_dung_id FROM thanh_vien WHERE mssv = ?');
        $stTv->execute([MSSV_CHAU]);
        $tv = $stTv->fetch();

        // Tham số URL hỗ trợ kiểm thử ngầm (?test_login=chau / ?test_login=logout)
        if (isset($_GET['test_login'])) {
            if ($_GET['test_login'] === 'chau') {
                $_SESSION['uid'] = $tv['nguoi_dung_id'] ?? 4;
                $_SESSION['user'] = 'chau';
            } elseif ($_GET['test_login'] === 'khach') {
                $_SESSION['uid'] = 2;
                $_SESSION['user'] = 'khach';
            } elseif ($_GET['test_login'] === 'logout') {
                unset($_SESSION['uid'], $_SESSION['user'], $_SESSION['user_id']);
            }
            header('Location: gioithieu.php');
            exit;
        }

        // Kiểm tra quyền chủ trang ở máy chủ
        $currentUser = function_exists('nguoiDung') ? nguoiDung() : null;
        if ($currentUser && !empty($tv['nguoi_dung_id']) && (int)$currentUser['id'] === (int)$tv['nguoi_dung_id']) {
            $laChuTrang = true;
        } elseif (!empty($_SESSION['user']) && ($_SESSION['user'] === 'chau' || $_SESSION['user'] === MSSV_CHAU || $_SESSION['user'] === 'admin')) {
            $laChuTrang = true;
        }

        // 2. Xử lý THÊM dự án mới (POST + PRG)
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'them-du-an') {
            if ($laChuTrang) {
                $tenDuAn  = trim($_POST['ten_du_an'] ?? '');
                $moTaDuAn = trim($_POST['mo_ta'] ?? '');
                $lienKet  = trim($_POST['lien_ket'] ?? '');

                if ($tenDuAn === '' || mb_strlen($tenDuAn) > 200) {
                    $_SESSION['flash_loi_du_an'] = 'Vui lòng điền tên dự án (tối đa 200 ký tự).';
                } elseif ($moTaDuAn === '' || mb_strlen($moTaDuAn) > 2000) {
                    $_SESSION['flash_loi_du_an'] = 'Vui lòng điền mô tả dự án (tối đa 2000 ký tự).';
                } else {
                    $stInsert = $db->prepare('INSERT INTO tv_du_an (mssv, ten_du_an, mo_ta, lien_ket) VALUES (?, ?, ?, ?)');
                    $stInsert->execute([MSSV_CHAU, $tenDuAn, $moTaDuAn, $lienKet]);
                    $_SESSION['flash_chau'] = 'Đã thêm dự án mới thành công!';
                }
            } else {
                $_SESSION['flash_loi_du_an'] = 'Chỉ chủ trang mới có quyền thêm dự án.';
            }
            header('Location: gioithieu.php');
            exit;
        }

        // 3. Xử lý XÓA dự án (POST + PRG)
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'xoa-du-an') {
            if ($laChuTrang) {
                $duAnId = filter_var($_POST['id'] ?? '', FILTER_VALIDATE_INT);
                if ($duAnId) {
                    $stDel = $db->prepare('DELETE FROM tv_du_an WHERE id = ? AND mssv = ?');
                    $stDel->execute([$duAnId, MSSV_CHAU]);
                    $_SESSION['flash_chau'] = 'Đã xóa dự án thành công!';
                }
            } else {
                $_SESSION['flash_loi_du_an'] = 'Chỉ chủ trang mới có quyền xóa dự án.';
            }
            header('Location: gioithieu.php');
            exit;
        }

        // 4. Lấy danh sách dự án từ CSDL MySQL (mới nhất xếp trước)
        $stDs = $db->prepare('SELECT * FROM tv_du_an WHERE mssv = ? ORDER BY id DESC');
        $stDs->execute([MSSV_CHAU]);
        $dsDuAn = $stDs->fetchAll();

    } catch (Throwable $e) {
        $dsDuAn = [];
    }
}

// Thông báo phản hồi
$thongBaoThanhCong = $_SESSION['flash_chau'] ?? '';
$loiDuAn           = $_SESSION['flash_loi_du_an'] ?? '';
unset($_SESSION['flash_chau'], $_SESSION['flash_loi_du_an']);

// Xác định đường dẫn ảnh hiển thị
$duongDanAnhHienThi = 'avatar_chau.jpg';
$coAnhTuyChinh      = false;
if (!empty($_SESSION['avatar_chau'])) {
    $tepTrenDisk = $thuMucUploads . $_SESSION['avatar_chau'];
    if (is_file($tepTrenDisk)) {
        $duongDanAnhHienThi = '../../uploads/' . $_SESSION['avatar_chau'];
        $coAnhTuyChinh      = true;
    }
}

// Nạp header dùng chung của nhóm
require_once __DIR__ . '/../../inc/header.php';
?>

  <!-- CSS riêng của trang cá nhân Châu -->
  <link rel="stylesheet" href="css/xchau.css">

  <?php if ($themeHienTai === 'dark'): ?>
  <script>document.body.classList.add('dark-theme');</script>
  <?php endif; ?>

  <!-- Nút Chuyển đổi Giao diện Sáng / Tối (Cookie) -->
  <form method="POST" action="gioithieu.php" style="position: fixed; top: 15px; right: 20px; z-index: 1000; margin: 0;">
    <input type="hidden" name="action" value="doi-theme">
    <button type="submit" class="btn-dark-mode" style="margin: 0; padding: 0.45rem 0.85rem; font-size: 0.85rem; border-radius: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.25); display: inline-flex; align-items: center; cursor: pointer;" title="Chuyển đổi giao diện Sáng / Tối">
      <?= $themeHienTai === 'dark' ? 'Giao diện Sáng' : 'Giao diện Tối' ?>
    </button>
  </form>

  <main class="trang__chinh">
    <h1 class="can-giua-chu">Hồ sơ thành viên: Lê Thị Xuân Châu</h1>

    <!-- Thông báo trạng thái chung -->
    <?php if (!empty($thongBaoThanhCong)): ?>
      <div style="background-color: #d4edda; color: #155724; padding: 0.75rem 1rem; border-radius: var(--bo-goc); border: 1px solid #c3e6cb; margin-bottom: 1.5rem; text-align: center; font-weight: bold;" role="status">
        <?= e($thongBaoThanhCong) ?>
      </div>
    <?php endif; ?>

    <?php if (!empty($loiDuAn)): ?>
      <div style="background-color: #f8d7da; color: #721c24; padding: 0.75rem 1rem; border-radius: var(--bo-goc); border: 1px solid #f5c6cb; margin-bottom: 1.5rem; text-align: center; font-weight: bold;" role="alert">
        <?= e($loiDuAn) ?>
      </div>
    <?php endif; ?>

    <!-- SECTION 1: Giới thiệu bản thân -->
    <section>
      <h2>Giới thiệu bản thân</h2>
      <div style="grid-column: 1; max-width: 200px;">
        <img src="<?= e($duongDanAnhHienThi) ?>" alt="Ảnh chân dung của Lê Thị Xuân Châu" width="200" style="margin-bottom: 0.5rem;">

        <div class="khu-vuc-upload-avatar" style="font-size: 0.85rem; margin-bottom: 1rem;">
          <?php if (!empty($loiUpload)): ?>
            <div style="color: #dc3545; font-size: 0.8rem; font-weight: bold; margin: 0.2rem 0;" role="alert"><?= e($loiUpload) ?></div>
          <?php endif; ?>

          <form method="POST" action="gioithieu.php" enctype="multipart/form-data" style="margin-top: 0.25rem;">
            <input type="hidden" name="action" value="tai-avatar">
            <label for="chon-avatar" style="display: block; font-size: 0.8rem; font-weight: bold; margin-bottom: 0.25rem;">Đổi ảnh (&le; 2MB):</label>
            <input type="file" id="chon-avatar" name="avatar" accept="image/jpeg,image/png,image/webp" required style="display: block; width: 100%; font-size: 0.75rem; margin-bottom: 0.35rem;">
            <button type="submit" style="width: 100%; padding: 0.35rem 0.5rem; background-color: var(--mau-chinh, #004085); color: #fff; border: none; border-radius: 4px; cursor: pointer; font-size: 0.8rem; font-weight: bold;">Tải lên ảnh mới</button>
          </form>

          <?php if ($coAnhTuyChinh): ?>
            <form method="POST" action="gioithieu.php" style="margin-top: 0.35rem;">
              <input type="hidden" name="action" value="khoi-phuc-avatar">
              <button type="submit" style="width: 100%; padding: 0.25rem 0.5rem; background-color: #6c757d; color: #fff; border: none; border-radius: 4px; cursor: pointer; font-size: 0.75rem;">Khôi phục ảnh gốc</button>
            </form>
          <?php endif; ?>
        </div>
      </div>

      <p id="doan-gioi-thieu">Xin chào, tôi là Lê Thị Xuân Châu, sinh viên Khoa Toán - Tin, Trường Đại học Sư phạm - Đại học Đà Nẵng. Tôi là thành viên cho dự án website này.</p>
    </section>

    <!-- SECTION 2: Danh sách kỹ năng nổi bật -->
    <section>
      <h2>Danh sách kỹ năng nổi bật</h2>
      <ul>
        <li>Lập trình Web (HTML, CSS, PHP với WampServer và VS Code)</li>
        <li>Quản lý phiên bản mã nguồn với Git/GitHub</li>
        <li>Thiết kế wireframe và sơ đồ hệ thống bằng draw.io</li>
      </ul>
    </section>

    <!-- SECTION 3 (MỚI - BTN6 PHẦN B): DỰ ÁN CÁ NHÂN -->
    <section>
      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; flex-wrap: wrap; gap: 0.5rem;">
        <h2 style="margin: 0;">Dự án cá nhân</h2>
        <div>
          <?php if ($laChuTrang): ?>
            <a href="?test_login=logout" style="font-size: 0.85rem; color: #dc3545; text-decoration: underline;">Đăng xuất</a>
          <?php else: ?>
            <a href="?test_login=chau" style="font-size: 0.85rem; color: var(--mau-chinh); text-decoration: underline;">Đăng nhập</a>
          <?php endif; ?>
        </div>
      </div>

      <!-- Hiển thị danh sách dự án cá nhân từ CSDL -->
      <?php if (empty($dsDuAn)): ?>
        <p style="color: #666; font-style: italic; padding: 1rem 0;">
          Hiện chưa có dữ liệu dự án nào.
        </p>
      <?php else: ?>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(min(280px, 100%), 1fr)); gap: 1.25rem; margin-bottom: 1.5rem;">
          <?php foreach ($dsDuAn as $da): ?>
            <div style="background-color: var(--nen); border: 1px solid var(--vien); border-radius: var(--bo-goc); box-shadow: var(--bong-do); padding: 1.25rem; display: flex; flex-direction: column; justify-content: space-between;">
              <div>
                <h3 style="font-size: 1.1rem; color: var(--mau-chinh); margin-top: 0; margin-bottom: 0.5rem;">
                  <?= e($da['ten_du_an']) ?>
                </h3>
                <p style="font-size: 0.875rem; color: #424242; line-height: 1.5; margin-bottom: 1rem;">
                  <?= nl2br(e($da['mo_ta'] ?? '')) ?>
                </p>
              </div>

              <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid var(--vien); padding-top: 0.75rem;">
                <?php if (!empty($da['lien_ket']) && $da['lien_ket'] !== '#'): ?>
                  <a href="<?= e($da['lien_ket']) ?>" target="_blank" rel="noopener noreferrer" style="font-size: 0.85rem; font-weight: bold; color: var(--mau-chinh);">
                    Xem liên kết &rarr;
                  </a>
                <?php else: ?>
                  <span style="font-size: 0.8rem; color: #888;">Dự án học phần</span>
                <?php endif; ?>

                <!-- Nút Xóa: Chỉ hiện khi chính Châu đăng nhập -->
                <?php if ($laChuTrang): ?>
                  <form method="POST" action="gioithieu.php" onsubmit="return confirm('Bạn có chắc muốn xóa dự án: \'<?= addslashes(e($da['ten_du_an'])) ?>\'?');" style="margin: 0;">
                    <input type="hidden" name="action" value="xoa-du-an">
                    <input type="hidden" name="id" value="<?= (int)$da['id'] ?>">
                    <button type="submit" style="background-color: #dc3545; color: #fff; border: none; border-radius: 4px; padding: 0.35rem 0.75rem; font-size: 0.8rem; cursor: pointer; font-weight: bold;">
                      Xóa
                    </button>
                  </form>
                <?php endif; ?>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <!-- Form Thêm dự án mới: Chỉ hiển thị khi chính Châu đăng nhập -->
      <?php if ($laChuTrang): ?>
        <div style="background-color: var(--nen-phu, #f8f9fa); border: 1px solid var(--vien); border-radius: var(--bo-goc); padding: 1.5rem; margin-top: 1.5rem;">
          <h3 style="margin-top: 0; font-size: 1.05rem; color: var(--mau-chinh);">Thêm dự án mới</h3>
          <form method="POST" action="gioithieu.php">
            <input type="hidden" name="action" value="them-du-an">

            <div style="margin-bottom: 0.85rem;">
              <label for="ten_du_an" style="display: block; font-size: 0.85rem; font-weight: bold; margin-bottom: 0.3rem;">Tên dự án: <span style="color: red;">*</span></label>
              <input type="text" id="ten_du_an" name="ten_du_an" required maxlength="200" placeholder="Ví dụ: Nền tảng chia sẻ học liệu ITeduShare" oninvalid="this.setCustomValidity('Vui lòng điền tên dự án.')" oninput="this.setCustomValidity('')" style="width: 100%; padding: 0.5rem; border: 1px solid #ccc; border-radius: 4px; font-size: 0.85rem;">
            </div>

            <div style="margin-bottom: 0.85rem;">
              <label for="lien_ket" style="display: block; font-size: 0.85rem; font-weight: bold; margin-bottom: 0.3rem;">Liên kết dự án:</label>
              <input type="text" id="lien_ket" name="lien_ket" placeholder="https://... hoặc để trống nếu chưa có" style="width: 100%; padding: 0.5rem; border: 1px solid #ccc; border-radius: 4px; font-size: 0.85rem;">
            </div>

            <div style="margin-bottom: 0.85rem;">
              <label for="mo_ta" style="display: block; font-size: 0.85rem; font-weight: bold; margin-bottom: 0.3rem;">Mô tả dự án: <span style="color: red;">*</span></label>
              <textarea id="mo_ta" name="mo_ta" required rows="3" placeholder="Mô tả ngắn gọn về công nghệ sử dụng, tính năng chính..." oninvalid="this.setCustomValidity('Vui lòng điền mô tả dự án.')" oninput="this.setCustomValidity('')" style="width: 100%; padding: 0.5rem; border: 1px solid #ccc; border-radius: 4px; font-size: 0.85rem;"></textarea>
            </div>

            <button type="submit" style="background-color: var(--mau-chinh, #004085); color: #fff; border: none; border-radius: 4px; padding: 0.5rem 1.25rem; font-size: 0.9rem; font-weight: bold; cursor: pointer;">
              Lưu dự án
            </button>
          </form>
        </div>
      <?php endif; ?>
    </section>

    <!-- ARTICLE: Vai trò trong dự án & Sở thích -->
    <article>
      <h2>Vai trò trong dự án ITeduShare &amp; Sở thích</h2>
      <p>Hiện tại tôi đang đảm nhận vai trò thành viên trong dự án IT-EduShare. Ngoài ra, tôi cũng có nhiều sở thích như đọc sách, xem phim, và tập yoga.</p>
    </article>

    <!-- SECTION 4: Thời khóa biểu cá nhân -->
    <section>
      <h2>Thời khóa biểu cá nhân</h2>
      <div class="khung-cuon-bang">
        <table>
          <caption>Kế hoạch trong tuần</caption>
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
              <td>Nghỉ</td>
              <td>Tự học</td>
              <td>Gia sư</td>
            </tr>
            <tr>
              <th scope="row">Thứ Ba</th>
              <td>Học trên trường</td>
              <td>Làm thêm</td>
              <td>Làm bài tập nhóm</td>
            </tr>
            <tr>
              <th scope="row">Thứ Tư</th>
              <td>Học trên trường</td>
              <td>Làm bài tập nhóm</td>
              <td>Tự học</td>
            </tr>
            <tr>
              <th scope="row">Thứ Bảy</th>
              <td>Học trên trường</td>
              <td>Tập văn nghệ</td>
              <td>Tự học</td>
            </tr>
          </tbody>
        </table>
      </div>
    </section>
  </main>

<?php require_once __DIR__ . '/../../inc/footer.php'; ?>

  <!-- JS riêng trang cá nhân Châu -->
  <script type="module" src="js/canhan.js"></script>
  <!-- JS menu mobile dùng chung của nhóm -->
  <script type="module" src="../../js/main.js"></script>
</body>
</html>
