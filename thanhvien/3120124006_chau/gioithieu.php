<?php
/**
 * Tệp: gioithieu.php - Trang cá nhân của Lê Thị Xuân Châu (MSSV: 3120124006)
 * Chức năng PHP máy chủ 1: Giao diện Sáng/Tối lưu trạng thái bằng Cookie (setcookie, PRG, an toàn khi tắt JS) - Góc phải màn hình
 * Chức năng PHP máy chủ 2: Tải lên ảnh đại diện an toàn (kiểm tra MIME thật qua finfo, dung lượng <= 2MB, tên ngẫu nhiên, lưu uploads/, PRG) - Phía dưới ảnh đại diện
 * Cách thử:
 *   - Bấm nút "Giao diện Sáng/Tối" ở góc phải màn hình để đổi màu và xem cookie 'theme' trong DevTools Application.
 *   - Chọn tệp ảnh tải lên ở phía dưới ảnh đại diện để cập nhật ảnh; thử file giả mạo hoặc file > 2MB để kiểm tra máy chủ báo lỗi.
 */

// Khởi động phiên làm việc (Session) cho Flash message và lưu ảnh
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$goc = '../../';
$tieuDe = 'Giới thiệu | Lê Thị Xuân Châu';
$trang = '';

// Nạp file cấu hình / hàm tiện ích dùng chung nếu có
if (is_file(__DIR__ . '/../../inc/config.php')) {
    require_once __DIR__ . '/../../inc/config.php';
} elseif (is_file(__DIR__ . '/../../inc/ham.php')) {
    require_once __DIR__ . '/../../inc/ham.php';
}

