<?php
/**
 * Tệp: gioithieu.php - Trang cá nhân của Lê Thị Xuân Châu (MSSV: 3120124006)
 * 
 * --- BẢO MẬT & GIA CỐ (BÀI TẬP NHÓM 7 - PHẦN B) ---
 * - Mã chống CSRF: csrfField() trên tất cả form POST (đổi theme, tải avatar, khôi phục avatar, thêm/xóa dự án).
 * - Làm sạch chuỗi chuoi(): chống lỗi TypeError khi nhận tham số dạng mảng [].
 * - Dọn sạch toàn bộ inline <script>, on*= và inline style= sang css/xchau.css & js/canhan.js (tuân thủ CSP).
 * - Cookie bảo vệ: setcookie('theme', ...) có đủ httponly, samesite=Lax, secure theo HTTPS.
 * - Chống XSS liên kết: kiểm tra URL dự án chỉ chấp nhận scheme http:// hoặc https://.
 * 
 * --- CHỨC NĂNG CSDL MYSQL (BÀI TẬP NHÓM 6 - PHẦN B) ---
 * - Chức năng CSDL 1: Hiển thị danh sách dự án cá nhân từ bảng MySQL tv_du_an qua PDO Prepared Statement (escape e() chống XSS).
 * - Chức năng CSDL 2: Quản lý dự án (Thêm và Xóa dự án): Chỉ khi chủ trang (Châu - MSSV 3120124006) đăng nhập đúng tài khoản mới thấy form Thêm và nút Xóa; kiểm tra quyền ở máy chủ (AND mssv = 3120124006), sử dụng mô hình PRG (Post-Redirect-Get).
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
if (file_exists(__DIR__ . '/../../inc/csrf.php')) {
    require_once __DIR__ . '/../../inc/csrf.php';
}

// Hàm chuoi() làm sạch tham số chuỗi (tránh TypeError khi gửi dạng mảng)
if (!function_exists('chuoi')) {
    function chuoi(mixed $v): string {
        return is_string($v) ? $v : '';
    }
}

// Hàm e() dự phòng nếu inc/config.php chưa định nghĩa
if (!function_exists('e')) {
    function e(mixed $str): string {
        return htmlspecialchars((string)$str, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

// Hàm csrfField() dự phòng
if (!function_exists('csrfField')) {
    function csrfField(): string {
        return '<input type="hidden" name="csrf" value="' . (function_exists('csrfToken') ? csrfToken() : '') . '">';
    }
}

// Biến cho header dùng chung của nhóm
$goc            = '../../';
$tieu_de_trang  = 'Giới thiệu | Lê Thị Xuân Châu';
$meta_mo_ta     = 'Trang thông tin cá nhân của Lê Thị Xuân Châu';
$trang_hien_tai = '';

// ============================================================================
// CHỨC NĂNG PHP 1: GIAO DIỆN SÁNG / TỐI LƯU TRẠNG THÁI BẰNG COOKIE (CÓ SECURE)
// ============================================================================
$themeHienTai = chuoi($_COOKIE['theme'] ?? 'light');
if ($themeHienTai !== 'dark' && $themeHienTai !== 'light') {
    $themeHienTai = 'light';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && chuoi($_POST['action'] ?? '') === 'doi-theme') {
    $themeMoi = ($themeHienTai === 'dark') ? 'light' : 'dark';
    setcookie('theme', $themeMoi, [
        'expires'  => time() + 30 * 24 * 3600,
        'path'     => '/',
        'secure'   => !empty($_SERVER['HTTPS']),
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    header('Location: gioithieu.php');
    exit;
}

// ============================================================================
// CHỨC NĂNG PHP 2: TẢI LÊN ẢNH ĐẠI DIỆN AN TOÀN (LƯU VÀO uploads/)
// ============================================================================
$loiUpload     = '';
$thuMucUploads = __DIR__ . '/../../uploads/';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && chuoi($_POST['action'] ?? '') === 'tai-avatar') {
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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && chuoi($_POST['action'] ?? '') === 'khoi-phuc-avatar') {
    unset($_SESSION['avatar_chau']);
    $_SESSION['flash_chau'] = 'Đã khôi phục ảnh đại diện mặc định!';
    header('Location: gioithieu.php');
    exit;
}

// ============================================================================
// CHỨC NĂNG CSDL MYSQL: BẢNG tv_du_an & PHÂN QUYỀN CHỦ TRANG
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

        // Kiểm tra quyền chủ trang ở máy chủ (chuẩn xác dựa trên uid và CSDL)
        $currentUser = function_exists('nguoiDung') ? nguoiDung() : null;
        $laChuTrang  = ($currentUser !== null) && !empty($tv['nguoi_dung_id']) && ((int)$currentUser['id'] === (int)$tv['nguoi_dung_id']);

        // 2. Xử lý THÊM dự án mới (POST + PRG + Chống XSS javascript: URL)
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && chuoi($_POST['action'] ?? '') === 'them-du-an') {
            if ($laChuTrang) {
                $tenDuAn  = trim(chuoi($_POST['ten_du_an'] ?? ''));
                $moTaDuAn = trim(chuoi($_POST['mo_ta'] ?? ''));
                $lienKet  = trim(chuoi($_POST['lien_ket'] ?? ''));

                if ($lienKet !== '' && !str_starts_with($lienKet, 'http://') && !str_starts_with($lienKet, 'https://')) {
                    $_SESSION['flash_loi_du_an'] = 'Liên kết dự án không an toàn! Chỉ chấp nhận liên kết bắt đầu bằng http:// hoặc https://.';
                } elseif ($tenDuAn === '' || mb_strlen($tenDuAn) > 200) {
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
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && chuoi($_POST['action'] ?? '') === 'xoa-du-an') {
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

  <!-- CSS riêng của trang cá nhân Châu (Đã tích hợp toàn bộ class chống CSP) -->
  <link rel="stylesheet" href="css/xchau.css">

  <!-- Nút Chuyển đổi Giao diện Sáng / Tối (Cookie an toàn, form POST có CSRF) -->
  <form method="POST" action="gioithieu.php" class="form-doi-theme">
    <?= csrfField() ?>
    <input type="hidden" name="action" value="doi-theme">
    <button type="submit" class="btn-dark-mode" title="Chuyển đổi giao diện Sáng / Tối">
      <?= $themeHienTai === 'dark' ? 'Giao diện Sáng' : 'Giao diện Tối' ?>
    </button>
  </form>

  <main class="trang__chinh <?= $themeHienTai === 'dark' ? 'dark-theme' : '' ?>">
    <h1 class="can-giua-chu">Hồ sơ thành viên: Lê Thị Xuân Châu</h1>

    <!-- Thông báo trạng thái chung -->
    <?php if (!empty($thongBaoThanhCong)): ?>
      <div class="thong-bao-thanh-cong" role="status">
        <?= e($thongBaoThanhCong) ?>
      </div>
    <?php endif; ?>

    <?php if (!empty($loiDuAn)): ?>
      <div class="thong-bao-loi" role="alert">
        <?= e($loiDuAn) ?>
      </div>
    <?php endif; ?>

    <!-- SECTION 1: Giới thiệu bản thân -->
    <section>
      <h2>Giới thiệu bản thân</h2>
      <div class="khung-avatar">
        <img src="<?= e($duongDanAnhHienThi) ?>" alt="Ảnh chân dung của Lê Thị Xuân Châu" width="200" class="anh-avatar">

        <div class="khu-vuc-upload-avatar">
          <?php if (!empty($loiUpload)): ?>
            <div class="loi-upload-avatar" role="alert"><?= e($loiUpload) ?></div>
          <?php endif; ?>

          <form method="POST" action="gioithieu.php" enctype="multipart/form-data" class="form-upload-avatar">
            <?= csrfField() ?>
            <input type="hidden" name="action" value="tai-avatar">
            <label for="chon-avatar" class="nhan-chon-avatar">Đổi ảnh (&le; 2MB):</label>
            <input type="file" id="chon-avatar" name="avatar" accept="image/jpeg,image/png,image/webp" required class="input-chon-avatar">
            <button type="submit" class="nut-upload-avatar">Tải lên ảnh mới</button>
          </form>

          <?php if ($coAnhTuyChinh): ?>
            <form method="POST" action="gioithieu.php" class="form-khoi-phuc-avatar">
              <?= csrfField() ?>
              <input type="hidden" name="action" value="khoi-phuc-avatar">
              <button type="submit" class="nut-khoi-phuc-avatar">Khôi phục ảnh gốc</button>
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

    <!-- SECTION 3: DỰ ÁN CÁ NHÂN (MySQL tv_du_an, Chống XSS & CSRF) -->
    <section>
      <div class="thanh-tieu-de-du-an">
        <h2 class="tieu-de-du-an">Dự án cá nhân</h2>
        <div>
          <?php if ($laChuTrang): ?>
            <span class="trang-thai-chu-trang">[Chủ trang: Lê Thị Xuân Châu]</span>
            <a href="<?= $goc ?>dang-xuat.php" class="link-dang-xuat">Đăng xuất</a>
          <?php elseif ($currentUser): ?>
            <span class="trang-thai-khach">Đang đăng nhập: <?= e($currentUser['ho_ten']) ?> (Khách)</span>
            <a href="<?= $goc ?>dang-xuat.php" class="link-dang-xuat">Đăng xuất</a>
          <?php else: ?>
            <a href="<?= $goc ?>dang-nhap.php" class="link-dang-nhap">Đăng nhập chủ trang</a>
          <?php endif; ?>
        </div>
      </div>

      <!-- Hiển thị danh sách dự án cá nhân từ CSDL -->
      <?php if (empty($dsDuAn)): ?>
        <p class="chua-co-du-an">
          Hiện chưa có dữ liệu dự án nào.
        </p>
      <?php else: ?>
        <div class="luoi-du-an">
          <?php foreach ($dsDuAn as $da): ?>
            <div class="the-du-an">
              <div>
                <h3 class="ten-du-an">
                  <?= e($da['ten_du_an']) ?>
                </h3>
                <p class="mo-ta-du-an">
                  <?= nl2br(e($da['mo_ta'] ?? '')) ?>
                </p>
              </div>

              <div class="chan-the-du-an">
                <?php 
                $lk = trim((string)($da['lien_ket'] ?? ''));
                $laUrlAnToan = str_starts_with($lk, 'http://') || str_starts_with($lk, 'https://');
                ?>
                <?php if ($laUrlAnToan): ?>
                  <a href="<?= e($lk) ?>" target="_blank" rel="noopener noreferrer" class="link-xem-du-an">
                    Xem liên kết &rarr;
                  </a>
                <?php else: ?>
                  <span class="nhan-du-an-hoc-phan">Dự án học phần</span>
                <?php endif; ?>

                <!-- Nút Xóa: Chỉ hiện khi chính Châu đăng nhập (bảo vệ CSRF + data-confirm) -->
                <?php if ($laChuTrang): ?>
                  <form method="POST" action="gioithieu.php" data-confirm="Bạn có chắc muốn xóa dự án: '<?= e(addslashes($da['ten_du_an'])) ?>'?" class="form-xoa-du-an">
                    <?= csrfField() ?>
                    <input type="hidden" name="action" value="xoa-du-an">
                    <input type="hidden" name="id" value="<?= (int)$da['id'] ?>">
                    <button type="submit" class="nut-xoa-du-an">
                      Xóa
                    </button>
                  </form>
                <?php endif; ?>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <!-- Form Thêm dự án mới: Chỉ hiển thị khi chính Châu đăng nhập (CSRF + addEventListener validation) -->
      <?php if ($laChuTrang): ?>
        <div class="khung-them-du-an">
          <h3 class="tieu-de-them-du-an">Thêm dự án mới</h3>
          <form method="POST" action="gioithieu.php">
            <?= csrfField() ?>
            <input type="hidden" name="action" value="them-du-an">

            <div class="nhom-nhap-lieu">
              <label for="ten_du_an" class="nhan-nhap-lieu">Tên dự án: <span class="dau-sao-bat-buoc">*</span></label>
              <input type="text" id="ten_du_an" name="ten_du_an" required maxlength="200" placeholder="Ví dụ: Nền tảng chia sẻ học liệu ITeduShare" class="input-du-an">
            </div>

            <div class="nhom-nhap-lieu">
              <label for="lien_ket" class="nhan-nhap-lieu">Liên kết dự án:</label>
              <input type="text" id="lien_ket" name="lien_ket" placeholder="https://... hoặc để trống nếu chưa có" class="input-du-an">
            </div>

            <div class="nhom-nhap-lieu">
              <label for="mo_ta" class="nhan-nhap-lieu">Mô tả dự án: <span class="dau-sao-bat-buoc">*</span></label>
              <textarea id="mo_ta" name="mo_ta" required rows="3" placeholder="Mô tả ngắn gọn về công nghệ sử dụng, tính năng chính..." class="textarea-du-an"></textarea>
            </div>

            <button type="submit" class="nut-luu-du-an">
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
