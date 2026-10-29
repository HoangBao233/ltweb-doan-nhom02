<?php
/**
 * gio-hang.php — Trang danh sách giáo án đã lưu / Giỏ hàng
 * 
 * Đồ án Lập trình Web - Nhóm 02
 * Phụ trách: Lê Hoàng Bảo (MSSV: 3120124003) — Phần A, Chức năng 1
 */

require_once 'inc/config.php';
use App\Services\DanhSachLuu;
use App\Data\KhoGiaoAn;

$dsLuu = new DanhSachLuu();
$kho   = new KhoGiaoAn();

// Xử lý các action POST (them, xoa, xoa_het)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    
    if ($action === 'them' && $id > 0) {
        if ($kho->timTheoId($id)) {
            $dsLuu->them($id);
        }
    } elseif ($action === 'xoa' && $id > 0) {
        $dsLuu->xoa($id);
    } elseif ($action === 'xoa_het') {
        $dsLuu->xoaHet();
    }
    
    // Áp dụng PRG (Post-Redirect-Get) quay lại trang trước
    $referer = $_SERVER['HTTP_REFERER'] ?? 'gio-hang.php';
    chuyen_trang($referer);
}

// Lấy danh sách ID từ session
$ids = $dsLuu->tatCa();

// CHỐNG LỖI N+1 TRUY VẤN: Đọc toàn bộ giỏ hàng chỉ bằng 1 CÂU SQL DUY NHẤT: WHERE id IN (?, ?, ...)
$dsGiaoAn = !empty($ids) ? $kho->theoDanhSachId($ids) : [];

// Tính tổng tiền giỏ hàng
$tongTien = 0;
foreach ($dsGiaoAn as $ga) {
    $tongTien += $ga->gia;
}

$tieu_de_trang  = 'Giỏ hàng / Giáo án đã lưu';
$meta_mo_ta     = 'Danh sách giáo án bạn đã lưu trên ITeduShare.';
$trang_hien_tai = 'gio-hang';

require_once 'inc/header.php';
?>

<main class="trang__chinh">
    <h1 class="can-giua-chu">🔖 Danh sách giáo án trong giỏ hàng</h1>
    <p class="can-giua-chu" style="color: #666; margin-bottom: 2rem;">
        Trang quản lý các tài liệu bạn dự định tải xuống hoặc đặt mua. 
        Bạn đang có <strong><?= count($dsGiaoAn) ?></strong> giáo án trong giỏ hàng.
    </p>

    <?php if (empty($dsGiaoAn)): ?>
        <div style="max-width: 800px; margin: 0 auto; padding: 2rem; background: var(--nen-phu, #f9f9f9); border-radius: 8px; text-align: center; border: 1px dashed var(--vien, #ccc);">
            <p style="font-size: 1.1rem; color: #555; margin-bottom: 1.5rem;">Hiện tại chưa có giáo án nào trong giỏ hàng của bạn.</p>
            <a href="kho-hoc-lieu.php" class="nut-thao-tac nut-thao-tac--xem" style="display: inline-block; text-decoration: none;">Khám phá Kho học liệu ngay</a>
        </div>
    <?php else: ?>
        <div style="max-width: 1000px; margin: 0 auto;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; flex-wrap: wrap; gap: 0.5rem;">
                <a href="kho-hoc-lieu.php" style="text-decoration: none; color: var(--mau-chinh); font-weight: bold;">&larr; Tiếp tục chọn thêm giáo án</a>
                <form action="gio-hang.php" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xoá tất cả giáo án khỏi giỏ hàng?');">
                    <input type="hidden" name="action" value="xoa_het">
                    <button type="submit" style="background: #dc3545; color: white; border: none; padding: 0.5rem 1rem; border-radius: 4px; cursor: pointer; font-size: 0.9rem;">
                        🗑️ Xoá tất cả
                    </button>
                </form>
            </div>
            
            <div class="khung-cuon-bang">
                <table style="width: 100%; border-collapse: collapse; margin-bottom: 2rem;">
                    <thead>
                        <tr style="background: var(--nen-phu, #f4f6f8); border-bottom: 2px solid #ddd;">
                            <th style="padding: 1rem; text-align: left;">Giáo án</th>
                            <th style="padding: 1rem; text-align: left;">Cấp học</th>
                            <th style="padding: 1rem; text-align: center;">Tồn kho</th>
                            <th style="padding: 1rem; text-align: right;">Đơn giá</th>
                            <th style="padding: 1rem; text-align: center;">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($dsGiaoAn as $ga): ?>
                            <tr style="border-bottom: 1px solid #ddd;">
                                <td style="padding: 1rem;">
                                    <a href="chi-tiet-giao-an.php?id=<?= $ga->id ?>" style="font-weight: bold; text-decoration: none; color: var(--mau-chinh);">
                                        <?= e($ga->ten) ?>
                                    </a>
                                </td>
                                <td style="padding: 1rem;">
                                    <span class="nhan-nho <?= $ga->cap_hoc === 'THCS' ? 'nhan-nho--xanh' : ($ga->cap_hoc === 'THPT' ? 'nhan-nho--cam' : 'nhan-nho--tim') ?>">
                                        <?= e($ga->cap_hoc) ?>
                                    </span>
                                </td>
                                <td style="padding: 1rem; text-align: center;">
                                    <?= $ga->ton_kho > 0 ? "Còn {$ga->ton_kho}" : '<strong style="color: red;">Hết hàng</strong>' ?>
                                </td>
                                <td style="padding: 1rem; text-align: right; color: var(--mau-nhan, #e65100); font-weight: bold;">
                                    <?= $ga->gia === 0 ? 'Miễn phí' : vnd($ga->gia) ?>
                                </td>
                                <td style="padding: 1rem; text-align: center;">
                                    <form action="gio-hang.php" method="POST" style="margin: 0; display: inline-block;">
                                        <input type="hidden" name="action" value="xoa">
                                        <input type="hidden" name="id" value="<?= $ga->id ?>">
                                        <button type="submit" style="background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; padding: 0.35rem 0.75rem; border-radius: 4px; cursor: pointer; font-size: 0.85rem;" title="Xoá mục này">
                                            Xoá
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr style="background: #e9ecef; font-weight: bold;">
                            <td colspan="3" style="padding: 1rem; text-align: right; font-size: 1.05rem;">Tổng cộng thanh toán:</td>
                            <td style="padding: 1rem; text-align: right; color: #d32f2f; font-size: 1.25rem;">
                                <?= vnd($tongTien) ?>
                            </td>
                            <td style="padding: 1rem; text-align: center;">
                                <a href="dat-hang.php" class="nut-thao-tac nut-thao-tac--xem" style="text-decoration: none; padding: 0.5rem 1rem; display: inline-block; white-space: nowrap;">
                                    Đặt hàng ngay &rarr;
                                </a>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    <?php endif; ?>
</main>

<?php require_once 'inc/footer.php'; ?>
    <script type="module" src="js/main.js"></script>
</body>
</html>
