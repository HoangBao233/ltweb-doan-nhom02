<?php
/**
 * dang-nhap.php — Trang đăng nhập hệ thống ITeduShare
 * Đồ án Lập trình Web - Nhóm 02
 * Phụ trách: Nguyễn Tiến Thịnh (MSSV: 3120222126) — Chức năng 4 & Biện pháp 2, 7
 */

require_once __DIR__ . '/inc/config.php';
require_once __DIR__ . '/inc/csrf.php';

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

$emailNhap = trim(chuoi($_POST['email'] ?? $_POST['ten_dang_nhap'] ?? ''));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $matKhau = chuoi($_POST['mat_khau'] ?? '');

    if ($emailNhap === '' || $matKhau === '') {
        $loi = 'Vui lòng nhập đầy đủ email và mật khẩu.';
    } else {
        // Cho phép alias 'admin' tương đương 'admin@ued.vn' để thuận tiện kiểm thử
        $emailTim = ($emailNhap === 'admin') ? 'admin@ued.vn' : mb_strtolower($emailNhap);

        // Chống Brute-force (Biện pháp 7): Kiểm tra số lần sai trong 15 phút trước khi truy vấn mật khẩu
        if (soLanSai($emailTim) >= 5) {
            http_response_code(429);
            $loi = 'Tài khoản tạm thời bị khóa do đăng nhập sai quá 5 lần. Vui lòng thử lại sau 15 phút.';
        } else {
            try {
                $st = db()->prepare('SELECT id, email, ho_ten, mat_khau, vai_tro FROM nguoi_dung WHERE email = ? LIMIT 1');
                $st->execute([$emailTim]);
                $u = $st->fetch();

                if ($u && password_verify($matKhau, $u['mat_khau'])) {
                    // Đăng nhập thành công: Xóa lịch sử đăng nhập sai của email này (Biện pháp 7)
                    $stXoa = db()->prepare('DELETE FROM dang_nhap_sai WHERE email = ?');
                    $stXoa->execute([$emailTim]);

                    // Kiểm tra nếu cần cập nhật lại thuật toán băm
                    if (password_needs_rehash($u['mat_khau'], PASSWORD_DEFAULT)) {
                        $hashMoi = password_hash($matKhau, PASSWORD_DEFAULT);
                        $stRehash = db()->prepare('UPDATE nguoi_dung SET mat_khau = ? WHERE id = ?');
                        $stRehash->execute([$hashMoi, $u['id']]);
                    }

                    // Cấp mã phiên mới chống Session Fixation và lưu uid qua hàm chuẩn hóa (Biện pháp 2)
                    dangNhap((int)$u['id']);

                    // Chuyển hướng sau khi đăng nhập (PRG)
                    if (($u['vai_tro'] ?? '') === 'quan_tri') {
                        header('Location: quan-tri.php');
                    } else {
                        header('Location: index.php');
                    }
                    exit;
                } else {
                    // Đăng nhập thất bại: Ghi nhận lần sai vào DB và logs/bao-mat.log (Biện pháp 7)
                    ghiLanSai($emailTim);

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
}

$tieuDe = 'Đăng nhập | ITeduShare';
$trang  = 'dang-nhap';
require_once __DIR__ . '/inc/header.php';
?>

    <main class="trang__chinh khung-xac-thuc">
        <h1 class="khung-xac-thuc__tieu-de">Đăng nhập tài khoản</h1>

        <?php if ($thongBao !== ''): ?>
            <div role="status" class="thong-bao-thanh-cong">
                <?= e($thongBao) ?>
            </div>
        <?php endif; ?>

        <?php if ($loi !== ''): ?>
            <div role="alert" aria-live="assertive" class="thong-bao-loi">
                <?= e($loi) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="dang-nhap.php" novalidate class="bieu-mau-xac-thuc">
            <?= csrfField() ?>
            <div class="bieu-mau-xac-thuc__nhom">
                <label for="email" class="bieu-mau-xac-thuc__nhan">
                    Địa chỉ Email <span class="bieu-mau-xac-thuc__bat-buoc">*</span>
                </label>
                <input type="text" id="email" name="email"
                       value="<?= e($emailNhap) ?>"
                       required autocomplete="email"
                       placeholder="Địa chỉ email"
                       class="bieu-mau-xac-thuc__o-nhap">
            </div>

            <div class="bieu-mau-xac-thuc__nhom--cuoi">
                <label for="mat-khau" class="bieu-mau-xac-thuc__nhan">
                    Mật khẩu <span class="bieu-mau-xac-thuc__bat-buoc">*</span>
                </label>
                <input type="password" id="mat-khau" name="mat_khau"
                       required autocomplete="current-password"
                       placeholder="Mật khẩu"
                       class="bieu-mau-xac-thuc__o-nhap">
            </div>

            <button type="submit" class="nut-thao-tac nut-thao-tac--xem bieu-mau-xac-thuc__nut-gui">
                Đăng nhập
            </button>

            <div class="bieu-mau-xac-thuc__chuyen-trang">
                Chưa có tài khoản? <a href="dang-ky.php" class="bieu-mau-xac-thuc__lien-ket">Đăng ký ngay</a>
            </div>
        </form>

        <div class="hop-tai-khoan-thu">
            <strong>Tài khoản thử nghiệm kiểm thử:</strong>
            <ul class="hop-tai-khoan-thu__danh-sach">
                <li><strong>Quản trị viên:</strong> <code>admin@ued.vn</code> (hoặc <code>admin</code>) — MK: <code>matkhau123</code></li>
                <li><strong>Khách hàng:</strong> <code>lan@ued.vn</code> hoặc <code>an@ued.vn</code> — MK: <code>matkhau123</code></li>
            </ul>
        </div>
    </main>

<?php require_once __DIR__ . '/inc/footer.php'; ?>
</body>
</html>
