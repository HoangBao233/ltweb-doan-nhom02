<?php
/**
 * gio-hang.php — Trang danh sách giáo án đã lưu (Chức năng 5 - Bảo & Thịnh)
 */
require_once 'inc/config.php';
use App\Services\DanhSachLuu;
use App\Data\KhoGiaoAn;

$dsLuu = new DanhSachLuu();
$kho = new KhoGiaoAn(__DIR__ . '/data/giao-an.json');

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
    
    // Quay lại trang trước đó (dù là trang chi tiết hay trang giỏ hàng)
    $referer = $_SERVER['HTTP_REFERER'] ?? 'gio-hang.php';
    header("Location: $referer");
    exit;
}

$tieu_de_trang  = 'Giáo án đã lưu';
$meta_mo_ta     = 'Danh sách giáo án bạn đã lưu trên ITeduShare.';
$trang_hien_tai = 'gio-hang';

require_once 'inc/header.php';
?>

<main class="trang__chinh">
    <style>
    /* Cố định các hàng Grid bằng độ đặc hiệu chuẩn (specificity), hoàn toàn không dùng !important */
    body.trang {
        grid-template-rows: auto auto 1fr auto;
        align-content: start;
    }
    header.trang__dau {
        align-self: start;
    }
    nav.trang__dieu-huong {
        align-self: start;
    }

    /* Đồng bộ căn chỉnh thanh menu và huy hiệu số lượng */
    nav .menu-chinh {
        align-items: center;
    }
    nav .menu-chinh a {
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    nav .menu-chinh .huy-hieu-so-luong {
        line-height: 1;
    }
    </style>

    <h1 class="can-giua-chu">🔖 Danh sách giáo án đã lưu</h1>
    <p class="can-giua-chu" style="color: #666; margin-bottom: 2rem;">
        Trang quản lý các bài giảng và giáo án yêu thích của bạn. 
        Bạn đang có <strong><?= $dsLuu->soMon() ?></strong> giáo án trong danh sách.
    </p>

    <?php 
    $ids = $dsLuu->tatCa();
    if (empty($ids)): 
    ?>
        <div style="max-width: 800px; margin: 0 auto; padding: 2rem; background: #f9f9f9; border-radius: 8px; text-align: center; border: 1px dashed #ccc;">
            <p style="font-size: 1.1rem; color: #555; margin-bottom: 1rem;">Hiện tại chưa có giáo án nào được lưu trong danh sách của bạn.</p>
            <a href="kho-hoc-lieu.php" class="nut-thao-tac nut-thao-tac--xem" style="display: inline-block; text-decoration: none;">Khám phá Kho học liệu ngay</a>
        </div>
    <?php else: ?>
        <div style="max-width: 1000px; margin: 0 auto;">
            <div style="text-align: right; margin-bottom: 1rem;">
                <form action="gio-hang.php" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xoá tất cả?');">
                    <input type="hidden" name="action" value="xoa_het">
                    <button type="submit" style="background: #dc3545; color: white; border: none; padding: 0.5rem 1rem; border-radius: 4px; cursor: pointer;">🗑️ Xoá tất cả</button>
                </form>
            </div>
            
            <table style="width: 100%; border-collapse: collapse; margin-bottom: 2rem;">
                <thead>
                    <tr style="background: var(--nen-phu, #f4f6f8); border-bottom: 2px solid #ddd;">
                        <th style="padding: 1rem; text-align: left;">Giáo án</th>
                        <th style="padding: 1rem; text-align: left;">Cấp học</th>
                        <th style="padding: 1rem; text-align: right;">Giá</th>
                        <th style="padding: 1rem; text-align: center;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $tongTien = 0;
                    foreach ($ids as $id):
                        $ga = $kho->timTheoId($id);
                        if ($ga):
                            $tongTien += $ga->gia;
                    ?>
                        <tr style="border-bottom: 1px solid #ddd;">
                            <td style="padding: 1rem;">
                                <a href="chi-tiet-giao-an.php?id=<?= $ga->id ?>" style="font-weight: bold; text-decoration: none; color: var(--mau-chinh);">
                                    <?= e($ga->ten) ?>
                                </a>
                            </td>
                            <td style="padding: 1rem;"><?= e($ga->cap_hoc) ?></td>
                            <td style="padding: 1rem; text-align: right; color: var(--mau-nhan); font-weight: bold;">
                                <?= $ga->gia === 0 ? 'Miễn phí' : vnd($ga->gia) ?>
                            </td>
                            <td style="padding: 1rem; text-align: center;">
                                <form action="gio-hang.php" method="POST" style="margin: 0; display: inline-block;">
                                    <input type="hidden" name="action" value="xoa">
                                    <input type="hidden" name="id" value="<?= $ga->id ?>">
                                    <button type="submit" style="background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; padding: 0.3rem 0.6rem; border-radius: 4px; cursor: pointer; font-size: 0.9rem;">Xoá</button>
                                </form>
                            </td>
                        </tr>
                    <?php 
                        endif;
                    endforeach; 
                    ?>
                </tbody>
                <tfoot>
                    <tr style="background: #e9ecef; font-weight: bold;">
                        <td colspan="2" style="padding: 1rem; text-align: right;">Tổng ước lượng:</td>
                        <td style="padding: 1rem; text-align: right; color: #d32f2f; font-size: 1.1rem;">
                            <?= vnd($tongTien) ?>
                        </td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    <?php endif; ?>
</main>

<?php require_once 'inc/footer.php'; ?>
    <script type="module" src="js/main.js"></script>
</body>
</html>
