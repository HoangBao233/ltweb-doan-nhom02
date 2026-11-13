<?php
/**
 * quan-tri.php — Bảng điều khiển quản trị: CRUD Giáo án & Xem liên hệ
 * 
 * Phụ trách: Lê Thị Xuân Châu (MSSV: 3120124006) — Phần A, Chức năng 3
 * Yêu cầu: yeuCauQuyen('quan_tri'), phân trang giáo án, bảng liên hệ từ MySQL, flash messages
 * Bảo mật BTN7: Chống CSRF trên form xóa giáo án, loại bỏ 100% inline styles & onsubmit sang CSS/JS ngoài
 */

require_once __DIR__ . '/inc/config.php';
if (file_exists(__DIR__ . '/inc/csrf.php')) {
    require_once __DIR__ . '/inc/csrf.php';
}

if (!function_exists('csrfField')) {
    function csrfField(): string {
        return '<input type="hidden" name="csrf" value="' . (function_exists('csrfToken') ? csrfToken() : '') . '">';
    }
}

use App\Data\KhoLienHe;

// 1. Phân quyền: Chỉ tài khoản 'quan_tri' mới được truy cập
yeuCauQuyen('quan_tri');

// 2. Lấy và xóa Flash message (thành công / lỗi)
$flashSuccess = $_SESSION['flash'] ?? null;
$flashError   = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash'], $_SESSION['flash_error']);

// 3. Phân trang cho bảng Giáo án
$soDongMoiTrang = 8;
$stCount = db()->query('SELECT COUNT(*) FROM giao_an');
$tongGiaoAn = (int)$stCount->fetchColumn();
$soTrang = max(1, (int)ceil($tongGiaoAn / $soDongMoiTrang));

$trangHienTai = filter_var($_GET['trang'] ?? 1, FILTER_VALIDATE_INT, [
    'options' => ['min_range' => 1, 'default' => 1]
]);
if ($trangHienTai === false || $trangHienTai < 1) {
    $trangHienTai = 1;
}
$trangHienTai = min($trangHienTai, $soTrang);
$boQua = ($trangHienTai - 1) * $soDongMoiTrang;

// Lấy danh sách giáo án kèm tên danh mục (JOIN để tránh N+1)
$sqlGiaoAn = "SELECT ga.id, ga.ten, ga.cap_hoc, ga.gia, ga.ton_kho, ga.luot_tai, ga.hinh_anh, dm.ten AS ten_danh_muc
              FROM giao_an ga
              LEFT JOIN danh_muc dm ON ga.danh_muc_id = dm.id
              ORDER BY ga.id DESC
              LIMIT ? OFFSET ?";
$stGA = db()->prepare($sqlGiaoAn);
$stGA->bindValue(1, $soDongMoiTrang, PDO::PARAM_INT);
$stGA->bindValue(2, $boQua, PDO::PARAM_INT);
$stGA->execute();
$dsGiaoAn = $stGA->fetchAll();

// 4. Lấy danh sách liên hệ từ bảng lien_he qua KhoLienHe (mới nhất lên đầu)
$khoLienHe = new KhoLienHe();
$danhSachLienHe = $khoLienHe->tatCa(50);

$tieuDe = 'Bảng điều khiển Quản trị | ITeduShare';
$trang  = 'quan-tri';
$nguoiDungHienTai = nguoiDung();
require __DIR__ . '/inc/header.php';
?>

<link rel="stylesheet" href="css/quan-tri.css">

