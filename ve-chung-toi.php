<?php
/**
 * ve-chung-toi.php — Giới thiệu nhóm + Form liên hệ
 */
require_once 'inc/config.php';

$loi        = [];
$thanh_cong = false;

// Xử lý POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ho_ten   = trim($_POST['ho-ten']   ?? '');
    $email    = trim($_POST['email']    ?? '');
    $tin_nhan = trim($_POST['tin-nhan'] ?? '');

    // Validate
    if (mb_strlen($ho_ten, 'UTF-8') < 2) {
        $loi['ho-ten'] = 'Họ tên phải có ít nhất 2 ký tự.';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $loi['email'] = 'Email không hợp lệ.';
    }
    if (mb_strlen($tin_nhan, 'UTF-8') < 10) {
        $loi['tin-nhan'] = 'Tin nhắn phải có ít nhất 10 ký tự.';
    }

    if (empty($loi)) {
        $thanh_cong = true;
        $ho_ten = $email = $tin_nhan = '';
    }
}

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

            <?php if ($thanh_cong): ?>
                <div role="alert" style="background: #e8f5e9; color: #2e7d32; padding: 1rem; border-radius: 6px; text-align: center; margin-bottom: 1rem;">
                    ✅ Cảm ơn bạn! Tin nhắn của bạn đã được ghi nhận.
                </div>
            <?php endif; ?>

            <form id="form-lien-he" class="bieu-mau"
                  action="ve-chung-toi.php" method="POST" novalidate>

                <div class="bieu-mau__nhom">
                    <label for="ho-ten">Họ và tên <span class="bat-buoc">*</span></label>
                    <input type="text" id="ho-ten" name="ho-ten"
                           class="bieu-mau__o-nhap"
                           style="width: 100%; padding: 0.5rem; border: 1px solid <?= isset($loi['ho-ten']) ? 'red' : '#ccc' ?>; border-radius: 4px;"
                           value="<?= e($ho_ten ?? '') ?>"
                           required minlength="2">
                    <?php if (isset($loi['ho-ten'])): ?>
                        <span style="color: red; font-size: 0.9rem;"><?= e($loi['ho-ten']) ?></span>
                    <?php endif; ?>
                </div>

                <div class="bieu-mau__nhom" style="margin-top: 1rem;">
                    <label for="email">Email <span class="bat-buoc">*</span></label>
                    <input type="email" id="email" name="email"
                           class="bieu-mau__o-nhap"
                           style="width: 100%; padding: 0.5rem; border: 1px solid <?= isset($loi['email']) ? 'red' : '#ccc' ?>; border-radius: 4px;"
                           value="<?= e($email ?? '') ?>"
                           required>
                    <?php if (isset($loi['email'])): ?>
                        <span style="color: red; font-size: 0.9rem;"><?= e($loi['email']) ?></span>
                    <?php endif; ?>
                </div>

                <div class="bieu-mau__nhom" style="margin-top: 1rem;">
                    <label for="tin-nhan">Tin nhắn <span class="bat-buoc">*</span></label>
                    <textarea id="tin-nhan" name="tin-nhan"
                              class="bieu-mau__o-nhap" rows="4"
                              style="width: 100%; padding: 0.5rem; border: 1px solid <?= isset($loi['tin-nhan']) ? 'red' : '#ccc' ?>; border-radius: 4px;"
                              required minlength="10"><?= e($tin_nhan ?? '') ?></textarea>
                    <?php if (isset($loi['tin-nhan'])): ?>
                        <span style="color: red; font-size: 0.9rem;"><?= e($loi['tin-nhan']) ?></span>
                    <?php endif; ?>
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
</body>
</html>
