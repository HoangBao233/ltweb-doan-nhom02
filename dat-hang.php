<?php
/**
 * dat-hang.php — Trang xác nhận và xử lý đặt hàng / đăng ký tải giáo án
 * Đồ án Lập trình Web - Nhóm 02
 * Phụ trách: Nguyễn Tiến Thịnh (MSSV: 3120222126) & Lê Hoàng Bảo — Chức năng 5 & Biện pháp 1, 3
 */

require_once __DIR__ . '/inc/config.php';
require_once __DIR__ . '/inc/csrf.php';
require_once __DIR__ . '/inc/don-hang.php';

use App\Services\DanhSachLuu;
use App\Data\KhoGiaoAn;

// Bắt buộc phải đăng nhập trước khi đặt hàng (Ca 8)
yeuCauDangNhap();

$u = nguoiDung();
$kho = new KhoGiaoAn();
$dsLuu = new DanhSachLuu();

// Lấy danh sách ID các giáo án trong giỏ
$gioHang = $_SESSION['danh_sach_luu'] ?? [];

// Nếu giỏ rỗng, chuyển về giỏ hàng
if (empty($gioHang)) {
    $_SESSION['loi'] = 'Giỏ hàng của bạn đang trống. Vui lòng chọn giáo án trước.';
    header('Location: gio-hang.php');
    exit;
}

$loiDatHang = '';

// Xử lý gửi đơn hàng (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ghiChu = trim(chuoi($_POST['ghi_chu'] ?? ''));

    try {
        // Thực hiện lưu đơn hàng trong Transaction
        $donHangId = luuDonHang((int)$u['id'], $gioHang, $ghiChu);

        // Đặt hàng thành công -> Xóa sạch giỏ hàng trong session
        $dsLuu->xoaHet();

        // Thiết lập thông báo thành công
        $_SESSION['thong_bao'] = "Đặt giáo án thành công! Mã đơn hàng của bạn là #{$donHangId}.";

        // Chuyển hướng sang trang chi tiết đơn hàng (PRG Pattern - Ca 9)
        header("Location: don-hang.php?id={$donHangId}");
        exit;
    } catch (Exception $e) {
        // Nếu lỗi (ví dụ không đủ tồn kho - Ca 10), giữ nguyên giỏ hàng và báo lỗi
        $loiDatHang = $e->getMessage();
    }
}

// Lấy thông tin các giáo án để hiển thị trang xác nhận
$ids = array_keys($gioHang);
$dsGiaoAn = !empty($ids) ? $kho->theoDanhSachId($ids) : [];

$tongTien = 0;
foreach ($dsGiaoAn as $ga) {
    $sl = $gioHang[$ga->id] ?? 1;
    $tongTien += $ga->gia * $sl;
}

$tieuDe = 'Xác nhận đặt giáo án | ITeduShare';
$trang  = 'dat-hang';
require_once __DIR__ . '/inc/header.php';
?>

<main class="trang__chinh khung-don-hang">
    <h1 class="khung-don-hang__tieu-de">Xác nhận đặt giáo án</h1>
    <p class="khung-don-hang__mo-ta">Vui lòng kiểm tra lại danh sách học liệu và thông tin người nhận</p>

    <?php if ($loiDatHang !== ''): ?>
        <div role="alert" aria-live="assertive" class="thong-bao-loi">
            <strong>Không thể hoàn tất đơn hàng:</strong> <?= e($loiDatHang) ?>
        </div>
    <?php endif; ?>

    <div class="the-trang-thong-tin">
        <h2 class="the-trang-thong-tin__tieu-de">
            Thông tin người đặt
        </h2>
        <div class="luoi-hai-cot">
            <div>
                <span class="chu-nhan-phu">Họ và tên:</span>
                <p class="chu-gia-tri"><?= e($u['ho_ten']) ?></p>
            </div>
            <div>
                <span class="chu-nhan-phu">Email nhận tài liệu:</span>
                <p class="chu-gia-tri"><?= e($u['email']) ?></p>
            </div>
        </div>

        <h2 class="the-trang-thong-tin__tieu-de">
            Danh sách giáo án đăng ký tải
        </h2>
        <div class="khung-cuon-bang">
            <table class="bang-du-lieu">
                <thead>
                    <tr>
                        <th class="chu-can-trai">Tên giáo án</th>
                        <th class="chu-can-giua">Số lượng</th>
                        <th class="chu-can-phai">Đơn giá</th>
                        <th class="chu-can-phai">Thành tiền</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($dsGiaoAn as $ga): 
                        $sl = $gioHang[$ga->id] ?? 1;
                        $thanhTien = $ga->gia * $sl;
                    ?>
                        <tr>
                            <td class="chu-can-trai">
                                <?= e($ga->ten) ?>
                                <span class="nhan-nho <?= $ga->cap_hoc === 'THCS' ? 'nhan-nho--xanh' : 'nhan-nho--cam' ?>">
                                    <?= e($ga->cap_hoc) ?>
                                </span>
                            </td>
                            <td class="chu-can-giua"><?= $sl ?></td>
                            <td class="chu-can-phai"><?= $ga->gia === 0 ? 'Miễn phí' : vnd($ga->gia) ?></td>
                            <td class="chu-can-phai chu-tong-tien"><?= vnd($thanhTien) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" class="chu-can-phai">Tổng thanh toán:</td>
                        <td class="chu-can-phai chu-tong-tien"><?= vnd($tongTien) ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <form method="POST" action="dat-hang.php">
            <?= csrfField() ?>
            <div class="bieu-mau-xac-thuc__nhom--cuoi">
                <label for="ghi-chu" class="bieu-mau-xac-thuc__nhan">
                    Ghi chú đơn hàng (tuỳ chọn)
                </label>
                <textarea id="ghi-chu" name="ghi_chu" rows="3"
                          placeholder="Ví dụ: Xin file Word chỉnh sửa được, hoặc xuất hóa đơn cho nhà trường..."
                          class="bieu-mau-xac-thuc__o-nhap"></textarea>
            </div>

            <div class="khung-chuyen-trang-hang">
                <a href="gio-hang.php" class="bieu-mau-xac-thuc__lien-ket">&larr; Quay lại giỏ hàng</a>
                <button type="submit" class="nut-thao-tac nut-thao-tac--xem">
                    Xác nhận đặt đơn hàng
                </button>
            </div>
        </form>
    </div>
</main>

<?php require_once __DIR__ . '/inc/footer.php'; ?>
</body>
</html>

