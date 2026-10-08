<?php
/**
 * quan-tri.php — Trang quản trị hệ thống (Giao diện UI mẫu)
 */
require_once 'inc/config.php';
$tieu_de_trang  = 'Quản trị hệ thống';
$meta_mo_ta     = 'Bảng điều khiển dành cho quản trị viên ITeduShare.';
$trang_hien_tai = 'quan-tri';

require_once 'inc/header.php';
?>

<main class="trang__chinh">
    <h1 class="can-giua-chu">⚙ Trang Quản trị Hệ thống</h1>
    <p class="can-giua-chu" style="color: #666; margin-bottom: 2rem;">Bảng điều khiển và quản lý dành cho Admin.</p>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
        <div style="padding: 1.5rem; background: #e3f2fd; border-radius: 8px; text-align: center;">
            <h3 style="margin: 0; color: #1976d2;">12</h3>
            <p style="margin: 0.5rem 0 0; color: #555;">Tổng số giáo án</p>
        </div>
        <div style="padding: 1.5rem; background: #e8f5e9; border-radius: 8px; text-align: center;">
            <h3 style="margin: 0; color: #388e3c;">5</h3>
            <p style="margin: 0.5rem 0 0; color: #555;">Tài liệu đã đóng góp</p>
        </div>
        <div style="padding: 1.5rem; background: #fff3e0; border-radius: 8px; text-align: center;">
            <h3 style="margin: 0; color: #f57c00;">3</h3>
            <p style="margin: 0.5rem 0 0; color: #555;">Yêu cầu liên hệ mới</p>
        </div>
    </div>
</main>

<?php require_once 'inc/footer.php'; ?>
    <script type="module" src="js/main.js"></script>
</body>
</html>
