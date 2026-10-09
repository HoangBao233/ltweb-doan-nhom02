<?php
/**
 * ve-chung-toi.php — Giới thiệu nhóm + Form liên hệ
 */
require_once 'inc/config.php';

use App\Data\KhoLienHe;

// 1. Khởi tạo biến dữ liệu và lỗi
$du = [
    'hoten'   => '',
    'email'   => '',
    'sdt'     => '',
    'noidung' => ''
];
$loi = [];

// 2. Kiểm tra REQUEST_METHOD === 'POST'
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $du['hoten']   = trim($_POST['hoten']   ?? $_POST['ho-ten']   ?? '');
    $du['email']   = trim($_POST['email']   ?? '');
    $du['sdt']     = trim($_POST['sdt']     ?? '');
    $du['noidung'] = trim($_POST['noidung'] ?? $_POST['tin-nhan'] ?? '');

    // 3. Validate từng ô
    if (mb_strlen($du['hoten'], 'UTF-8') < 2) {
        $loi['hoten'] = 'Họ tên phải có ít nhất 2 ký tự.';
    }

    if (!filter_var($du['email'], FILTER_VALIDATE_EMAIL)) {
        $loi['email'] = 'Email không hợp lệ.';
    }

    if (empty($du['sdt'])) {
        $loi['sdt'] = 'Vui lòng nhập số điện thoại.';
    } elseif (!preg_match('/^[0-9]{9,11}$/', $du['sdt'])) {
        $loi['sdt'] = 'Số điện thoại không hợp lệ (cần từ 9 đến 11 chữ số).';
    }

    if (mb_strlen($du['noidung'], 'UTF-8') < 10) {
        $loi['noidung'] = 'Nội dung phải có ít nhất 10 ký tự.';
    }

    // 4. Upload ảnh: kiểm tra $_FILES['anh']['error'], dung lượng <= 2MB, mime finfo_file
    $ten_anh_uploaded = null;
    if (isset($_FILES['anh']) && $_FILES['anh']['error'] !== UPLOAD_ERR_NO_FILE) {
        if ($_FILES['anh']['error'] !== UPLOAD_ERR_OK) {
            $loi['anh'] = 'Lỗi khi tải ảnh lên (Mã lỗi: ' . $_FILES['anh']['error'] . ').';
        } elseif ($_FILES['anh']['size'] > 2 * 1024 * 1024) { // ≤ 2MB
            $loi['anh'] = 'Kích thước ảnh không được vượt quá 2MB.';
        } else {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $_FILES['anh']['tmp_name']);
            finfo_close($finfo);

            $cho_phep_mime = [
                'image/jpeg' => 'jpg',
                'image/png'  => 'png',
                'image/webp' => 'webp'
            ];

            if (!array_key_exists($mime, $cho_phep_mime)) {
                $loi['anh'] = 'Định dạng ảnh không hợp lệ (chỉ chấp nhận JPG, PNG, WEBP).';
            } else {
                $ext = $cho_phep_mime[$mime];
                $ten_file_moi = uniqid('lh_', true) . '.' . $ext;
                
                $uploadDir = UPLOADS_DIR;
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                $duong_dan_dich = $uploadDir . $ten_file_moi;

                if (move_uploaded_file($_FILES['anh']['tmp_name'], $duong_dan_dich)) {
                    $ten_anh_uploaded = $ten_file_moi;
                } else {
                    $loi['anh'] = 'Không thể lưu tệp tải lên.';
                }
            }
        }
    }

    // 5. Nếu không có lỗi: lưu vào storage/lien-he.jsonl, gán $_SESSION['flash'], redirect PRG
    if (empty($loi)) {
        if (!is_dir(STORAGE_DIR)) {
            mkdir(STORAGE_DIR, 0777, true);
        }

        $khoLienHe = new KhoLienHe(STORAGE_DIR . 'lien-he.jsonl');
        $khoLienHe->them([
            'hoten'    => $du['hoten'],
            'email'    => $du['email'],
            'sdt'      => $du['sdt'],
            'noidung'  => $du['noidung'],
            'anh'      => $ten_anh_uploaded,
            'ngay_tao' => date('Y-m-d H:i:s')
        ]);

        $_SESSION['flash'] = [
            'type'    => 'success',
            'message' => '✅ Cảm ơn bạn! Tin nhắn của bạn đã được ghi nhận.'
        ];

        header('Location: ve-chung-toi.php');
        exit;
    }
}

// Đọc và xóa $_SESSION['flash'] ngay đầu phần hiển thị
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

$tieu_de_trang  = 'Về chúng tôi';
$meta_mo_ta     = 'Thông tin liên hệ và danh sách các thành viên phát triển nền tảng ITeduShare.';
$trang_hien_tai = 've-chung-toi';

