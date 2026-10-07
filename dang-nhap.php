<?php
/**
 * dang-nhap.php — Form đăng nhập admin
 */
require_once 'inc/config.php';

// Đã đăng nhập rồi → về quản trị
if (!empty($_SESSION['admin'])) {
    chuyen_trang('quan-tri.php');
}

$loi = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ten_dang_nhap = trim($_POST['ten_dang_nhap'] ?? '');
    $mat_khau      = $_POST['mat_khau'] ?? '';

    $tai_khoan = require 'inc/tai-khoan.php';

    if (isset($tai_khoan[$ten_dang_nhap]) &&
        password_verify($mat_khau, $tai_khoan[$ten_dang_nhap]['mat_khau_bam'])) {
        // Đăng nhập thành công
        session_regenerate_id(true);
        $_SESSION['admin'] = [
            'username'     => $ten_dang_nhap,
            'ten_hien_thi' => $tai_khoan[$ten_dang_nhap]['ten_hien_thi'],
        ];
        chuyen_trang('quan-tri.php');
    } else {
        $loi = 'Tên đăng nhập hoặc mật khẩu không đúng.';
    }
}

$tieu_de_trang  = 'Đăng nhập';
$meta_mo_ta     = '';
$trang_hien_tai = 'dang-nhap';

require_once 'inc/header.php';
?>

    <main class="trang__chinh" style="max-width: 400px; margin: 3rem auto;">
        <h1 class="can-giua-chu">🔑 Đăng nhập quản trị</h1>

        <?php if ($loi): ?>
            <div role="alert"
                 style="background:#ffebee; color:#c62828; padding:0.75rem 1rem; border-radius:6px; margin-bottom:1rem; text-align:center;">
                <?= e($loi) ?>
            </div>
        <?php endif; ?>

        <!-- Hiển thị flash message nếu có (vd: từ bao-ve.php) -->
        <?php $flash = lay_flash(); if ($flash): ?>
            <div role="alert"
                 style="background:#fff3e0; color:#e65100; padding:0.75rem 1rem; border-radius:6px; margin-bottom:1rem; text-align:center;">
                <?= e($flash['noi_dung']) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="dang-nhap.php" class="bieu-mau"
              style="background:var(--nen-phu,#f9f9f9); padding:1.5rem; border-radius:8px; border:1px solid var(--vien,#ddd);">

            <div class="bieu-mau__nhom" style="margin-bottom:1rem;">
                <label for="ten_dang_nhap" style="display:block; font-weight:bold; margin-bottom:0.4rem;">
                    Tên đăng nhập
                </label>
                <input type="text" id="ten_dang_nhap" name="ten_dang_nhap"
                       class="bieu-mau__o-nhap"
                       style="width:100%; padding:0.5rem; border:1px solid #ccc; border-radius:4px;"
                       required autofocus autocomplete="username">
            </div>

            <div class="bieu-mau__nhom" style="margin-bottom:1.5rem;">
                <label for="mat_khau" style="display:block; font-weight:bold; margin-bottom:0.4rem;">
                    Mật khẩu
                </label>
                <input type="password" id="mat_khau" name="mat_khau"
                       class="bieu-mau__o-nhap"
                       style="width:100%; padding:0.5rem; border:1px solid #ccc; border-radius:4px;"
                       required autocomplete="current-password">
            </div>

            <button type="submit"
                    style="width:100%; padding:0.75rem; background:var(--mau-chinh,#004d40); color:#fff; border:none; border-radius:4px; font-weight:bold; cursor:pointer; font-size:1rem;">
                Đăng nhập
            </button>
        </form>

        <p style="text-align:center; margin-top:1rem;">
            <a href="index.php">← Quay về trang chủ</a>
        </p>
    </main>

<?php require_once 'inc/footer.php'; ?>

    <script type="module" src="js/main.js"></script>
</body>
</html>
