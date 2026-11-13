<?php
/**
 * 404.php — Trang báo lỗi 404 không tìm thấy trang
 */
require_once 'inc/config.php';
http_response_code(404); // Đảm bảo luôn trả mã 404 dù truy cập trực tiếp hay gọi từ chi-tiet-giao-an.php
$tieu_de_trang  = 'Không tìm thấy trang';
$meta_mo_ta     = 'Trang bạn tìm kiếm không tồn tại.';
$trang_hien_tai = '404';

require_once 'inc/header.php';
?>

<main class="trang__chinh hop-loi-404">
    <h1 class="ma-loi-404">404</h1>
    <h2 class="tieu-de-loi-404">Không tìm thấy trang</h2>
    <p class="mo-ta-loi-404">Rất tiếc, trang bạn đang truy cập không tồn tại hoặc đã bị di chuyển.</p>
    <a href="index.php" class="nut-thao-tac nut-thao-tac--xem link-khong-gach">Quay về Trang chủ</a>
</main>

<?php require_once 'inc/footer.php'; ?>
    <script type="module" src="js/main.js"></script>
</body>
</html>
