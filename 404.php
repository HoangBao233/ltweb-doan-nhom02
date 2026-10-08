<?php
/**
 * 404.php — Trang báo lỗi 404 không tìm thấy trang
 */
require_once 'inc/config.php';
$tieu_de_trang  = 'Không tìm thấy trang';
$meta_mo_ta     = 'Trang bạn tìm kiếm không tồn tại.';
$trang_hien_tai = '404';

require_once 'inc/header.php';
?>

<main class="trang__chinh" style="text-align: center; padding: 4rem 1rem;">
    <h1 style="font-size: 4rem; color: var(--mau-chinh, #2196f3); margin: 0;">404</h1>
    <h2 style="margin-top: 0.5rem;">Không tìm thấy trang</h2>
    <p style="color: #666; margin-bottom: 2rem;">Rất tiếc, trang bạn đang truy cập không tồn tại hoặc đã bị di chuyển.</p>
    <a href="index.php" class="nut-thao-tac nut-thao-tac--xem" style="text-decoration: none;">Quay về Trang chủ</a>
</main>

<?php require_once 'inc/footer.php'; ?>
    <script type="module" src="js/main.js"></script>
</body>
</html>
