<?php
/**
 * dat-hang.php — Trang xác nhận và xử lý đặt hàng / đăng ký tải giáo án
 * Đồ án Lập trình Web - Nhóm 02
 * Phụ trách: Nguyễn Tiến Thịnh (MSSV: 3120222126) & Lê Hoàng Bảo — Chức năng 5
 */

require_once __DIR__ . '/inc/config.php';
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
    $ghiChu = trim($_POST['ghi_chu'] ?? '');

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

<main class="trang__chinh" style="max-width: 800px; margin: 3rem auto; padding: 0 1rem;">
    <h1 style="color: var(--mau-chinh, #004085); margin-bottom: 0.5rem; text-align: center;">🛍️ Xác nhận đặt giáo án</h1>
    <p style="text-align: center; color: #666; margin-bottom: 2rem;">Vui lòng kiểm tra lại danh sách học liệu và thông tin người nhận</p>

    <?php if ($loiDatHang !== ''): ?>
        <div role="alert" aria-live="assertive" style="background: #fff3f3; border: 1px solid #e74c3c; border-radius: 8px; padding: 1.25rem; margin-bottom: 2rem; color: #c0392b;">
            <strong>⚠️ Không thể hoàn tất đơn hàng:</strong> <?= e($loiDatHang) ?>
        </div>
    <?php endif; ?>

    <div style="background: #fff; border: 1px solid #ddd; border-radius: 12px; padding: 2rem; box-shadow: 0 4px 12px rgba(0,0,0,0.05); margin-bottom: 2rem;">
        <h2 style="font-size: 1.2rem; margin-bottom: 1rem; border-bottom: 1px solid #eee; padding-bottom: 0.5rem; color: #333;">
            👤 Thông tin người đặt
        </h2>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
            <div>
                <span style="color: #666; font-size: 0.9rem;">Họ và tên:</span>
                <p style="margin: 0.25rem 0 0 0; font-weight: bold;"><?= e($u['ho_ten']) ?></p>
            </div>
            <div>
                <span style="color: #666; font-size: 0.9rem;">Email nhận tài liệu:</span>
                <p style="margin: 0.25rem 0 0 0; font-weight: bold;"><?= e($u['email']) ?></p>
            </div>
        </div>

        <h2 style="font-size: 1.2rem; margin-bottom: 1rem; border-bottom: 1px solid #eee; padding-bottom: 0.5rem; color: #333;">
            📚 Danh sách giáo án đăng ký tải
        </h2>
        <div class="khung-cuon-bang" style="overflow-x: auto; margin-bottom: 1.5rem;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                        <th style="padding: 0.75rem; text-align: left;">Tên giáo án</th>
                        <th style="padding: 0.75rem; text-align: center;">Số lượng</th>
                        <th style="padding: 0.75rem; text-align: right;">Đơn giá</th>
                        <th style="padding: 0.75rem; text-align: right;">Thành tiền</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($dsGiaoAn as $ga): 
                        $sl = $gioHang[$ga->id] ?? 1;
                        $thanhTien = $ga->gia * $sl;
                    ?>
                        <tr style="border-bottom: 1px solid #eee;">
                            <td style="padding: 0.75rem; font-weight: 500;">
                                <?= e($ga->ten) ?>
                                <span class="nhan-nho <?= $ga->cap_hoc === 'THCS' ? 'nhan-nho--xanh' : 'nhan-nho--cam' ?>" style="font-size: 0.75rem; margin-left: 0.5rem;">
                                    <?= e($ga->cap_hoc) ?>
                                </span>
                            </td>
                            <td style="padding: 0.75rem; text-align: center;"><?= $sl ?></td>
                            <td style="padding: 0.75rem; text-align: right;"><?= $ga->gia === 0 ? 'Miễn phí' : vnd($ga->gia) ?></td>
                            <td style="padding: 0.75rem; text-align: right; font-weight: bold; color: var(--mau-nhan, #e65100);"><?= vnd($thanhTien) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr style="background: #fdfdfe; font-weight: bold; font-size: 1.1rem; border-top: 2px solid #dee2e6;">
                        <td colspan="3" style="padding: 1rem; text-align: right;">Tổng thanh toán:</td>
                        <td style="padding: 1rem; text-align: right; color: #d32f2f;"><?= vnd($tongTien) ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <form method="POST" action="dat-hang.php">
            <div style="margin-bottom: 1.5rem;">
                <label for="ghi-chu" style="display: block; font-weight: 600; margin-bottom: 0.5rem; color: #333;">
                    Ghi chú đơn hàng (tuỳ chọn)
                </label>
                <textarea id="ghi-chu" name="ghi_chu" rows="3"
                          placeholder="Ví dụ: Xin file Word chỉnh sửa được, hoặc xuất hóa đơn cho nhà trường..."
                          style="width: 100%; padding: 0.75rem; border: 1px solid #ccc; border-radius: 6px; box-sizing: border-box; font-family: inherit; font-size: 0.95rem;"></textarea>
            </div>

            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                <a href="gio-hang.php" style="color: #666; text-decoration: none;">&larr; Quay lại giỏ hàng</a>
                <button type="submit" class="nut-thao-tac nut-thao-tac--xem"
                        style="padding: 0.85rem 2rem; font-size: 1.05rem; font-weight: bold; border-radius: 6px; cursor: pointer;">
                    🚀 Xác nhận đặt đơn hàng
                </button>
            </div>
        </form>
    </div>
</main>

<?php require_once __DIR__ . '/inc/footer.php'; ?>
</body>
</html>

