<?php
/**
 * don-hang.php — Quản lý và xem danh sách / chi tiết đơn hàng
 * Đồ án Lập trình Web - Nhóm 02
 * Phụ trách: Nguyễn Tiến Thịnh (MSSV: 3120222126) & Lê Hoàng Bảo — Chức năng 5
 */

require_once __DIR__ . '/inc/config.php';
require_once __DIR__ . '/inc/ham.php';

// Bắt buộc phải đăng nhập (Ca 8)
yeuCauDangNhap();

$u = nguoiDung();
$isAdmin = (($u['vai_tro'] ?? '') === 'quan_tri');

$thongBao = $_SESSION['thong_bao'] ?? '';
unset($_SESSION['thong_bao']);

$loi = $_SESSION['loi'] ?? '';
unset($_SESSION['loi']);

// Xử lý Admin cập nhật trạng thái đơn hàng (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $isAdmin && isset($_POST['cap_nhat_trang_thai'])) {
    $donId = filter_var($_POST['don_hang_id'] ?? 0, FILTER_VALIDATE_INT);
    $trangThaiMoi = $_POST['trang_thai'] ?? '';

    // Whitelist trạng thái hợp lệ
    $trangThaiHopLe = ['cho_duyet', 'da_giao', 'da_huy'];

    if ($donId && in_array($trangThaiMoi, $trangThaiHopLe, true)) {
        try {
            $st = db()->prepare('UPDATE don_hang SET trang_thai = ? WHERE id = ?');
            $st->execute([$trangThaiMoi, $donId]);
            $_SESSION['thong_bao'] = "Đã cập nhật trạng thái đơn hàng #{$donId} thành công.";
        } catch (Throwable $e) {
            error_log("Lỗi cập nhật đơn hàng: " . $e->getMessage());
            $_SESSION['loi'] = "Không thể cập nhật trạng thái đơn hàng.";
        }
    }

    $chuyenHuong = isset($_GET['id']) ? "don-hang.php?id={$donId}" : 'don-hang.php';
    header("Location: {$chuyenHuong}");
    exit;
}

