<?php
/**
 * dong-gop-tai-lieu.php — Biểu mẫu đóng góp học liệu (upload file)
 */
require_once 'inc/config.php';

$loi       = [];
$thanh_cong = false;

// Xử lý POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ho_ten = trim($_POST['hoten'] ?? '');
    $email  = trim($_POST['email'] ?? '');
    $sdt    = trim($_POST['sdt']   ?? '');

    // Validate các trường text
    if (mb_strlen($ho_ten, 'UTF-8') < 5) {
        $loi['hoten'] = 'Họ và tên phải có ít nhất 5 ký tự.';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $loi['email'] = 'Email không hợp lệ.';
    }
    if (!preg_match('/^(84|0[35789])[0-9]{8}$/', $sdt)) {
        $loi['sdt'] = 'Số điện thoại Việt Nam không hợp lệ.';
    }

    // Validate file upload
    $file = $_FILES['tailieu'] ?? null;
    if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
        $loi['tailieu'] = 'Vui lòng chọn file để tải lên.';
    } else {
        $duoi_hop_le = ['pdf', 'doc', 'docx'];
        $duoi_file   = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $kich_thuoc_mb = $file['size'] / 1024 / 1024;

        if (!in_array($duoi_file, $duoi_hop_le, true)) {
            $loi['tailieu'] = 'Chỉ chấp nhận file .pdf, .doc, .docx.';
        } elseif ($kich_thuoc_mb > 5) {
            $loi['tailieu'] = 'File quá lớn (tối đa 5 MB).';
        }
    }

    // Lưu file nếu không có lỗi
    if (empty($loi)) {
        // Tạo tên file duy nhất để tránh ghi đè
        $ten_file_moi = time() . '_' . preg_replace('/[^a-z0-9._-]/i', '_', $file['name']);
        $duong_dan_luu = UPLOADS_DIR . $ten_file_moi;

        if (move_uploaded_file($file['tmp_name'], $duong_dan_luu)) {
            $thanh_cong = true;
            $ho_ten = $email = $sdt = '';
        } else {
            $loi['tailieu'] = 'Không thể lưu file. Vui lòng thử lại.';
        }
    }
}

$tieu_de_trang  = 'Đóng góp tài liệu';
$meta_mo_ta     = 'Gửi biểu mẫu đóng góp tài liệu hoặc liên hệ hợp tác với ban quản trị website.';
$trang_hien_tai = 'dong-gop-tai-lieu';

require_once 'inc/header.php';
?>

    <main class="trang__chinh phan-lien-he">
        <h1 class="phan-lien-he__tieu-de">Biểu mẫu đóng góp học liệu</h1>

        <?php if ($thanh_cong): ?>
            <div role="alert" style="background: #e8f5e9; color: #2e7d32; padding: 1rem; border-radius: 6px; text-align: center; margin-bottom: 1.5rem; max-width: 600px; margin-inline: auto;">
                ✅ Cảm ơn bạn đã đóng góp! Tài liệu của bạn đã được nhận và sẽ được xem xét.
            </div>
        <?php endif; ?>

        <form class="bieu-mau" action="dong-gop-tai-lieu.php" method="POST"
              enctype="multipart/form-data">
            <fieldset class="bieu-mau__nhom">
                <legend class="bieu-mau__tieu-de">Thông tin người gửi tài liệu</legend>

                <p class="bieu-mau__dong">
                    <label class="bieu-mau__nhan" for="hoten">Họ và tên (bắt buộc):</label>
                    <input class="bieu-mau__o-nhap" type="text" id="hoten" name="hoten"
                           value="<?= e($ho_ten ?? '') ?>" required minlength="5">
                    <?php if (isset($loi['hoten'])): ?>
                        <span style="color:red; font-size:0.9rem;"><?= e($loi['hoten']) ?></span>
                    <?php endif; ?>
                </p>

                <p class="bieu-mau__dong">
                    <label class="bieu-mau__nhan" for="email">Địa chỉ Email:</label>
                    <input class="bieu-mau__o-nhap" type="email" id="email" name="email"
                           value="<?= e($email ?? '') ?>" required>
                    <?php if (isset($loi['email'])): ?>
                        <span style="color:red; font-size:0.9rem;"><?= e($loi['email']) ?></span>
                    <?php endif; ?>
                </p>

                <p class="bieu-mau__dong">
                    <label class="bieu-mau__nhan" for="sdt">Số điện thoại liên hệ:</label>
                    <input class="bieu-mau__o-nhap" type="tel" id="sdt" name="sdt"
                           value="<?= e($sdt ?? '') ?>"
                           pattern="(84|0[35789])+([0-9]{8})"
                           title="Vui lòng nhập số điện thoại Việt Nam hợp lệ">
                    <?php if (isset($loi['sdt'])): ?>
                        <span style="color:red; font-size:0.9rem;"><?= e($loi['sdt']) ?></span>
                    <?php endif; ?>
                </p>

                <p class="bieu-mau__dong">
                    <label class="bieu-mau__nhan" for="tailieu">Tải lên tệp giáo án (.pdf, .docx) — tối đa 5 MB:</label>
                    <input class="bieu-mau__o-nhap" type="file" id="tailieu" name="tailieu"
                           accept=".pdf,.doc,.docx" required>
                    <?php if (isset($loi['tailieu'])): ?>
                        <span style="color:red; font-size:0.9rem;"><?= e($loi['tailieu']) ?></span>
                    <?php endif; ?>
                </p>

                <p class="bieu-mau__dong">
                    <button class="bieu-mau__nut-bam" type="submit">Gửi thông tin đóng góp</button>
                </p>
            </fieldset>
        </form>

        <section class="thong-tin">
            <h2 class="thong-tin__tieu-de">Thông tin liên hệ</h2>
            <p class="thong-tin__dong">Email: contact@itedushare.com</p>
        </section>
    </main>

<?php require_once 'inc/footer.php'; ?>

    <script type="module" src="js/main.js"></script>
</body>
</html>
