<?php
/**
 * 500.php — Trang báo lỗi máy chủ 500
 */
require_once 'inc/config.php';
$tieu_de_trang  = 'Lỗi máy chủ';
$meta_mo_ta     = 'Đã xảy ra lỗi máy chủ.';
$trang_hien_tai = '500';

require_once 'inc/header.php';
?>

<main class="trang__chinh" style="text-align: center; padding: 4rem 1rem;">
    <h1 style="font-size: 4rem; color: #e53935; margin: 0;">500</h1>
    <h2 style="margin-top: 0.5rem;">Lỗi hệ thống máy chủ</h2>
    <p style="color: #666; margin-bottom: 2rem;">Hệ thống đang gặp sự cố nhỏ. Vui lòng thử lại sau.</p>
    <a href="index.php" class="nut-thao-tac nut-thao-tac--xem" style="text-decoration: none;">Quay về Trang chủ</a>
</main>

<?php require_once 'inc/footer.php'; ?>
    <script type="module" src="js/main.js"></script>
</body>
</html>
