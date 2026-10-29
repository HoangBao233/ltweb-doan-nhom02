<?php
/**
 * dang-ky.php — Trang đăng ký tài khoản khách hàng mới
 * Đồ án Lập trình Web - Nhóm 02
 * Phụ trách: Nguyễn Tiến Thịnh (MSSV: 3120222126) — Chức năng 4
 */

require_once __DIR__ . '/inc/config.php';

// Nếu đã đăng nhập thì chuyển về trang chủ
if (daDangNhap()) {
    header('Location: index.php');
    exit;
}

$loi = [];
$loiChung = '';

$hoTenNhap   = trim($_POST['ho_ten'] ?? '');
$emailNhap   = trim($_POST['email'] ?? '');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $matKhauNhap        = $_POST['mat_khau'] ?? '';
    $xacNhanMatKhauNhap = $_POST['xac_nhan_mat_khau'] ?? '';

    // 1. Kiểm tra họ tên
    if ($hoTenNhap === '') {
        $loi['ho_ten'] = 'Họ và tên không được để trống.';
    } elseif (mb_strlen($hoTenNhap) < 2 || mb_strlen($hoTenNhap) > 100) {
        $loi['ho_ten'] = 'Họ và tên phải có độ dài từ 2 đến 100 ký tự.';
    }

    // 2. Kiểm tra email (hỗ trợ Ca 6: email sai định dạng "abc")
    if ($emailNhap === '') {
        $loi['email'] = 'Email không được để trống.';
    } elseif (!filter_var($emailNhap, FILTER_VALIDATE_EMAIL)) {
        $loi['email'] = 'Địa chỉ email không đúng định dạng (ví dụ: nguyenvana@gmail.com).';
    }

    // 3. Kiểm tra mật khẩu (hỗ trợ Ca 6: mật khẩu < 8 ký tự)
    if ($matKhauNhap === '') {
        $loi['mat_khau'] = 'Mật khẩu không được để trống.';
    } elseif (mb_strlen($matKhauNhap) < 8) {
        $loi['mat_khau'] = 'Mật khẩu phải có độ dài tối thiểu từ 8 ký tự trở lên.';
    }

    // 4. Kiểm tra xác nhận mật khẩu
    if ($xacNhanMatKhauNhap === '') {
        $loi['xac_nhan_mat_khau'] = 'Vui lòng nhập lại mật khẩu để xác nhận.';
    } elseif ($matKhauNhap !== $xacNhanMatKhauNhap) {
        $loi['xac_nhan_mat_khau'] = 'Mật khẩu xác nhận không khớp với mật khẩu đã nhập.';
    }

    // Nếu không có lỗi biểu mẫu, tiến hành lưu vào CSDL
    if (empty($loi)) {
        // Chuẩn hóa email về chữ thường
        $emailChuan = mb_strtolower($emailNhap);

        // Băm mật khẩu bằng thuật toán an toàn Bcrypt (PASSWORD_DEFAULT)
        $matKhauBam = password_hash($matKhauNhap, PASSWORD_DEFAULT);

        try {
            // LƯU Ý BẢO MẬT: Mặc định vai_tro luôn là 'khach_hang', tuyệt đối KHÔNG nhận vai_tro từ $_POST
            // để phòng chống tấn công leo thang đặc quyền (Privilege Escalation - Ca 6)
            $st = db()->prepare('INSERT INTO nguoi_dung (email, ho_ten, mat_khau, vai_tro) VALUES (?, ?, ?, \'khach_hang\')');
            $st->execute([$emailChuan, $hoTenNhap, $matKhauBam]);

            // Thiết lập thông báo thành công và chuyển hướng sang trang đăng nhập (PRG Pattern)
            $_SESSION['thong_bao'] = 'Đăng ký tài khoản thành công! Vui lòng đăng nhập.';
            header('Location: dang-nhap.php');
            exit;
        } catch (PDOException $e) {
            // Bắt mã lỗi MySQL 1062 (Duplicate entry - trùng email đã có)
            if (isset($e->errorInfo[1]) && $e->errorInfo[1] === 1062) {
                $loi['email'] = 'Email này đã được đăng ký. Vui lòng sử dụng email khác hoặc đăng nhập.';
            } else {
                error_log('Lỗi đăng ký CSDL: ' . $e->getMessage());
                $loiChung = 'Đã xảy ra lỗi máy chủ trong quá trình xử lý đăng ký. Vui lòng thử lại sau.';
            }
        }
    }
}

