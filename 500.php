<?php
/**
 * 500.php — Trang lỗi máy chủ
 */
if (!defined('ROOT')) {
    require_once 'inc/config.php';
}
http_response_code(500);

$tieu_de_trang  = 'Lỗi máy chủ';
$meta_mo_ta     = '';
$trang_hien_tai = '';

require_once 'inc/header.php';
?>

    <main class="trang__chinh" style="text-align: center; padding: 4rem 1rem;">
        <h1 style="font-size: 5rem; margin: 0; color: #e53935; line-height: 1;">500</h1>
        <h2 style="margin-top: 0.5rem;">Lỗi máy chủ nội bộ</h2>
        <p style="color: #666; margin-bottom: 2rem;">Đã có sự cố xảy ra. Vui lòng thử lại sau hoặc liên hệ quản trị viên.</p>
        <a href="index.php" class="nut-thao-tac nut-thao-tac--xem">← Về trang chủ</a>
    </main>

<?php require_once 'inc/footer.php'; ?>

    <script type="module" src="js/main.js"></script>
</body>
</html>
