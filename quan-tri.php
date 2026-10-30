<?php
/**
 * quan-tri.php — Bảng điều khiển quản trị: CRUD Giáo án & Xem liên hệ
 * 
 * Phụ trách: Lê Thị Xuân Châu (MSSV: 3120124006) — Phần A, Chức năng 3
 * Yêu cầu: yeuCauQuyen('quan_tri'), phân trang giáo án, bảng liên hệ từ MySQL, flash messages
 */

require_once __DIR__ . '/inc/config.php';
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

<main class="trang__chinh" style="max-width: 1200px; margin: 0 auto; padding: 1.5rem 1rem;">
    <!-- Thanh tiêu đề quản trị -->
    <div style="margin-bottom: 1.5rem; border-bottom: 2px solid #e2e8f0; padding-bottom: 1rem;">
        <h1 style="margin: 0; font-size: 1.75rem; color: var(--mau-chinh, #004085);">Trang Quản trị</h1>
    </div>

    <!-- Thông báo Flash (Thành công / Thất bại) -->
    <?php if ($flashSuccess): ?>
        <div style="padding: 0.85rem 1.25rem; margin-bottom: 1.5rem; background: #ecfdf5; border: 1px solid #a7f3d0; border-left: 4px solid #10b981; color: #065f46; border-radius: 6px; font-weight: 500;">
            <?= is_array($flashSuccess) ? e($flashSuccess['message'] ?? '') : e($flashSuccess) ?>
        </div>
    <?php endif; ?>

    <?php if ($flashError): ?>
        <div style="padding: 0.85rem 1.25rem; margin-bottom: 1.5rem; background: #fff1f2; border: 1px solid #fecdd3; border-left: 4px solid #f43f5e; color: #9f1239; border-radius: 6px; font-weight: 500;">
            <?= is_array($flashError) ? e($flashError['message'] ?? '') : e($flashError) ?>
        </div>
    <?php endif; ?>

    <!-- KHỐI 1: QUẢN LÝ DANH SÁCH GIÁO ÁN (CRUD) -->
    <section style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 1.5rem; margin-bottom: 2.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; flex-wrap: wrap; gap: 0.75rem;">
            <div>
                <h2 style="margin: 0; font-size: 1.3rem; color: #1e293b;">Danh sách Giáo án &amp; Học liệu</h2>
                <span style="color: #64748b; font-size: 0.875rem;">Tổng cộng: <strong><?= $tongGiaoAn ?></strong> giáo án</span>
            </div>
            <a href="them-giao-an.php" class="nut-thao-tac" style="background: #0284c7; color: #fff; padding: 0.55rem 1.1rem; border-radius: 6px; text-decoration: none; font-weight: 600; display: inline-block;">
                + Thêm giáo án mới
            </a>
        </div>

        <?php if (empty($dsGiaoAn)): ?>
            <p style="color: #64748b; font-style: italic; padding: 2rem 0; text-align: center;">
                Chưa có giáo án nào trong cơ sở dữ liệu.
            </p>
        <?php else: ?>
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.925rem;">
                    <thead>
                        <tr style="background: #f8fafc; border-bottom: 2px solid #e2e8f0; color: #475569;">
                            <th style="padding: 0.75rem 0.5rem; width: 55px; text-align: center;">STT</th>
                            <th style="padding: 0.75rem 0.5rem; width: 80px; text-align: center;">Ảnh bìa</th>
                            <th style="padding: 0.75rem 0.5rem;">Tên giáo án</th>
                            <th style="padding: 0.75rem 0.5rem; width: 120px;">Danh mục</th>
                            <th style="padding: 0.75rem 0.5rem; width: 80px;">Cấp học</th>
                            <th style="padding: 0.75rem 0.5rem; width: 90px;">Đơn giá</th>
                            <th style="padding: 0.75rem 0.5rem; width: 75px; text-align: center;">Tồn kho</th>
                            <th style="padding: 0.75rem 0.5rem; width: 75px; text-align: center;">Lượt tải</th>
                            <th style="padding: 0.75rem 0.5rem; width: 140px; text-align: center;">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($dsGiaoAn as $i => $ga): ?>
                            <tr style="border-bottom: 1px solid #f1f5f9; <?= $i % 2 === 1 ? 'background: #fafafa;' : '' ?>">
                                <td style="padding: 0.75rem 0.5rem; text-align: center; color: #334155; font-weight: 600;">
                                    <?= $tongGiaoAn - $boQua - $i ?>
                                </td>
                                <td style="padding: 0.75rem 0.5rem; text-align: center;">
                                    <?php if (!empty($ga['hinh_anh'])): ?>
                                        <img src="uploads/<?= e($ga['hinh_anh']) ?>" 
                                             alt="<?= e($ga['ten']) ?>" 
                                             style="width: 60px; height: 42px; object-fit: cover; border-radius: 4px; border: 1px solid #e2e8f0;">
                                    <?php else: ?>
                                        <div style="width: 60px; height: 42px; background: #e2e8f0; border-radius: 4px; display: inline-flex; align-items: center; justify-content: center; font-size: 0.7rem; color: #64748b;">
                                            Chưa ảnh
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td style="padding: 0.75rem 0.5rem; font-weight: 600; color: #1e293b;">
                                    <?= e($ga['ten']) ?>
                                </td>
                                <td style="padding: 0.75rem 0.5rem; color: #475569;">
                                    <?= e($ga['ten_danh_muc'] ?? 'Chưa phân loại') ?>
                                </td>
                                <td style="padding: 0.75rem 0.5rem;">
                                    <span style="display: inline-block; padding: 0.2rem 0.5rem; border-radius: 4px; font-size: 0.8rem; background: #e0f2fe; color: #0369a1; font-weight: 600;">
                                        <?= e($ga['cap_hoc']) ?>
                                    </span>
                                </td>
                                <td style="padding: 0.75rem 0.5rem; font-weight: 600; color: #0f766e;">
                                    <?= (int)$ga['gia'] === 0 ? 'Miễn phí' : number_format((int)$ga['gia'], 0, ',', '.') . ' ₫' ?>
                                </td>
                                <td style="padding: 0.75rem 0.5rem; text-align: center; color: #334155;">
                                    <?= (int)$ga['ton_kho'] ?>
                                </td>
                                <td style="padding: 0.75rem 0.5rem; text-align: center; color: #334155;">
                                    <?= (int)$ga['luot_tai'] ?>
                                </td>
                                <td style="padding: 0.75rem 0.5rem; text-align: center; white-space: nowrap;">
                                    <a href="sua-giao-an.php?id=<?= (int)$ga['id'] ?>" 
                                       style="display: inline-block; padding: 0.3rem 0.65rem; border-radius: 4px; background: #f59e0b; color: #fff; text-decoration: none; font-size: 0.825rem; font-weight: 600; margin-right: 0.35rem;">
                                        Sửa
                                    </a>
                                    <form method="POST" action="xoa-giao-an.php" style="display: inline;" 
                                          onsubmit="return confirm('Bạn có chắc chắn muốn xóa giáo án \'<?= e(addslashes($ga['ten'])) ?>\' (ID: <?= (int)$ga['id'] ?>)?');">
                                        <input type="hidden" name="id" value="<?= (int)$ga['id'] ?>">
                                        <button type="submit" 
                                                style="cursor: pointer; padding: 0.3rem 0.65rem; border-radius: 4px; background: #ef4444; color: #fff; border: none; font-size: 0.825rem; font-weight: 600;">
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
                <div style="display: flex; justify-content: center; gap: 0.4rem; margin-top: 1.5rem; flex-wrap: wrap;">
                    <?php for ($p = 1; $p <= $soTrang; $p++): ?>
                        <a href="quan-tri.php?trang=<?= $p ?>" 
                           style="padding: 0.4rem 0.8rem; border-radius: 4px; text-decoration: none; font-size: 0.9rem; font-weight: 600; border: 1px solid #cbd5e1; <?= $p === $trangHienTai ? 'background: #0284c7; color: #fff; border-color: #0284c7;' : 'background: #fff; color: #334155;' ?>">
                            <?= $p ?>
                        </a>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </section>

    <!-- KHỐI 2: DANH SÁCH LIÊN HỆ ĐÃ NHẬN (TỪ BẢNG lien_he) -->
    <section style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 1.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
        <h2 style="margin: 0 0 0.5rem; font-size: 1.3rem; color: #1e293b;">
            Hộp thư liên hệ &amp; Phản hồi
            <small style="font-size: 0.875rem; color: #64748b; font-weight: normal;">(<?= count($danhSachLienHe) ?> tin nhắn mới nhất)</small>
        </h2>

        <?php if (empty($danhSachLienHe)): ?>
            <p style="color: #64748b; font-style: italic; padding: 2rem 0; text-align: center;">
                Chưa có liên hệ nào được gửi đến hệ thống.
            </p>
        <?php else: ?>
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.925rem;">
                    <thead>
                        <tr style="background: #f8fafc; border-bottom: 2px solid #e2e8f0; color: #475569;">
                            <th style="padding: 0.75rem 0.5rem; width: 55px; text-align: center;">STT</th>
                            <th style="padding: 0.75rem 0.5rem; width: 140px;">Thời gian</th>
                            <th style="padding: 0.75rem 0.5rem; width: 150px;">Họ tên</th>
                            <th style="padding: 0.75rem 0.5rem; width: 170px;">Email</th>
                            <th style="padding: 0.75rem 0.5rem; width: 110px;">Số điện thoại</th>
                            <th style="padding: 0.75rem 0.5rem;">Nội dung phản hồi</th>
                            <th style="padding: 0.75rem 0.5rem; width: 100px; text-align: center;">Tệp đính kèm</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($danhSachLienHe as $i => $lh): ?>
                            <tr style="border-bottom: 1px solid #f1f5f9; <?= $i % 2 === 1 ? 'background: #fafafa;' : '' ?>">
                                <td style="padding: 0.75rem 0.5rem; text-align: center; color: #64748b;">
                                    <?= (int)($lh['id'] ?? ($i + 1)) ?>
                                </td>
                                <td style="padding: 0.75rem 0.5rem; color: #475569; white-space: nowrap; font-size: 0.85rem;">
                                    <?= e($lh['ngay_tao'] ?? '') ?>
                                </td>
                                <td style="padding: 0.75rem 0.5rem; font-weight: 600; color: #1e293b;">
                                    <?= e($lh['ho_ten'] ?? $lh['hoten'] ?? '') ?>
                                </td>
                                <td style="padding: 0.75rem 0.5rem;">
                                    <a href="mailto:<?= e($lh['email'] ?? '') ?>" style="color: #0284c7; text-decoration: none;">
                                        <?= e($lh['email'] ?? '') ?>
                                    </a>
                                </td>
                                <td style="padding: 0.75rem 0.5rem; color: #475569;">
                                    <?= e($lh['sdt'] ?? '—') ?>
                                </td>
                                <td style="padding: 0.75rem 0.5rem; color: #334155; line-height: 1.45; max-width: 350px;">
                                    <?= nl2br(e($lh['noi_dung'] ?? $lh['noidung'] ?? '')) ?>
                                </td>
                                <td style="padding: 0.75rem 0.5rem; text-align: center;">
                                    <?php if (!empty($lh['anh'])): ?>
                                        <a href="uploads/<?= e($lh['anh']) ?>" target="_blank" rel="noopener noreferrer">
                                            <img src="uploads/<?= e($lh['anh']) ?>"
                                                 alt="Đính kèm từ <?= e($lh['ho_ten'] ?? '') ?>"
                                                 style="max-width: 80px; max-height: 55px; border-radius: 4px; object-fit: cover; border: 1px solid #e2e8f0;">
                                        </a>
                                    <?php else: ?>
                                        <span style="color: #94a3b8; font-style: italic; font-size: 0.85rem;">Không có</span>
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

<?php require __DIR__ . '/inc/footer.php'; ?>
</body>
</html>