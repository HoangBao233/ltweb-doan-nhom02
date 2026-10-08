<?php
/**
 * dang-nhap.php — Trang đăng nhập hệ thống (Giao diện UI mẫu)
 */
require_once 'inc/config.php';
$tieu_de_trang  = 'Đăng nhập';
$meta_mo_ta     = 'Đăng nhập tài khoản quản trị ITeduShare.';
$trang_hien_tai = 'dang-nhap';

require_once 'inc/header.php';
?>

<main class="trang__chinh phan-lien-he">
    <h1 class="can-giua-chu">🔑 Đăng nhập hệ thống</h1>

    <section class="bieu-mau" style="max-width: 450px; margin: 0 auto;">
        <form action="quan-tri.php" method="GET" class="bieu-mau__khung">
            <div class="bieu-mau__nhom">
                <label for="username" class="bieu-mau__nhan">Tên đăng nhập:</label>
                <input type="text" id="username" name="username" class="bieu-mau__o-nhap" placeholder="Nhập tên đăng nhập..." required>
            </div>

            <div class="bieu-mau__nhom" style="margin-top: 1rem;">
                <label for="password" class="bieu-mau__nhan">Mật khẩu:</label>
                <input type="password" id="password" name="password" class="bieu-mau__o-nhap" placeholder="Nhập mật khẩu..." required>
            </div>

            <div style="margin-top: 1.5rem;">
                <button type="submit" class="nut-thao-tac nut-thao-tac--xem" style="width: 100%;">Đăng nhập</button>
            </div>
        </form>
    </section>
</main>

<?php require_once 'inc/footer.php'; ?>
    <script type="module" src="js/main.js"></script>
</body>
</html>
