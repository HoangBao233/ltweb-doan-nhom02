<?php
/**
 * dang-ky.php — Trang đăng ký tài khoản khách hàng mới
 * Đồ án Lập trình Web - Nhóm 02
 * Phụ trách: Nguyễn Tiến Thịnh (MSSV: 3120222126) — Chức năng 4 & Biện pháp 1, 3
 */

require_once __DIR__ . '/inc/config.php';
require_once __DIR__ . '/inc/csrf.php';

// Nếu đã đăng nhập thì chuyển về trang chủ
if (daDangNhap()) {
    header('Location: index.php');
    exit;
}

$loi = [];
$loiChung = '';

$hoTenNhap   = trim(chuoi($_POST['ho_ten'] ?? ''));
$emailNhap   = trim(chuoi($_POST['email'] ?? ''));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $matKhauNhap        = chuoi($_POST['mat_khau'] ?? '');
    $xacNhanMatKhauNhap = chuoi($_POST['xac_nhan_mat_khau'] ?? '');

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

    <main class="trang__chinh khung-xac-thuc khung-xac-thuc--rong">
        <h1 class="khung-xac-thuc__tieu-de">Đăng ký tài khoản</h1>
        <p class="khung-xac-thuc__mo-ta">Tạo tài khoản để tham gia tải giáo án và lưu bài giảng yêu thích</p>

        <?php if ($loiChung !== ''): ?>
            <div role="alert" aria-live="assertive" class="thong-bao-loi">
                <?= e($loiChung) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="dang-ky.php" novalidate class="bieu-mau-xac-thuc">
            <?= csrfField() ?>
            <!-- Ô Họ và tên -->
            <div class="bieu-mau-xac-thuc__nhom">
                <label for="ho-ten" class="bieu-mau-xac-thuc__nhan">
                    Họ và tên <span class="bieu-mau-xac-thuc__bat-buoc" aria-hidden="true">*</span>
                </label>
                <input type="text" id="ho-ten" name="ho_ten"
                       value="<?= e($hoTenNhap) ?>"
                       required autocomplete="name"
                       placeholder="Họ và tên"
                       <?= isset($loi['ho_ten']) ? 'aria-invalid="true" aria-describedby="loi-ho-ten"' : '' ?>
                       class="bieu-mau-xac-thuc__o-nhap <?= isset($loi['ho_ten']) ? 'bieu-mau-xac-thuc__o-nhap--loi' : '' ?>">
                <?php if (isset($loi['ho_ten'])): ?>
                    <p id="loi-ho-ten" role="alert" class="bieu-mau-xac-thuc__loi-nhap">
                        <?= e($loi['ho_ten']) ?>
                    </p>
                <?php endif; ?>
            </div>

            <!-- Ô Email -->
            <div class="bieu-mau-xac-thuc__nhom">
                <label for="email" class="bieu-mau-xac-thuc__nhan">
                    Địa chỉ Email <span class="bieu-mau-xac-thuc__bat-buoc" aria-hidden="true">*</span>
                </label>
                <input type="email" id="email" name="email"
                       value="<?= e($emailNhap) ?>"
                       required autocomplete="email"
                       placeholder="Địa chỉ email"
                       <?= isset($loi['email']) ? 'aria-invalid="true" aria-describedby="loi-email"' : '' ?>
                       class="bieu-mau-xac-thuc__o-nhap <?= isset($loi['email']) ? 'bieu-mau-xac-thuc__o-nhap--loi' : '' ?>">
                <?php if (isset($loi['email'])): ?>
                    <p id="loi-email" role="alert" class="bieu-mau-xac-thuc__loi-nhap">
                        <?= e($loi['email']) ?>
                    </p>
                <?php endif; ?>
            </div>

            <!-- Ô Mật khẩu -->
            <div class="bieu-mau-xac-thuc__nhom">
                <label for="mat-khau" class="bieu-mau-xac-thuc__nhan">
                    Mật khẩu (tối thiểu 8 ký tự) <span class="bieu-mau-xac-thuc__bat-buoc" aria-hidden="true">*</span>
                </label>
                <input type="password" id="mat-khau" name="mat_khau"
                       required autocomplete="new-password"
                       placeholder="Mật khẩu"
                       <?= isset($loi['mat_khau']) ? 'aria-invalid="true" aria-describedby="loi-mat-khau"' : '' ?>
                       class="bieu-mau-xac-thuc__o-nhap <?= isset($loi['mat_khau']) ? 'bieu-mau-xac-thuc__o-nhap--loi' : '' ?>">
                <?php if (isset($loi['mat_khau'])): ?>
                    <p id="loi-mat-khau" role="alert" class="bieu-mau-xac-thuc__loi-nhap">
                        <?= e($loi['mat_khau']) ?>
                    </p>
                <?php endif; ?>
            </div>

            <!-- Ô Xác nhận mật khẩu -->
            <div class="bieu-mau-xac-thuc__nhom--cuoi">
                <label for="xac-nhan-mat-khau" class="bieu-mau-xac-thuc__nhan">
                    Xác nhận lại mật khẩu <span class="bieu-mau-xac-thuc__bat-buoc" aria-hidden="true">*</span>
                </label>
                <input type="password" id="xac-nhan-mat-khau" name="xac_nhan_mat_khau"
                       required autocomplete="new-password"
                       placeholder="Xác nhận lại mật khẩu"
                       <?= isset($loi['xac_nhan_mat_khau']) ? 'aria-invalid="true" aria-describedby="loi-xac-nhan"' : '' ?>
                       class="bieu-mau-xac-thuc__o-nhap <?= isset($loi['xac_nhan_mat_khau']) ? 'bieu-mau-xac-thuc__o-nhap--loi' : '' ?>">
                <?php if (isset($loi['xac_nhan_mat_khau'])): ?>
                    <p id="loi-xac-nhan" role="alert" class="bieu-mau-xac-thuc__loi-nhap">
                        <?= e($loi['xac_nhan_mat_khau']) ?>
                    </p>
                <?php endif; ?>
            </div>

            <button type="submit" class="nut-thao-tac nut-thao-tac--xem bieu-mau-xac-thuc__nut-gui">
                Đăng ký tài khoản
            </button>

            <div class="bieu-mau-xac-thuc__chuyen-trang">
                Đã có tài khoản? <a href="dang-nhap.php" class="bieu-mau-xac-thuc__lien-ket">Đăng nhập</a>
            </div>
        </form>
    </main>

<?php require_once __DIR__ . '/inc/footer.php'; ?>
</body>
</html>

