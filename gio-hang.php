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

// Xử lý các action POST (them, cap_nhat, xoa, xoa_het)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = chuoi($_POST['action'] ?? '');
    $id = filter_var($_POST['id'] ?? 0, FILTER_VALIDATE_INT) ?: 0;

    if ($action === 'them' && $id > 0) {
        $sl = filter_var($_POST['sl'] ?? 1, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) ?: 1;
        $ga = $kho->timTheoId($id);
        if ($ga) {
            $slHienTai = $dsLuu->soLuong($id);
            if ($slHienTai + $sl > $ga->ton_kho) {
                $_SESSION['loi'] = "Giáo án '{$ga->ten}' chỉ còn {$ga->ton_kho} lượt trong kho.";
            } else {
                $dsLuu->them($id, $sl);
            }
        }
    } elseif ($action === 'cap_nhat' && $id > 0) {
        $sl = filter_var($_POST['sl'] ?? 1, FILTER_VALIDATE_INT);
        $ga = $kho->timTheoId($id);
        if ($sl === false || $sl <= 0) {
            $dsLuu->xoa($id);
        } elseif ($ga && $sl > $ga->ton_kho) {
            $_SESSION['loi'] = "Giáo án '{$ga->ten}' chỉ còn tối đa {$ga->ton_kho} lượt trong kho.";
        } elseif ($ga) {
            $dsLuu->capNhat($id, $sl);
            $_SESSION['thong_bao'] = "Đã cập nhật số lượng cho '{$ga->ten}'.";
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

$thongBao = $_SESSION['thong_bao'] ?? '';
$loi      = $_SESSION['loi'] ?? '';
unset($_SESSION['thong_bao'], $_SESSION['loi']);

// Lấy danh sách ID từ session
$ids = $dsLuu->tatCa();

// CHỐNG LỖI N+1 TRUY VẤN: Đọc toàn bộ giỏ hàng chỉ bằng 1 CÂU SQL DUY NHẤT: WHERE id IN (?, ?, ...)
$dsGiaoAn = !empty($ids) ? $kho->theoDanhSachId($ids) : [];

// Tính tổng tiền giỏ hàng theo số lượng từng mục (đồng bộ với dat-hang.php)
$tongTien = 0;
foreach ($dsGiaoAn as $ga) {
    $sl = max(1, $dsLuu->soLuong($ga->id));
    $tongTien += $ga->gia * $sl;
}

$tieu_de_trang  = 'Giỏ hàng / Giáo án đã lưu';
$meta_mo_ta     = 'Danh sách giáo án bạn đã lưu trên ITeduShare.';
$trang_hien_tai = 'gio-hang';

require_once 'inc/header.php';
?>

<main class="trang__chinh">
    <h1 class="can-giua-chu">Danh sách giáo án trong giỏ hàng</h1>
    <p class="can-giua-chu gio-hang__mo-ta">
        Trang quản lý các tài liệu bạn dự định tải xuống hoặc đặt mua. 
        Bạn đang có <strong><?= count($dsGiaoAn) ?></strong> giáo án trong giỏ hàng.
    </p>

    <?php if ($thongBao !== ''): ?>
        <div role="status" class="hop-thong-bao hop-thong-bao--thanh-cong hop-thong-bao--rong">
            <?= e($thongBao) ?>
        </div>
    <?php endif; ?>

    <?php if ($loi !== ''): ?>
        <div role="alert" class="hop-thong-bao hop-thong-bao--loi hop-thong-bao--rong">
            <?= e($loi) ?>
        </div>
    <?php endif; ?>

    <?php if (empty($dsGiaoAn)): ?>
        <div class="gio-hang__rong">
            <p class="gio-hang__rong-chu">Hiện tại chưa có giáo án nào trong giỏ hàng của bạn.</p>
            <a href="kho-hoc-lieu.php" class="nut-thao-tac nut-thao-tac--xem gio-hang__rong-nut">Khám phá Kho học liệu ngay</a>
        </div>
    <?php else: ?>
        <div class="gio-hang__khung">
            <div class="gio-hang__thanh-cong-cu">
                <a href="kho-hoc-lieu.php" class="gio-hang__tiep-tuc">&larr; Tiếp tục chọn thêm giáo án</a>
                <form action="gio-hang.php" method="POST" data-confirm="Bạn có chắc chắn muốn xoá tất cả giáo án khỏi giỏ hàng?">
                    <?= csrfField() ?>
                    <input type="hidden" name="action" value="xoa_het">
                    <button type="submit" class="gio-hang__nut-xoa-het">
                        Xoá tất cả
                    </button>
                </form>
            </div>
            
            <div class="khung-cuon-bang">
                <table class="gio-hang__bang">
                    <thead>
                        <tr>
                            <th class="gio-hang__o-trai">Giáo án</th>
                            <th class="gio-hang__o-trai">Cấp học</th>
                            <th class="gio-hang__o-giua">Tồn kho</th>
                            <th class="gio-hang__o-phai">Đơn giá</th>
                            <th class="gio-hang__o-giua">Số lượng</th>
                            <th class="gio-hang__o-phai">Thành tiền</th>
                            <th class="gio-hang__o-giua">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($dsGiaoAn as $ga):
                            $sl = max(1, $dsLuu->soLuong($ga->id));
                            $thanhTien = $ga->gia * $sl;
                        ?>
                            <tr>
                                <td>
                                    <a href="chi-tiet-giao-an.php?id=<?= $ga->id ?>" class="gio-hang__ten-link">
                                        <?= e($ga->ten) ?>
                                    </a>
                                </td>
                                <td>
                                    <span class="nhan-nho <?= $ga->cap_hoc === 'THCS' ? 'nhan-nho--xanh' : ($ga->cap_hoc === 'THPT' ? 'nhan-nho--cam' : 'nhan-nho--tim') ?>">
                                        <?= e($ga->cap_hoc) ?>
                                    </span>
                                </td>
                                <td class="gio-hang__o-giua">
                                    <?= $ga->ton_kho > 0 ? "Còn {$ga->ton_kho}" : '<strong class="gio-hang__het-hang">Hết hàng</strong>' ?>
                                </td>
                                <td class="gio-hang__o-phai gio-hang__don-gia">
                                    <?= $ga->gia === 0 ? 'Miễn phí' : vnd($ga->gia) ?>
                                </td>
                                <td class="gio-hang__o-giua">
                                    <form action="gio-hang.php" method="POST" class="gio-hang__form-sl">
                                        <?= csrfField() ?>
                                        <input type="hidden" name="action" value="cap_nhat">
                                        <input type="hidden" name="id" value="<?= $ga->id ?>">
                                        <input type="number" name="sl" value="<?= $sl ?>" min="1" max="<?= max(1, $ga->ton_kho) ?>"
                                               aria-label="Số lượng cho <?= e($ga->ten) ?>"
                                               class="gio-hang__input-sl">
                                        <button type="submit" class="gio-hang__nut-cap-nhat">
                                            Cập nhật
                                        </button>
                                    </form>
                                </td>
                                <td class="gio-hang__o-phai gio-hang__thanh-tien">
                                    <?= $thanhTien === 0 ? 'Miễn phí' : vnd($thanhTien) ?>
                                </td>
                                <td class="gio-hang__o-giua">
                                    <form action="gio-hang.php" method="POST" class="gio-hang__form-xoa">
                                        <?= csrfField() ?>
                                        <input type="hidden" name="action" value="xoa">
                                        <input type="hidden" name="id" value="<?= $ga->id ?>">
                                        <button type="submit" class="gio-hang__nut-xoa" title="Xoá mục này">
                                            Xoá
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr class="gio-hang__hang-tong">
                            <td colspan="5" class="gio-hang__o-phai gio-hang__nhan-tong">Tổng cộng thanh toán:</td>
                            <td class="gio-hang__o-phai gio-hang__gia-tong">
                                <?= vnd($tongTien) ?>
                            </td>
                            <td class="gio-hang__o-giua">
                                <a href="dat-hang.php" class="nut-thao-tac nut-thao-tac--xem gio-hang__nut-dat-hang">
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