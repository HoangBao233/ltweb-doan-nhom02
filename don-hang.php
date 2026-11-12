<?php
/**
 * don-hang.php — Quản lý và xem danh sách / chi tiết đơn hàng
 * Đồ án Lập trình Web - Nhóm 02
 * Phụ trách: Nguyễn Tiến Thịnh (MSSV: 3120222126) & Lê Hoàng Bảo — Chức năng 5 & Biện pháp 1, 3
 */

require_once __DIR__ . '/inc/config.php';
require_once __DIR__ . '/inc/csrf.php';
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
    $trangThaiMoi = chuoi($_POST['trang_thai'] ?? '');

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

    <main class="trang__chinh khung-don-hang">
        <div class="khung-don-hang__thanh-tieu-de">
            <h1 class="khung-don-hang__tieu-de--trai">Chi tiết đơn hàng #<?= (int)$donHang['id'] ?></h1>
            <a href="don-hang.php" class="bieu-mau-xac-thuc__lien-ket">&larr; Về danh sách đơn hàng</a>
        </div>

        <?php if ($thongBao !== ''): ?>
            <div role="status" class="thong-bao-thanh-cong">
                <?= e($thongBao) ?>
            </div>
        <?php endif; ?>

        <?php if ($loi !== ''): ?>
            <div role="alert" class="thong-bao-loi">
                <?= e($loi) ?>
            </div>
        <?php endif; ?>

        <div class="the-trang-thong-tin">
            <div class="luoi-hai-cot">
                <div>
                    <span class="chu-nhan-phu">Ngày đặt hàng:</span>
                    <p class="chu-gia-tri"><?= e($donHang['ngay_tao']) ?></p>
                </div>
                <div>
                    <span class="chu-nhan-phu">Người nhận:</span>
                    <p class="chu-gia-tri"><?= e($donHang['ho_ten']) ?> (<?= e($donHang['email']) ?>)</p>
                </div>
                <div>
                    <span class="chu-nhan-phu">Trạng thái đơn:</span>
                    <p class="chu-gia-tri">
                        <?php if ($donHang['trang_thai'] === 'da_giao'): ?>
                            <span class="nhan-trang-thai nhan-trang-thai--hoan-tat">Hoàn tất</span>
                        <?php elseif ($donHang['trang_thai'] === 'da_huy'): ?>
                            <span class="nhan-trang-thai nhan-trang-thai--da-huy">Đã hủy</span>
                        <?php else: ?>
                            <span class="nhan-trang-thai nhan-trang-thai--cho-duyet">Chờ duyệt</span>
                        <?php endif; ?>
                    </p>
                </div>
            </div>

            <?php if (!empty($donHang['ghi_chu'])): ?>
                <div class="thong-bao-thanh-cong">
                    <strong class="chu-nhan-phu">Ghi chú của người đặt:</strong>
                    <p class="chu-gia-tri"><?= e($donHang['ghi_chu']) ?></p>
                </div>
            <?php endif; ?>

            <h2 class="the-trang-thong-tin__tieu-de">Danh sách học liệu trong đơn</h2>
            <div class="khung-cuon-bang">
                <table class="bang-du-lieu">
                    <thead>
                        <tr>
                            <th class="chu-can-trai">Giáo án</th>
                            <th class="chu-can-giua">Cấp học</th>
                            <th class="chu-can-giua">Số lượng</th>
                            <th class="chu-can-phai">Đơn giá lúc mua</th>
                            <th class="chu-can-phai">Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($dsChiTiet as $item): 
                            $thanhTien = $item['don_gia'] * $item['so_luong'];
                        ?>
                            <tr>
                                <td class="chu-can-trai">
                                    <a href="chi-tiet-giao-an.php?id=<?= $item['giao_an_id'] ?>" class="bieu-mau-xac-thuc__lien-ket">
                                        <?= e($item['ten']) ?>
                                    </a>
                                </td>
                                <td class="chu-can-giua">
                                    <span class="nhan-nho <?= $item['cap_hoc'] === 'THCS' ? 'nhan-nho--xanh' : 'nhan-nho--cam' ?>">
                                        <?= e($item['cap_hoc']) ?>
                                    </span>
                                </td>
                                <td class="chu-can-giua"><?= $item['so_luong'] ?></td>
                                <td class="chu-can-phai"><?= $item['don_gia'] === 0 ? 'Miễn phí' : vnd($item['don_gia']) ?></td>
                                <td class="chu-can-phai chu-tong-tien"><?= vnd($thanhTien) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="4" class="chu-can-phai">Tổng thanh toán:</td>
                            <td class="chu-can-phai chu-tong-tien"><?= vnd($donHang['tong_tien']) ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Nếu là Quản trị viên: Hiển thị form cập nhật trạng thái đơn hàng -->
            <?php if ($isAdmin): ?>
                <div class="khung-phan-cach-form">
                    <form method="POST" action="don-hang.php?id=<?= (int)$donHang['id'] ?>" class="form-cap-nhat-trang-thai">
                        <?= csrfField() ?>
                        <input type="hidden" name="cap_nhat_trang_thai" value="1">
                        <input type="hidden" name="don_hang_id" value="<?= (int)$donHang['id'] ?>">
                        <label for="trang_thai" class="bieu-mau-xac-thuc__nhan">Cập nhật trạng thái đơn:</label>
                        <select id="trang_thai" name="trang_thai" class="o-chon-trang-thai">
                            <option value="cho_duyet" <?= $donHang['trang_thai'] === 'cho_duyet' ? 'selected' : '' ?>>Chờ duyệt</option>
                            <option value="da_giao" <?= $donHang['trang_thai'] === 'da_giao' ? 'selected' : '' ?>>Hoàn tất</option>
                            <option value="da_huy" <?= $donHang['trang_thai'] === 'da_huy' ? 'selected' : '' ?>>Đã hủy</option>
                        </select>
                        <button type="submit" class="nut-thao-tac nut-thao-tac--xem">
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
    // Quản trị viên xem tất cả đơn hàng trong hệ thống (kèm JOIN + GROUP BY + SUM)
    $st = db()->query('
        SELECT dh.id, dh.nguoi_dung_id, dh.tong_tien, dh.trang_thai, dh.ghi_chu, dh.ngay_tao,
               nd.ho_ten, nd.email,
               COUNT(ct.giao_an_id) AS so_mon,
               COALESCE(SUM(ct.so_luong), 0) AS tong_so_luong,
               COALESCE(SUM(ct.so_luong * ct.don_gia), dh.tong_tien) AS tong_tien_chi_tiet
        FROM don_hang dh
        JOIN nguoi_dung nd ON dh.nguoi_dung_id = nd.id
        LEFT JOIN chi_tiet_don ct ON dh.id = ct.don_hang_id
        GROUP BY dh.id, dh.nguoi_dung_id, dh.tong_tien, dh.trang_thai, dh.ghi_chu, dh.ngay_tao, nd.ho_ten, nd.email
        ORDER BY dh.id DESC
    ');
    $danhSachDon = $st->fetchAll();
    $tieuDe = 'Quản lý đơn hàng toàn hệ thống | ITeduShare';
} else {
    // Khách hàng chỉ xem danh sách đơn hàng của chính mình (WHERE nguoi_dung_id = ?)
    $st = db()->prepare('
        SELECT dh.id, dh.nguoi_dung_id, dh.tong_tien, dh.trang_thai, dh.ghi_chu, dh.ngay_tao,
               nd.ho_ten, nd.email,
               COUNT(ct.giao_an_id) AS so_mon,
               COALESCE(SUM(ct.so_luong), 0) AS tong_so_luong,
               COALESCE(SUM(ct.so_luong * ct.don_gia), dh.tong_tien) AS tong_tien_chi_tiet
        FROM don_hang dh
        JOIN nguoi_dung nd ON dh.nguoi_dung_id = nd.id
        LEFT JOIN chi_tiet_don ct ON dh.id = ct.don_hang_id
        WHERE dh.nguoi_dung_id = ?
        GROUP BY dh.id, dh.nguoi_dung_id, dh.tong_tien, dh.trang_thai, dh.ghi_chu, dh.ngay_tao, nd.ho_ten, nd.email
        ORDER BY dh.id DESC
    ');
    $st->execute([(int)$u['id']]);
    $danhSachDon = $st->fetchAll();
    $tieuDe = 'Đơn hàng của tôi | ITeduShare';
}

$trang = 'don-hang';
require_once __DIR__ . '/inc/header.php';
?>

<main class="trang__chinh khung-don-hang khung-don-hang--rong">
    <h1 class="khung-don-hang__tieu-de--trai">
        <?= $isAdmin ? 'Quản lý đơn hàng (Toàn hệ thống)' : 'Đơn hàng của tôi' ?>
    </h1>
    <p class="khung-don-hang__mo-ta--trai">
        <?= $isAdmin ? 'Xem và phê duyệt các yêu cầu đặt mua / tải giáo án của giáo viên và khách hàng' : 'Xem lại lịch sử các giáo án bạn đã đăng ký tải và mua trên hệ thống' ?>
    </p>

    <?php if ($thongBao !== ''): ?>
        <div role="status" class="thong-bao-thanh-cong">
            <?= e($thongBao) ?>
        </div>
    <?php endif; ?>

    <?php if ($loi !== ''): ?>
        <div role="alert" class="thong-bao-loi">
            <?= e($loi) ?>
        </div>
    <?php endif; ?>

    <?php if (empty($danhSachDon)): ?>
        <div class="hop-trong-don-hang">
            <p class="hop-trong-don-hang__chu">
                <?= $isAdmin ? 'Hiện chưa có đơn hàng nào trong hệ thống.' : 'Bạn chưa có đơn đặt giáo án nào.' ?>
            </p>
            <a href="kho-hoc-lieu.php" class="nut-thao-tac nut-thao-tac--xem">
                Khám phá Kho học liệu ngay
            </a>
        </div>
    <?php else: ?>
        <div class="khung-cuon-bang the-trang-thong-tin">
            <table class="bang-du-lieu">
                <thead>
                    <tr>
                        <th class="chu-can-giua">Mã đơn</th>
                        <?php if ($isAdmin): ?>
                            <th class="chu-can-trai">Khách hàng</th>
                        <?php endif; ?>
                        <th class="chu-can-trai">Ngày đặt</th>
                        <th class="chu-can-giua">Số mục</th>
                        <th class="chu-can-phai">Tổng thanh toán</th>
                        <th class="chu-can-giua">Trạng thái</th>
                        <th class="chu-can-giua">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($danhSachDon as $don): ?>
                        <tr>
                            <td class="chu-can-giua chu-gia-tri">
                                #<?= (int)$don['id'] ?>
                            </td>
                            <?php if ($isAdmin): ?>
                                <td>
                                    <strong><?= e($don['ho_ten']) ?></strong><br>
                                    <small class="chu-nhan-phu"><?= e($don['email']) ?></small>
                                </td>
                            <?php endif; ?>
                            <td class="chu-nhan-phu">
                                <?= e($don['ngay_tao']) ?>
                            </td>
                            <td class="chu-can-giua">
                                <?= (int)($don['so_mon'] ?? 0) ?> giáo án (SL: <?= (int)($don['tong_so_luong'] ?? 0) ?>)
                            </td>
                            <td class="chu-can-phai chu-tong-tien">
                                <?= vnd($don['tong_tien_chi_tiet'] ?? $don['tong_tien']) ?>
                            </td>
                            <td class="chu-can-giua">
                                <?php if ($don['trang_thai'] === 'da_giao'): ?>
                                    <span class="nhan-trang-thai nhan-trang-thai--hoan-tat">Hoàn tất</span>
                                <?php elseif ($don['trang_thai'] === 'da_huy'): ?>
                                    <span class="nhan-trang-thai nhan-trang-thai--da-huy">Đã hủy</span>
                                <?php else: ?>
                                    <span class="nhan-trang-thai nhan-trang-thai--cho-duyet">Chờ duyệt</span>
                                <?php endif; ?>
                            </td>
                            <td class="chu-can-giua">
                                <a href="don-hang.php?id=<?= (int)$don['id'] ?>" class="nut-thao-tac nut-thao-tac--xem">
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

