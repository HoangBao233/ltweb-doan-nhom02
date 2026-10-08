<?php
// dang-nhap.php — Trang đăng nhập trang quản trị
// Phụ trách: Tấn (MSSV 3120124027) + Châu — Chức năng 6 Bảng 1
// Cách thử: mở dang-nhap.php, nhập admin / nhom02@ltweb
require __DIR__ . '/inc/config.php';

$loi = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tenDangNhap = trim($_POST['ten_dang_nhap'] ?? '');
    $matKhau     = $_POST['mat_khau'] ?? '';

    require __DIR__ . '/inc/tai-khoan.php';

    if (
        isset(TAI_KHOAN[$tenDangNhap]) &&
        password_verify($matKhau, TAI_KHOAN[$tenDangNhap])
    ) {
        // Cấp mã phiên mới sau khi đăng nhập — chống Session Fixation
        session_regenerate_id(true);
        $_SESSION['user'] = $tenDangNhap;
        header('Location: quan-tri.php');
        exit;
    }

    // Báo lỗi CHUNG — không tiết lộ sai tên hay sai mật khẩu
    $loi = 'Sai tên đăng nhập hoặc mật khẩu.';

    // Ghi log để theo dõi đăng nhập thất bại
    error_log("Đăng nhập sai: user={$tenDangNhap}, ip=" . ($_SERVER['REMOTE_ADDR'] ?? 'unknown'));
}

$tieuDe = 'Đăng nhập | ITeduShare';
$trang  = '';
require __DIR__ . '/inc/header.php';
?>

    <main class="trang__chinh" style="max-width: 480px; margin: 4rem auto; padding: 0 1rem;">
        <h1 style="text-align: center; margin-bottom: 2rem;">🔐 Đăng nhập Quản trị</h1>

        <?php if ($loi !== ''): ?>
            <div role="alert" style="background: #fff3f3; border: 1px solid #e74c3c;
                                     border-radius: 6px; padding: 1rem; margin-bottom: 1.5rem; color: #c0392b;">
                ⚠️ <?= e($loi) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="dang-nhap.php" novalidate
              style="background: #fff; border: 1px solid #ddd; border-radius: 8px; padding: 2rem;">
            <div style="margin-bottom: 1.5rem;">
                <label for="ten-dang-nhap" style="display: block; font-weight: bold; margin-bottom: 0.5rem;">
                    Tên đăng nhập
                </label>
                <input type="text" id="ten-dang-nhap" name="ten_dang_nhap"
                       required autocomplete="username"
                       style="width: 100%; padding: 0.6rem; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;">
            </div>

            <div style="margin-bottom: 2rem;">
                <label for="mat-khau" style="display: block; font-weight: bold; margin-bottom: 0.5rem;">
                    Mật khẩu
                </label>
                <input type="password" id="mat-khau" name="mat_khau"
                       required autocomplete="current-password"
                       style="width: 100%; padding: 0.6rem; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;">
            </div>

            <button type="submit" class="nut-thao-tac nut-thao-tac--xem"
                    style="width: 100%; padding: 0.75rem;">
                Đăng nhập
            </button>
        </form>
    </main>

<?php require __DIR__ . '/inc/footer.php'; ?>

