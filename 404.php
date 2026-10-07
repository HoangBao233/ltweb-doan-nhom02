<?php
/**
 * 404.php — Trang lỗi không tìm thấy
 */
if (!defined('ROOT')) {
    require_once 'inc/config.php';
}
if (http_response_code() === 200) {
    http_response_code(404);
}

$tieu_de_trang  = 'Không tìm thấy trang';
$meta_mo_ta     = '';
$trang_hien_tai = '';

// Đảm bảo header.php chỉ được include nếu chưa output
if (!headers_sent()) {
    require_once 'inc/header.php';
}
?>

    <main class="trang__chinh" style="text-align: center; padding: 4rem 1rem;">
        <h1 style="font-size: 5rem; margin: 0; color: var(--mau-chinh, #004085); line-height: 1;">404</h1>
        <h2 style="margin-top: 0.5rem;">Không tìm thấy trang</h2>
        <p style="color: #666; margin-bottom: 2rem;">Trang bạn đang tìm kiếm không tồn tại hoặc đã bị xoá.</p>
        <a href="index.php" class="nut-thao-tac nut-thao-tac--xem">← Về trang chủ</a>
        <span style="margin: 0 1rem; color: #ccc;">|</span>
        <a href="kho-hoc-lieu.php" class="nut-thao-tac">Xem kho học liệu</a>
    </main>

<?php require_once 'inc/footer.php'; ?>

    <script type="module" src="js/main.js"></script>
</body>
</html>
