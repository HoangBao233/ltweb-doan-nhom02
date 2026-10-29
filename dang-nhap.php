<?php
/**
 * dang-nhap.php — Trang đăng nhập hệ thống ITeduShare
 * Đồ án Lập trình Web - Nhóm 02
 * Phụ trách: Nguyễn Tiến Thịnh (MSSV: 3120222126) — Chức năng 4
 */

require_once __DIR__ . '/inc/config.php';

// Nếu đã đăng nhập thì chuyển hướng phù hợp
if (daDangNhap()) {
    $u = nguoiDung();
    if ($u && ($u['vai_tro'] ?? '') === 'quan_tri') {
        header('Location: quan-tri.php');
    } else {
        header('Location: index.php');
    }
    exit;
}

$loi = '';
$thongBao = $_SESSION['thong_bao'] ?? '';
unset($_SESSION['thong_bao']);

$emailNhap = trim($_POST['email'] ?? $_POST['ten_dang_nhap'] ?? '');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $matKhau = $_POST['mat_khau'] ?? '';

    if ($emailNhap === '' || $matKhau === '') {
        $loi = 'Vui lòng nhập đầy đủ email và mật khẩu.';
    } else {
        // Cho phép alias 'admin' tương đương 'admin@ued.vn' để thuận tiện kiểm thử
        $emailTim = ($emailNhap === 'admin') ? 'admin@ued.vn' : mb_strtolower($emailNhap);

        try {
            $st = db()->prepare('SELECT id, email, ho_ten, mat_khau, vai_tro FROM nguoi_dung WHERE email = ? LIMIT 1');
            $st->execute([$emailTim]);
            $u = $st->fetch();

            if ($u && password_verify($matKhau, $u['mat_khau'])) {
                // Kiểm tra nếu cần cập nhật lại thuật toán băm
                if (password_needs_rehash($u['mat_khau'], PASSWORD_DEFAULT)) {
                    $hashMoi = password_hash($matKhau, PASSWORD_DEFAULT);
                    $stRehash = db()->prepare('UPDATE nguoi_dung SET mat_khau = ? WHERE id = ?');
                    $stRehash->execute([$hashMoi, $u['id']]);
                }

                // Cấp mã phiên mới — chống Session Fixation (Ca 7)
                session_regenerate_id(true);

                // Lưu ID phiên (Session chỉ lưu uid để chống lỗi phân quyền cũ)
                $_SESSION['uid'] = (int)$u['id'];
                $_SESSION['user'] = $u['ho_ten']; // tương thích hiển thị cũ

                // Chuyển hướng sau khi đăng nhập (PRG)
                if (($u['vai_tro'] ?? '') === 'quan_tri') {
                    header('Location: quan-tri.php');
                } else {
                    header('Location: index.php');
                }
                exit;
            } else {
                // Thông báo lỗi CHUNG — không tiết lộ sai email hay sai mật khẩu (chống User Enumeration - Ca 7)
                $loi = 'Email hoặc mật khẩu không chính xác.';
                error_log("Đăng nhập thất bại: email={$emailTim}, ip=" . ($_SERVER['REMOTE_ADDR'] ?? 'unknown'));
            }
        } catch (Throwable $e) {
            error_log("Lỗi đăng nhập: " . $e->getMessage());
            $loi = 'Đã xảy ra lỗi máy chủ trong quá trình đăng nhập. Vui lòng thử lại sau.';
        }
    }
}

$tieuDe = 'Đăng nhập | ITeduShare';
$trang  = 'dang-nhap';
require_once __DIR__ . '/inc/header.php';
?>

    <main class="trang__chinh" style="max-width: 480px; margin: 3rem auto; padding: 0 1rem;">
        <h1 style="text-align: center; margin-bottom: 1.5rem; color: var(--mau-chinh, #004085);">🔐 Đăng nhập tài khoản</h1>

        <?php if ($thongBao !== ''): ?>
            <div role="status" style="background: #e8f5e9; border: 1px solid #4caf50; border-radius: 8px; padding: 1rem; margin-bottom: 1.5rem; color: #2e7d32;">
                ✅ <?= e($thongBao) ?>
            </div>
        <?php endif; ?>

        <?php if ($loi !== ''): ?>
            <div role="alert" aria-live="assertive" style="background: #fff3f3; border: 1px solid #e74c3c; border-radius: 8px; padding: 1rem; margin-bottom: 1.5rem; color: #c0392b;">
                ⚠️ <?= e($loi) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="dang-nhap.php" novalidate
              style="background: #fff; border: 1px solid #ddd; border-radius: 12px; padding: 2rem; box-shadow: 0 4px 12px rgba(0,0,0,0.05);">
            <div style="margin-bottom: 1.25rem;">
                <label for="email" style="display: block; font-weight: 600; margin-bottom: 0.5rem; color: #333;">
                    Địa chỉ Email <span style="color: #e74c3c;">*</span>
                </label>
                <input type="text" id="email" name="email"
                       value="<?= e($emailNhap) ?>"
                       required autocomplete="email"
                       placeholder="Ví dụ: admin@ued.vn hoặc lan@ued.vn"
                       style="width: 100%; padding: 0.75rem; border: 1px solid #ccc; border-radius: 6px; box-sizing: border-box; font-size: 1rem;">
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label for="mat-khau" style="display: block; font-weight: 600; margin-bottom: 0.5rem; color: #333;">
                    Mật khẩu <span style="color: #e74c3c;">*</span>
                </label>
                <input type="password" id="mat-khau" name="mat_khau"
                       required autocomplete="current-password"
                       placeholder="Nhập mật khẩu của bạn"
                       style="width: 100%; padding: 0.75rem; border: 1px solid #ccc; border-radius: 6px; box-sizing: border-box; font-size: 1rem;">
            </div>

            <button type="submit" class="nut-thao-tac nut-thao-tac--xem"
                    style="width: 100%; padding: 0.85rem; font-size: 1.05rem; font-weight: bold; border-radius: 6px; cursor: pointer;">
                Đăng nhập
            </button>

            <div style="margin-top: 1.5rem; text-align: center; font-size: 0.95rem; color: #555;">
                Chưa có tài khoản? <a href="dang-ky.php" style="color: var(--mau-chinh, #007bff); font-weight: 600; text-decoration: none;">Đăng ký ngay</a>
            </div>
        </form>

        <div style="margin-top: 2rem; background: #f8f9fa; border: 1px dashed #ced4da; border-radius: 8px; padding: 1rem; font-size: 0.85rem; color: #666;">
            <strong>Tài khoản thử nghiệm kiểm thử:</strong>
            <ul style="margin: 0.5rem 0 0 1.25rem; padding: 0;">
                <li><strong>Quản trị viên:</strong> <code>admin@ued.vn</code> (hoặc <code>admin</code>) — MK: <code>matkhau123</code></li>
                <li><strong>Khách hàng:</strong> <code>lan@ued.vn</code> hoặc <code>an@ued.vn</code> — MK: <code>matkhau123</code></li>
            </ul>
        </div>
    </main>

<?php require_once __DIR__ . '/inc/footer.php'; ?>
</body>
</html>