$tieuDe = 'Đăng ký tài khoản | ITeduShare';
$trang  = 'dang-ky';
require_once __DIR__ . '/inc/header.php';
?>

    <main class="trang__chinh" style="max-width: 520px; margin: 3rem auto; padding: 0 1rem;">
        <h1 style="text-align: center; margin-bottom: 0.5rem; color: var(--mau-chinh, #004085);">📝 Đăng ký tài khoản</h1>
        <p style="text-align: center; color: #555; margin-bottom: 2rem;">Tạo tài khoản để tham gia tải giáo án và lưu bài giảng yêu thích</p>

        <?php if ($loiChung !== ''): ?>
            <div role="alert" aria-live="assertive" style="background: #fff3f3; border: 1px solid #e74c3c; border-radius: 8px; padding: 1rem; margin-bottom: 1.5rem; color: #c0392b;">
                ⚠️ <?= e($loiChung) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="dang-ky.php" novalidate
              style="background: #fff; border: 1px solid #ddd; border-radius: 12px; padding: 2rem; box-shadow: 0 4px 12px rgba(0,0,0,0.05);">
            
            <!-- Ô Họ và tên -->
            <div style="margin-bottom: 1.25rem;">
                <label for="ho-ten" style="display: block; font-weight: 600; margin-bottom: 0.4rem; color: #333;">
                    Họ và tên <span style="color: #e74c3c;" aria-hidden="true">*</span>
                </label>
                <input type="text" id="ho-ten" name="ho_ten"
                       value="<?= e($hoTenNhap) ?>"
                       required autocomplete="name"
                       placeholder="Ví dụ: Nguyễn Văn An"
                       <?= isset($loi['ho_ten']) ? 'aria-invalid="true" aria-describedby="loi-ho-ten"' : '' ?>
                       style="width: 100%; padding: 0.75rem; border: 1px solid <?= isset($loi['ho_ten']) ? '#e74c3c' : '#ccc' ?>; border-radius: 6px; box-sizing: border-box; font-size: 1rem;">
                <?php if (isset($loi['ho_ten'])): ?>
                    <p id="loi-ho-ten" role="alert" style="color: #c0392b; font-size: 0.875rem; margin-top: 0.35rem; margin-bottom: 0;">
                        ⚠️ <?= e($loi['ho_ten']) ?>
                    </p>
                <?php endif; ?>
            </div>

            <!-- Ô Email -->
            <div style="margin-bottom: 1.25rem;">
                <label for="email" style="display: block; font-weight: 600; margin-bottom: 0.4rem; color: #333;">
                    Địa chỉ Email <span style="color: #e74c3c;" aria-hidden="true">*</span>
                </label>
                <input type="email" id="email" name="email"
                       value="<?= e($emailNhap) ?>"
                       required autocomplete="email"
                       placeholder="user@example.com"
                       <?= isset($loi['email']) ? 'aria-invalid="true" aria-describedby="loi-email"' : '' ?>
                       style="width: 100%; padding: 0.75rem; border: 1px solid <?= isset($loi['email']) ? '#e74c3c' : '#ccc' ?>; border-radius: 6px; box-sizing: border-box; font-size: 1rem;">
                <?php if (isset($loi['email'])): ?>
                    <p id="loi-email" role="alert" style="color: #c0392b; font-size: 0.875rem; margin-top: 0.35rem; margin-bottom: 0;">
                        ⚠️ <?= e($loi['email']) ?>
                    </p>
                <?php endif; ?>
            </div>

            <!-- Ô Mật khẩu -->
            <div style="margin-bottom: 1.25rem;">
                <label for="mat-khau" style="display: block; font-weight: 600; margin-bottom: 0.4rem; color: #333;">
                    Mật khẩu (tối thiểu 8 ký tự) <span style="color: #e74c3c;" aria-hidden="true">*</span>
                </label>
                <input type="password" id="mat-khau" name="mat_khau"
                       required autocomplete="new-password"
                       placeholder="Nhập tối thiểu 8 ký tự"
                       <?= isset($loi['mat_khau']) ? 'aria-invalid="true" aria-describedby="loi-mat-khau"' : '' ?>
                       style="width: 100%; padding: 0.75rem; border: 1px solid <?= isset($loi['mat_khau']) ? '#e74c3c' : '#ccc' ?>; border-radius: 6px; box-sizing: border-box; font-size: 1rem;">
                <?php if (isset($loi['mat_khau'])): ?>
                    <p id="loi-mat-khau" role="alert" style="color: #c0392b; font-size: 0.875rem; margin-top: 0.35rem; margin-bottom: 0;">
                        ⚠️ <?= e($loi['mat_khau']) ?>
                    </p>
                <?php endif; ?>
            </div>

            <!-- Ô Xác nhận mật khẩu -->
            <div style="margin-bottom: 1.75rem;">
                <label for="xac-nhan-mat-khau" style="display: block; font-weight: 600; margin-bottom: 0.4rem; color: #333;">
                    Xác nhận lại mật khẩu <span style="color: #e74c3c;" aria-hidden="true">*</span>
                </label>
                <input type="password" id="xac-nhan-mat-khau" name="xac_nhan_mat_khau"
                       required autocomplete="new-password"
                       placeholder="Nhập lại mật khẩu ở trên"
                       <?= isset($loi['xac_nhan_mat_khau']) ? 'aria-invalid="true" aria-describedby="loi-xac-nhan"' : '' ?>
                       style="width: 100%; padding: 0.75rem; border: 1px solid <?= isset($loi['xac_nhan_mat_khau']) ? '#e74c3c' : '#ccc' ?>; border-radius: 6px; box-sizing: border-box; font-size: 1rem;">
                <?php if (isset($loi['xac_nhan_mat_khau'])): ?>
                    <p id="loi-xac-nhan" role="alert" style="color: #c0392b; font-size: 0.875rem; margin-top: 0.35rem; margin-bottom: 0;">
                        ⚠️ <?= e($loi['xac_nhan_mat_khau']) ?>
                    </p>
                <?php endif; ?>
            </div>

            <button type="submit" class="nut-thao-tac nut-thao-tac--xem"
                    style="width: 100%; padding: 0.85rem; font-size: 1.05rem; font-weight: bold; border-radius: 6px; cursor: pointer;">
                Đăng ký tài khoản
            </button>

            <div style="margin-top: 1.5rem; text-align: center; font-size: 0.95rem; color: #555;">
                Đã có tài khoản? <a href="dang-nhap.php" style="color: var(--mau-chinh, #007bff); font-weight: 600; text-decoration: none;">Đăng nhập</a>
            </div>
        </form>
    </main>

<?php require_once __DIR__ . '/inc/footer.php'; ?>
</body>
</html>