<main class="trang__chinh khung-quan-tri">
    <!-- Thanh tiêu đề quản trị -->
    <div class="thanh-tieu-de-qt">
        <h1 class="tieu-de-trang-qt">Trang Quản trị</h1>
        <div class="nhom-nut-qt">
            <a href="don-hang.php" class="nut-don-hang">
                Quản lý đơn hàng
            </a>
            <a href="them-giao-an.php" class="nut-them-ga">
                + Thêm giáo án mới
            </a>
        </div>
    </div>

    <!-- Thông báo Flash (Thành công / Thất bại) -->
    <?php if ($flashSuccess): ?>
        <div class="flash-thanh-cong">
            <?= is_array($flashSuccess) ? e($flashSuccess['message'] ?? '') : e($flashSuccess) ?>
        </div>
    <?php endif; ?>

    <?php if ($flashError): ?>
        <div class="flash-loi">
            <?= is_array($flashError) ? e($flashError['message'] ?? '') : e($flashError) ?>
        </div>
    <?php endif; ?>

    <!-- KHỐI 1: QUẢN LÝ DANH SÁCH GIÁO ÁN (CRUD) -->
    <section class="phan-quan-tri">
        <div class="thanh-dau-muc">
            <div>
                <h2 class="tieu-de-muc">Danh sách Giáo án &amp; Học liệu</h2>
                <span class="dem-tong-so">Tổng cộng: <strong><?= $tongGiaoAn ?></strong> giáo án</span>
            </div>
            <a href="them-giao-an.php" class="nut-them-ga">
                + Thêm giáo án mới
            </a>
        </div>

        <?php if (empty($dsGiaoAn)): ?>
            <p class="chua-co-du-lieu-qt">
                Chưa có giáo án nào trong cơ sở dữ liệu.
            </p>
        <?php else: ?>
            <div class="khung-cuon-bang">
                <table class="bang-quan-tri">
                    <thead>
                        <tr class="hang-tieu-de-bang">
                            <th class="o-stt">STT</th>
                            <th class="o-anh">Ảnh bìa</th>
                            <th class="o-tieu-de">Tên giáo án</th>
                            <th class="o-danh-muc">Danh mục</th>
                            <th class="o-cap-hoc">Cấp học</th>
                            <th class="o-gia">Đơn giá</th>
                            <th class="o-so-luong">Tồn kho</th>
                            <th class="o-so-luong">Lượt tải</th>
                            <th class="o-thao-tac">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($dsGiaoAn as $i => $ga): ?>
                            <tr class="hang-du-lieu <?= $i % 2 === 1 ? 'hang-xen-ke' : '' ?>">
                                <td class="o-stt">
                                    <?= $tongGiaoAn - $boQua - $i ?>
                                </td>
                                <td class="o-anh">
                                    <?php if (!empty($ga['hinh_anh'])): ?>
                                        <img src="<?= e(anhGiaoAn($ga['hinh_anh'])) ?>" 
                                             alt="<?= e($ga['ten']) ?>" 
                                             class="anh-thumbnail-ga">
                                    <?php else: ?>
                                        <div class="chua-anh-ga">
                                            Chưa ảnh
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="ten-ga-qt">
                                    <?= e($ga['ten']) ?>
                                </td>
                                <td class="o-danh-muc">
                                    <?= e($ga['ten_danh_muc'] ?? 'Chưa phân loại') ?>
                                </td>
                                <td class="o-cap-hoc">
                                    <span class="cap-hoc-badge">
                                        <?= e($ga['cap_hoc']) ?>
                                    </span>
                                </td>
                                <td class="gia-ga-qt">
                                    <?= (int)$ga['gia'] === 0 ? 'Miễn phí' : number_format((int)$ga['gia'], 0, ',', '.') . ' ₫' ?>
                                </td>
                                <td class="o-so-luong">
                                    <?= (int)$ga['ton_kho'] ?>
                                </td>
                                <td class="o-so-luong">
                                    <?= (int)$ga['luot_tai'] ?>
                                </td>
                                <td class="o-thao-tac">
                                    <a href="sua-giao-an.php?id=<?= (int)$ga['id'] ?>" class="nut-sua-ga">
                                        Sửa
                                    </a>
                                    <form method="POST" action="xoa-giao-an.php" class="form-xoa-ga" 
                                          data-confirm="Bạn có chắc chắn muốn xóa giáo án '<?= e(addslashes($ga['ten'])) ?>' (ID: <?= (int)$ga['id'] ?>)?">
                                        <?= csrfField() ?>
                                        <input type="hidden" name="id" value="<?= (int)$ga['id'] ?>">
                                        <button type="submit" class="nut-xoa-ga">
                                            Xóa
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Phân trang Giáo án -->
            <?php if ($soTrang > 1): ?>
                <div class="phan-trang-qt">
                    <?php for ($p = 1; $p <= $soTrang; $p++): ?>
                        <a href="quan-tri.php?trang=<?= $p ?>" 
                           class="nut-trang-qt <?= $p === $trangHienTai ? 'nut-trang-hien-tai' : '' ?>">
                            <?= $p ?>
                        </a>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </section>

    <!-- KHỐI 2: DANH SÁCH LIÊN HỆ ĐÃ NHẬN (TỪ BẢNG lien_he) -->
    <section class="phan-quan-tri">
        <h2 class="tieu-de-muc">
            Hộp thư liên hệ &amp; Phản hồi
            <small class="dem-tong-so">(<?= count($danhSachLienHe) ?> tin nhắn mới nhất)</small>
        </h2>

        <?php if (empty($danhSachLienHe)): ?>
            <p class="chua-co-du-lieu-qt">
                Chưa có liên hệ nào được gửi đến hệ thống.
            </p>
        <?php else: ?>
            <div class="khung-cuon-bang">
                <table class="bang-quan-tri">
                    <thead>
                        <tr class="hang-tieu-de-bang">
                            <th class="o-stt">STT</th>
                            <th class="o-ngaytao">Thời gian</th>
                            <th class="o-hoten">Họ tên</th>
                            <th class="o-email">Email</th>
                            <th class="o-sdt">Số điện thoại</th>
                            <th class="o-noidung">Nội dung phản hồi</th>
                            <th class="o-dinhkem">Tệp đính kèm</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($danhSachLienHe as $i => $lh): ?>
                            <tr class="hang-du-lieu <?= $i % 2 === 1 ? 'hang-xen-ke' : '' ?>">
                                <td class="o-stt">
                                    <?= (int)($lh['id'] ?? ($i + 1)) ?>
                                </td>
                                <td class="o-ngaytao">
                                    <?= e($lh['ngay_tao'] ?? '') ?>
                                </td>
                                <td class="o-hoten">
                                    <?= e($lh['ho_ten'] ?? $lh['hoten'] ?? '') ?>
                                </td>
                                <td class="o-email">
                                    <a href="mailto:<?= e($lh['email'] ?? '') ?>" class="link-email-lh">
                                        <?= e($lh['email'] ?? '') ?>
                                    </a>
                                </td>
                                <td class="o-sdt">
                                    <?= e($lh['sdt'] ?? '—') ?>
                                </td>
                                <td class="o-noidung">
                                    <?= nl2br(e($lh['noi_dung'] ?? $lh['noidung'] ?? '')) ?>
                                </td>
                                <td class="o-dinhkem">
                                    <?php if (!empty($lh['anh'])): ?>
                                        <?php $srcAnhLh = anhGiaoAn($lh['anh']); ?>
                                        <a href="<?= e($srcAnhLh) ?>" target="_blank" rel="noopener noreferrer">
                                            <img src="<?= e($srcAnhLh) ?>"
                                                 alt="Đính kèm từ <?= e($lh['ho_ten'] ?? '') ?>"
                                                 class="anh-dinhkem-lh">
                                        </a>
                                    <?php else: ?>
                                        <span class="khong-dinhkem">Không có</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </section>
</main>

<script type="module" src="js/quan-tri.js"></script>
<?php require __DIR__ . '/inc/footer.php'; ?>
</body>
</html>