// Hàm thoát HTML an toàn e() chống XSS theo chuẩn Chương 5
if (!function_exists('e')) {
    function e(mixed $str): string {
        return htmlspecialchars((string)$str, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

// ============================================================================
// CHỨC NĂNG PHP 1: GIAO DIỆN SÁNG / TỐI LƯU TRẠNG THÁI BẰNG COOKIE MÁY CHỦ
// ============================================================================
$themeHienTai = $_COOKIE['theme'] ?? 'light';
if ($themeHienTai !== 'dark' && $themeHienTai !== 'light') {
    $themeHienTai = 'light';
}

// Xử lý đổi theme qua POST (Post/Redirect/Get) - gọi TRƯỚC mọi output
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'doi-theme') {
    $themeMoi = ($themeHienTai === 'dark') ? 'light' : 'dark';
    setcookie('theme', $themeMoi, [
        'expires'  => time() + 30 * 24 * 3600, // 30 ngày
        'path'     => '/',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    header('Location: gioithieu.php');
    exit;
}

// ============================================================================
// CHỨC NĂNG PHP 2: TẢI LÊN ẢNH ĐẠI DIỆN AN TOÀN (LƯU VÀO uploads/)
// ============================================================================
$loiUpload = '';
$thuMucUploads = __DIR__ . '/../../uploads/';

// Xử lý submit tải lên ảnh (POST với PRG)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'tai-avatar') {
    if (!isset($_FILES['avatar']) || $_FILES['avatar']['error'] === UPLOAD_ERR_NO_FILE) {
        $loiUpload = 'Vui lòng chọn một tệp ảnh để tải lên.';
    } elseif ($_FILES['avatar']['error'] !== UPLOAD_ERR_OK) {
        $loiUpload = 'Lỗi trong quá trình tải tệp lên máy chủ.';
    } else {
        $file = $_FILES['avatar'];
        $gioiHanDungLuong = 2 * 1024 * 1024; // 2 MB

        // Kiểm tra dung lượng file
        if ($file['size'] > $gioiHanDungLuong) {
            $loiUpload = 'Dung lượng ảnh vượt quá giới hạn 2 MB cho phép.';
        } else {
            // Kiểm tra kiểu MIME thật của tệp bằng finfo (không tin phần mở rộng hay client header)
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
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
                // Tạo thư mục uploads/ nếu chưa tồn tại
                if (!is_dir($thuMucUploads)) {
                    mkdir($thuMucUploads, 0755, true);
                }

                // Đặt tên ngẫu nhiên tránh trùng và ngăn thực thi mã độc
                $duoiMoRong = $mimeHopLe[$mimeThat];
                $tenTepMoi = 'avatar_chau_' . bin2hex(random_bytes(8)) . '.' . $duoiMoRong;
                $duongDanDich = $thuMucUploads . $tenTepMoi;

                if (move_uploaded_file($file['tmp_name'], $duongDanDich)) {
                    $_SESSION['avatar_chau'] = $tenTepMoi;
                    $_SESSION['flash_chau'] = 'Tải lên ảnh đại diện thành công!';
                    header('Location: gioithieu.php');
                    exit;
                } else {
                    $loiUpload = 'Không thể lưu tệp vào thư mục máy chủ.';
                }
            }
        }
    }
}

// Xử lý khôi phục ảnh đại diện mặc định
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'khoi-phuc-avatar') {
    unset($_SESSION['avatar_chau']);
    $_SESSION['flash_chau'] = 'Đã khôi phục ảnh đại diện mặc định!';
    header('Location: gioithieu.php');
    exit;
}

// Lấy thông báo Flash và xóa khỏi phiên
$thongBaoThanhCong = $_SESSION['flash_chau'] ?? '';
unset($_SESSION['flash_chau']);

// Xác định đường dẫn ảnh hiển thị
$duongDanAnhHienThi = 'avatar_chau.jpg';
$coAnhTuyChinh = false;
if (!empty($_SESSION['avatar_chau'])) {
    $tepTrenDisk = $thuMucUploads . $_SESSION['avatar_chau'];
    if (is_file($tepTrenDisk)) {
        $duongDanAnhHienThi = '../../uploads/' . $_SESSION['avatar_chau'];
        $coAnhTuyChinh = true;
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Trang thông tin cá nhân của Lê Thị Xuân Châu">
  <title>Giới thiệu | Lê Thị Xuân Châu</title>

  <!-- Nạp 5 file CSS chung của nhóm để lấy header/footer -->
  <link rel="stylesheet" href="../../css/01-bien.css">
  <link rel="stylesheet" href="../../css/02-chuan-hoa.css">
  <link rel="stylesheet" href="../../css/03-bo-cuc.css">
  <link rel="stylesheet" href="../../css/04-thanh-phan.css">
  <link rel="stylesheet" href="../../css/05-tien-ich.css">
  
  <!-- CSS nội dung bên trong của Châu -->
  <link rel="stylesheet" href="css/xchau.css">
</head>
<body class="trang <?= $themeHienTai === 'dark' ? 'dark-theme' : '' ?>">
  
  <!-- Nút Chức năng PHP 1: Giao diện Sáng / Tối (Cookie máy chủ) - Cố định ở góc phải màn hình -->
  <form method="POST" action="gioithieu.php" style="position: fixed; top: 15px; right: 20px; z-index: 1000; margin: 0;">
    <input type="hidden" name="action" value="doi-theme">
    <button type="submit" class="btn-dark-mode" style="margin: 0; padding: 0.45rem 0.85rem; font-size: 0.85rem; border-radius: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.25); display: inline-flex; align-items: center; gap: 5px; cursor: pointer;" title="Chuyển đổi giao diện Sáng / Tối">
      <?= $themeHienTai === 'dark' ? '☀️ Giao diện Sáng' : '🌙 Giao diện Tối' ?>
    </button>
  </form>

  <!-- Header giống nhóm -->
  <header class="trang__dau can-giua-chu">
    <p class="trang__khieu-giao">ITeduShare - Cùng giáo viên Tin học kiến tạo tương lai</p>
  </header>

  <!-- Nav giống nhóm -->
  <nav class="trang__dieu-huong" aria-label="Menu chính">
    <!-- Nút Menu hiển thị trên Mobile (Mặc định ẩn) -->
    <button type="button" class="nut-menu" aria-expanded="false" aria-controls="menu-chinh">☰ Menu</button>
    
    <ul id="menu-chinh" class="menu-chinh menu">
      <li><a href="../../index.html" class="menu-chinh__lien-ket">Trang chủ</a></li>
      <li><a href="../../kho-hoc-lieu.html" class="menu-chinh__lien-ket">Kho học liệu</a></li>
      <li><a href="../../chi-tiet-giao-an.html" class="menu-chinh__lien-ket">Chi tiết giáo án</a></li>
      <li><a href="../../ve-chung-toi.html" class="menu-chinh__lien-ket">Về chúng tôi</a></li>
      <li><a href="../../dong-gop-tai-lieu.html" class="menu-chinh__lien-ket">Đóng góp tài liệu</a></li>
    </ul>
  </nav>

  <!-- Main chứa nội dung riêng -->
  <main class="trang__chinh">
    <h1 class="can-giua-chu">Hồ sơ thành viên: Lê Thị Xuân Châu</h1>

    <!-- SECTION 1 (Giới thiệu bản thân): Cột 1 là ảnh và form upload; Cột 2 là dòng chữ giới thiệu và câu nói hay -->
    <section>
      <h2>Giới thiệu bản thân</h2>

      <!-- Cột 1 (trái): Ảnh đại diện và form upload ngay bên dưới ảnh -->
      <div style="grid-column: 1; max-width: 200px;">
        <img src="<?= e($duongDanAnhHienThi) ?>" alt="Ảnh chân dung của Lê Thị Xuân Châu" width="200" style="margin-bottom: 0.5rem;">

        <!-- Chức năng PHP 2: Tải lên ảnh đại diện an toàn (ở phía dưới ảnh đại diện) -->
        <div class="khu-vuc-upload-avatar" style="font-size: 0.85rem; margin-bottom: 1rem;">
          <?php if (!empty($thongBaoThanhCong)): ?>
            <div style="color: #28a745; font-size: 0.8rem; font-weight: bold; margin: 0.2rem 0;" role="status" aria-live="polite">✓ <?= e($thongBaoThanhCong) ?></div>
          <?php endif; ?>
          <?php if (!empty($loiUpload)): ?>
            <div style="color: #dc3545; font-size: 0.8rem; font-weight: bold; margin: 0.2rem 0;" role="alert" aria-live="polite">⚠️ <?= e($loiUpload) ?></div>
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

      <!-- Cột 2 (phải): Dòng chữ giới thiệu bản thân - Căn thẳng hàng với đỉnh ảnh đại diện -->
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

    <!-- ARTICLE: Vai trò trong dự án & Sở thích -->
    <article>
      <h2>Vai trò trong dự án ITeduShare &amp; Sở thích</h2>
      <p>Hiện tại tôi đang đảm nhận vai trò thành viên trong dự án IT-EduShare. Ngoài ra, tôi cũng có nhiều sở thích như đọc sách, xem phim, và tập yoga.</p>
    </article>

    <!-- SECTION 3: Thời khóa biểu cá nhân -->
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

  <!-- Footer giống nhóm -->
  <footer class="trang__chan can-giua-chu">
    <p class="trang__ban-quyen">© 2026 Nhóm 02 - Khoa Toán - Tin</p>
  </footer>

  <!-- Kịch bản JS tương tác động riêng của trang cá nhân Châu -->
  <script type="module" src="js/canhan.js"></script>
  
  <!-- Nạp JS cho tính năng Menu Mobile của nhóm -->
  <script type="module" src="../../js/main.js"></script>
</body>
</html>