require_once 'inc/header.php';
?>

    <main class="trang__chinh">
        <h1 class="can-giua-chu">Đội ngũ phát triển dự án</h1>

        <h2 class="can-giua-chu">Danh sách thành viên</h2>
        <p class="doan-van-dai can-giua-chu">Nền tảng được xây dựng và vận hành bởi sinh viên chuyên ngành Sư phạm Tin học và Công nghệ thông tin.</p>

        <ul class="danh-sach-thanh-vien">
            <li>
                <a href="thanhvien/3120124003_bao/gioithieu.php">Lê Hoàng Bảo</a>
                <p>Captain</p>
            </li>
            <li><a href="thanhvien/3120124027_tan/gioithieu.php">Nguyễn Cảnh Tấn</a></li>
            <li><a href="thanhvien/3120124006_chau/gioithieu.php">Lê Thị Xuân Châu</a></li>
            <li><a href="thanhvien/3120222126_thinh/gioithieu.php">Nguyễn Tiến Thịnh</a></li>
        </ul>

        <!-- Form Liên hệ -->
        <section class="phan-lien-he" style="max-width: 600px; margin: 2rem auto; padding: 1.5rem; background: var(--nen-phu, #f9f9f9); border: 1px solid var(--vien, #ddd); border-radius: 8px;">
            <h2 class="can-giua-chu">Liên hệ với nhóm phát triển</h2>

            <?php if ($flash): ?>
                <div role="alert" style="background: #e8f5e9; color: #2e7d32; padding: 1rem; border-radius: 6px; text-align: center; margin-bottom: 1rem;">
                    <?= e($flash['message']) ?>
                </div>
            <?php endif; ?>

            <form id="form-lien-he" class="bieu-mau"
                  action="ve-chung-toi.php" method="POST" enctype="multipart/form-data" novalidate>

                <div class="bieu-mau__nhom">
                    <label for="hoten">Họ và tên <span class="bat-buoc">*</span></label>
                    <input type="text" id="hoten" name="hoten"
                           class="bieu-mau__o-nhap"
                           style="width: 100%; padding: 0.5rem; border: 1px solid <?= isset($loi['hoten']) ? 'red' : '#ccc' ?>; border-radius: 4px;"
                           value="<?= e($du['hoten']) ?>"
                           required minlength="2">
                    <span class="bieu-mau__loi" style="color: red; font-size: 0.9rem; margin-top: 0.25rem; display: block;"><?= e($loi['hoten'] ?? '') ?></span>
                </div>

                <div class="bieu-mau__nhom" style="margin-top: 1rem;">
                    <label for="email">Email <span class="bat-buoc">*</span></label>
                    <input type="email" id="email" name="email"
                           class="bieu-mau__o-nhap"
                           style="width: 100%; padding: 0.5rem; border: 1px solid <?= isset($loi['email']) ? 'red' : '#ccc' ?>; border-radius: 4px;"
                           value="<?= e($du['email']) ?>"
                           required>
                    <span class="bieu-mau__loi" style="color: red; font-size: 0.9rem; margin-top: 0.25rem; display: block;"><?= e($loi['email'] ?? '') ?></span>
                </div>

                <div class="bieu-mau__nhom" style="margin-top: 1rem;">
                    <label for="sdt">Số điện thoại <span class="bat-buoc">*</span></label>
                    <input type="tel" id="sdt" name="sdt"
                           class="bieu-mau__o-nhap"
                           style="width: 100%; padding: 0.5rem; border: 1px solid <?= isset($loi['sdt']) ? 'red' : '#ccc' ?>; border-radius: 4px;"
                           value="<?= e($du['sdt']) ?>"
                           required pattern="[0-9]{9,11}">
                    <span class="bieu-mau__loi" style="color: red; font-size: 0.9rem; margin-top: 0.25rem; display: block;"><?= e($loi['sdt'] ?? '') ?></span>
                </div>

                <div class="bieu-mau__nhom" style="margin-top: 1rem;">
                    <label for="noidung">Tin nhắn / Nội dung <span class="bat-buoc">*</span></label>
                    <textarea id="noidung" name="noidung"
                              class="bieu-mau__o-nhap" rows="4"
                              style="width: 100%; padding: 0.5rem; border: 1px solid <?= isset($loi['noidung']) ? 'red' : '#ccc' ?>; border-radius: 4px;"
                              required minlength="10"><?= e($du['noidung']) ?></textarea>
                    <span class="bieu-mau__loi" style="color: red; font-size: 0.9rem; margin-top: 0.25rem; display: block;"><?= e($loi['noidung'] ?? '') ?></span>
                </div>

                <div class="bieu-mau__nhom" style="margin-top: 1rem;">
                    <label for="anh">Hình ảnh đính kèm (nếu có)</label>
                    <input type="file" id="anh" name="anh" accept="image/jpeg,image/png,image/webp"
                           class="bieu-mau__o-nhap"
                           style="width: 100%; padding: 0.5rem; border: 1px solid <?= isset($loi['anh']) ? 'red' : '#ccc' ?>; border-radius: 4px;">
                    <span class="bieu-mau__loi" style="color: red; font-size: 0.9rem; margin-top: 0.25rem; display: block;"><?= e($loi['anh'] ?? '') ?></span>
                </div>

                <button type="submit" id="nut-gui" class="nut-bam"
                        style="margin-top: 1.5rem; width: 100%; padding: 0.75rem; background: var(--mau-chinh, #004d40); color: white; border: none; border-radius: 4px; font-weight: bold; cursor: pointer;">
                    Gửi tin nhắn
                </button>
            </form>
        </section>
    </main>

<?php require_once 'inc/footer.php'; ?>

    <script type="module" src="js/main.js"></script>
    <script type="module" src="js/trang-lien-he.js"></script>
</body>
</html>