// ============================================================================
// TRƯỜNG HỢP 1: XEM CHI TIẾT 1 ĐƠN HÀNG (?id=...)
// ============================================================================
if (isset($_GET['id'])) {
    $donHangId = filter_var($_GET['id'], FILTER_VALIDATE_INT);

    if ($donHangId === false || $donHangId <= 0) {
        http_response_code(404);
        require __DIR__ . '/404.php';
        exit;
    }

    // Truy vấn thông tin đơn hàng
    $st = db()->prepare('
        SELECT dh.*, nd.ho_ten, nd.email 
        FROM don_hang dh 
        JOIN nguoi_dung nd ON dh.nguoi_dung_id = nd.id 
        WHERE dh.id = ? 
        LIMIT 1
    ');
    $st->execute([$donHangId]);
    $donHang = $st->fetch();

    // BẢO MẬT CA 8: Nếu đơn hàng không tồn tại, HOẶC khách hàng A xem đơn của khách hàng B -> Báo 404 (không làm lộ dữ liệu)
    if (!$donHang || (!$isAdmin && (int)$donHang['nguoi_dung_id'] !== (int)$u['id'])) {
        http_response_code(404);
        require __DIR__ . '/404.php';
        exit;
    }

    // Truy vấn các giáo án trong đơn hàng từ bảng chi_tiet_don
    $stItems = db()->prepare('
        SELECT ct.*, ga.ten, ga.cap_hoc, ga.hinh_anh
        FROM chi_tiet_don ct
        JOIN giao_an ga ON ct.giao_an_id = ga.id
        WHERE ct.don_hang_id = ?
    ');
    $stItems->execute([$donHangId]);
    $dsChiTiet = $stItems->fetchAll();

    $tieuDe = "Chi tiết đơn hàng #{$donHangId} | ITeduShare";
    $trang  = 'don-hang';
    require_once __DIR__ . '/inc/header.php';
    ?>

    <main class="trang__chinh" style="max-width: 900px; margin: 3rem auto; padding: 0 1rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
            <h1 style="margin: 0; color: var(--mau-chinh, #004085);">📦 Chi tiết đơn hàng #<?= (int)$donHang['id'] ?></h1>
            <a href="don-hang.php" style="text-decoration: none; color: var(--mau-chinh, #007bff); font-weight: bold;">&larr; Về danh sách đơn hàng</a>
        </div>

        <?php if ($thongBao !== ''): ?>
            <div role="status" style="background: #e8f5e9; border: 1px solid #4caf50; border-radius: 8px; padding: 1rem; margin-bottom: 1.5rem; color: #2e7d32;">
                ✅ <?= e($thongBao) ?>
            </div>
        <?php endif; ?>

        <?php if ($loi !== ''): ?>
            <div role="alert" style="background: #fff3f3; border: 1px solid #e74c3c; border-radius: 8px; padding: 1rem; margin-bottom: 1.5rem; color: #c0392b;">
                ⚠️ <?= e($loi) ?>
            </div>
        <?php endif; ?>

        <div style="background: #fff; border: 1px solid #ddd; border-radius: 12px; padding: 2rem; box-shadow: 0 4px 12px rgba(0,0,0,0.05); margin-bottom: 2rem;">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.5rem; margin-bottom: 1.5rem; border-bottom: 1px solid #eee; padding-bottom: 1.5rem;">
                <div>
                    <span style="color: #666; font-size: 0.85rem;">Ngày đặt hàng:</span>
                    <p style="margin: 0.25rem 0 0 0; font-weight: 600;"><?= e($donHang['ngay_tao']) ?></p>
                </div>
                <div>
                    <span style="color: #666; font-size: 0.85rem;">Người nhận:</span>
                    <p style="margin: 0.25rem 0 0 0; font-weight: 600;"><?= e($donHang['ho_ten']) ?> (<?= e($donHang['email']) ?>)</p>
                </div>
                <div>
                    <span style="color: #666; font-size: 0.85rem;">Trạng thái đơn:</span>
                    <p style="margin: 0.25rem 0 0 0;">
                        <?php if ($donHang['trang_thai'] === 'da_giao'): ?>
                            <span style="background: #d4edda; color: #155724; padding: 0.2rem 0.6rem; border-radius: 4px; font-weight: bold; font-size: 0.9rem;">✅ Đã giao / Hoàn tất</span>
                        <?php elseif ($donHang['trang_thai'] === 'da_huy'): ?>
                            <span style="background: #f8d7da; color: #721c24; padding: 0.2rem 0.6rem; border-radius: 4px; font-weight: bold; font-size: 0.9rem;">❌ Đã hủy</span>
                        <?php else: ?>
                            <span style="background: #fff3cd; color: #856404; padding: 0.2rem 0.6rem; border-radius: 4px; font-weight: bold; font-size: 0.9rem;">⏳ Chờ duyệt</span>
                        <?php endif; ?>
                    </p>
                </div>
            </div>

            <?php if (!empty($donHang['ghi_chu'])): ?>
                <div style="background: #f8f9fa; border-left: 4px solid var(--mau-chinh, #007bff); padding: 0.75rem 1rem; margin-bottom: 1.5rem; border-radius: 0 6px 6px 0;">
                    <strong style="font-size: 0.9rem; color: #555;">Ghi chú của người đặt:</strong>
                    <p style="margin: 0.25rem 0 0 0; color: #333;"><?= e($donHang['ghi_chu']) ?></p>
                </div>
            <?php endif; ?>

            <h2 style="font-size: 1.15rem; margin-bottom: 1rem; color: #333;">Danh sách học liệu trong đơn</h2>
            <div class="khung-cuon-bang" style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                            <th style="padding: 0.75rem; text-align: left;">Giáo án</th>
                            <th style="padding: 0.75rem; text-align: center;">Cấp học</th>
                            <th style="padding: 0.75rem; text-align: center;">Số lượng</th>
                            <th style="padding: 0.75rem; text-align: right;">Đơn giá lúc mua</th>
                            <th style="padding: 0.75rem; text-align: right;">Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($dsChiTiet as $item): 
                            $thanhTien = $item['don_gia'] * $item['so_luong'];
                        ?>
                            <tr style="border-bottom: 1px solid #eee;">
                                <td style="padding: 0.75rem;">
                                    <a href="chi-tiet-giao-an.php?id=<?= $item['giao_an_id'] ?>" style="font-weight: 600; text-decoration: none; color: var(--mau-chinh, #007bff);">
                                        <?= e($item['ten']) ?>
                                    </a>
                                </td>
                                <td style="padding: 0.75rem; text-align: center;">
                                    <span class="nhan-nho <?= $item['cap_hoc'] === 'THCS' ? 'nhan-nho--xanh' : 'nhan-nho--cam' ?>">
                                        <?= e($item['cap_hoc']) ?>
                                    </span>
                                </td>
                                <td style="padding: 0.75rem; text-align: center;"><?= $item['so_luong'] ?></td>
                                <td style="padding: 0.75rem; text-align: right;"><?= $item['don_gia'] === 0 ? 'Miễn phí' : vnd($item['don_gia']) ?></td>
                                <td style="padding: 0.75rem; text-align: right; font-weight: bold; color: var(--mau-nhan, #e65100);"><?= vnd($thanhTien) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr style="background: #fdfdfe; font-weight: bold; font-size: 1.15rem; border-top: 2px solid #dee2e6;">
                            <td colspan="4" style="padding: 1rem; text-align: right;">Tổng thanh toán:</td>
                            <td style="padding: 1rem; text-align: right; color: #d32f2f;"><?= vnd($donHang['tong_tien']) ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Nếu là Quản trị viên: Hiển thị form cập nhật trạng thái đơn hàng -->
            <?php if ($isAdmin): ?>
                <div style="margin-top: 2rem; padding-top: 1.5rem; border-top: 1px dashed #ced4da;">
                    <form method="POST" action="don-hang.php?id=<?= (int)$donHang['id'] ?>" style="display: flex; align-items: center; gap: 1rem; flex-wrap: wrap;">
                        <input type="hidden" name="cap_nhat_trang_thai" value="1">
                        <input type="hidden" name="don_hang_id" value="<?= (int)$donHang['id'] ?>">
                        <label for="trang_thai" style="font-weight: bold; color: #333;">🛠️ Cập nhật trạng thái đơn:</label>
                        <select id="trang_thai" name="trang_thai" style="padding: 0.5rem 1rem; border-radius: 4px; border: 1px solid #ccc; font-size: 0.95rem;">
                            <option value="cho_duyet" <?= $donHang['trang_thai'] === 'cho_duyet' ? 'selected' : '' ?>>⏳ Chờ duyệt</option>
                            <option value="da_giao" <?= $donHang['trang_thai'] === 'da_giao' ? 'selected' : '' ?>>✅ Đã giao / Hoàn tất</option>
                            <option value="da_huy" <?= $donHang['trang_thai'] === 'da_huy' ? 'selected' : '' ?>>❌ Đã hủy</option>
                        </select>
                        <button type="submit" class="nut-thao-tac nut-thao-tac--xem" style="padding: 0.5rem 1.25rem;">
                            Lưu trạng thái
                        </button>
                    </form>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <?php require_once __DIR__ . '/inc/footer.php'; ?>
    </body>
    </html>
    <?php
    exit;
}

// ============================================================================
// TRƯỜNG HỢP 2: XEM DANH SÁCH ĐƠN HÀNG
// ============================================================================

if ($isAdmin) {
    // Quản trị viên xem tất cả đơn hàng trong hệ thống
    $st = db()->query('
        SELECT dh.*, nd.ho_ten, nd.email 
        FROM don_hang dh 
        JOIN nguoi_dung nd ON dh.nguoi_dung_id = nd.id 
        ORDER BY dh.id DESC
    ');
    $danhSachDon = $st->fetchAll();
    $tieuDe = 'Quản lý đơn hàng toàn hệ thống | ITeduShare';
} else {
    // Khách hàng chỉ xem danh sách đơn hàng của chính mình (WHERE nguoi_dung_id = ?)
    $st = db()->prepare('
        SELECT dh.*, nd.ho_ten, nd.email 
        FROM don_hang dh 
        JOIN nguoi_dung nd ON dh.nguoi_dung_id = nd.id 
        WHERE dh.nguoi_dung_id = ? 
        ORDER BY dh.id DESC
    ');
    $st->execute([(int)$u['id']]);
    $danhSachDon = $st->fetchAll();
    $tieuDe = 'Đơn hàng của tôi | ITeduShare';
}

$trang = 'don-hang';
require_once __DIR__ . '/inc/header.php';
?>

<main class="trang__chinh" style="max-width: 1000px; margin: 3rem auto; padding: 0 1rem;">
    <h1 style="color: var(--mau-chinh, #004085); margin-bottom: 0.5rem;">
        <?= $isAdmin ? '🛠️ Quản lý đơn hàng (Toàn hệ thống)' : '📦 Đơn hàng của tôi' ?>
    </h1>
    <p style="color: #666; margin-bottom: 2rem;">
        <?= $isAdmin ? 'Xem và phê duyệt các yêu cầu đặt mua / tải giáo án của giáo viên và khách hàng' : 'Xem lại lịch sử các giáo án bạn đã đăng ký tải và mua trên hệ thống' ?>
    </p>

    <?php if ($thongBao !== ''): ?>
        <div role="status" style="background: #e8f5e9; border: 1px solid #4caf50; border-radius: 8px; padding: 1rem; margin-bottom: 1.5rem; color: #2e7d32;">
            ✅ <?= e($thongBao) ?>
        </div>
    <?php endif; ?>

    <?php if ($loi !== ''): ?>
        <div role="alert" style="background: #fff3f3; border: 1px solid #e74c3c; border-radius: 8px; padding: 1rem; margin-bottom: 1.5rem; color: #c0392b;">
            ⚠️ <?= e($loi) ?>
        </div>
    <?php endif; ?>

    <?php if (empty($danhSachDon)): ?>
        <div style="background: #f8f9fa; border: 1px dashed #ced4da; border-radius: 12px; padding: 3rem 1rem; text-align: center;">
            <p style="font-size: 1.1rem; color: #666; margin-bottom: 1.5rem;">
                <?= $isAdmin ? 'Hiện chưa có đơn hàng nào trong hệ thống.' : 'Bạn chưa có đơn đặt giáo án nào.' ?>
            </p>
            <a href="kho-hoc-lieu.php" class="nut-thao-tac nut-thao-tac--xem" style="text-decoration: none;">
                Khám phá Kho học liệu ngay
            </a>
        </div>
    <?php else: ?>
        <div class="khung-cuon-bang" style="background: #fff; border: 1px solid #ddd; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.05);">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #f4f6f8; border-bottom: 2px solid #dee2e6;">
                        <th style="padding: 1rem; text-align: center;">Mã đơn</th>
                        <?php if ($isAdmin): ?>
                            <th style="padding: 1rem; text-align: left;">Khách hàng</th>
                        <?php endif; ?>
                        <th style="padding: 1rem; text-align: left;">Ngày đặt</th>
                        <th style="padding: 1rem; text-align: right;">Tổng thanh toán</th>
                        <th style="padding: 1rem; text-align: center;">Trạng thái</th>
                        <th style="padding: 1rem; text-align: center;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($danhSachDon as $don): ?>
                        <tr style="border-bottom: 1px solid #eee;">
                            <td style="padding: 1rem; text-align: center; font-weight: bold;">
                                #<?= (int)$don['id'] ?>
                            </td>
                            <?php if ($isAdmin): ?>
                                <td style="padding: 1rem;">
                                    <strong><?= e($don['ho_ten']) ?></strong><br>
                                    <small style="color: #666;"><?= e($don['email']) ?></small>
                                </td>
                            <?php endif; ?>
                            <td style="padding: 1rem; color: #555;">
                                <?= e($don['ngay_tao']) ?>
                            </td>
                            <td style="padding: 1rem; text-align: right; font-weight: bold; color: #d32f2f;">
                                <?= vnd($don['tong_tien']) ?>
                            </td>
                            <td style="padding: 1rem; text-align: center;">
                                <?php if ($don['trang_thai'] === 'da_giao'): ?>
                                    <span style="background: #d4edda; color: #155724; padding: 0.2rem 0.6rem; border-radius: 4px; font-weight: bold; font-size: 0.85rem;">✅ Đã giao</span>
                                <?php elseif ($don['trang_thai'] === 'da_huy'): ?>
                                    <span style="background: #f8d7da; color: #721c24; padding: 0.2rem 0.6rem; border-radius: 4px; font-weight: bold; font-size: 0.85rem;">❌ Đã hủy</span>
                                <?php else: ?>
                                    <span style="background: #fff3cd; color: #856404; padding: 0.2rem 0.6rem; border-radius: 4px; font-weight: bold; font-size: 0.85rem;">⏳ Chờ duyệt</span>
                                <?php endif; ?>
                            </td>
                            <td style="padding: 1rem; text-align: center;">
                                <a href="don-hang.php?id=<?= (int)$don['id'] ?>" class="nut-thao-tac nut-thao-tac--xem" style="text-decoration: none; font-size: 0.85rem; padding: 0.35rem 0.75rem;">
                                    Xem chi tiết
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</main>

<?php require_once __DIR__ . '/inc/footer.php'; ?>
</body>
</html>

