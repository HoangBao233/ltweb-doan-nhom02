<?php
/**
 * gio-hang.php — Trang danh sách giáo án đã lưu (Giao diện UI mẫu)
 */
require_once 'inc/config.php';
$tieu_de_trang  = 'Giáo án đã lưu';
$meta_mo_ta     = 'Danh sách giáo án bạn đã lưu trên ITeduShare.';
$trang_hien_tai = 'gio-hang';

require_once 'inc/header.php';
?>

<main class="trang__chinh">
    <h1 class="can-giua-chu">🔖 Danh sách giáo án đã lưu</h1>
    <p class="can-giua-chu" style="color: #666; margin-bottom: 2rem;">Trang quản lý các bài giảng và giáo án yêu thích của bạn.</p>

    <div style="max-width: 800px; margin: 0 auto; padding: 2rem; background: #f9f9f9; border-radius: 8px; text-align: center; border: 1px dashed #ccc;">
        <p style="font-size: 1.1rem; color: #555; margin-bottom: 1rem;">Hiện tại chưa có giáo án nào được lưu trong danh sách của bạn.</p>
        <a href="kho-hoc-lieu.php" class="nut-thao-tac nut-thao-tac--xem" style="display: inline-block; text-decoration: none;">Khám phá Kho học liệu ngay</a>
    </div>
</main>

<?php require_once 'inc/footer.php'; ?>
    <script type="module" src="js/main.js"></script>
</body>
</html>
