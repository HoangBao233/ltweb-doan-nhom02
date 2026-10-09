<?php
// quan-tri.php — Trang quản trị: xem danh sách liên hệ đã nhận
// Phụ trách: Tấn (MSSV 3120124027) + Châu — Chức năng 6 Bảng 1
// Cách thử: đăng nhập tại dang-nhap.php (admin / nhom02@ltweb) rồi mở trang này
require __DIR__ . '/inc/config.php';
require __DIR__ . '/inc/bao-ve.php';  // chặn nếu chưa đăng nhập
use App\Data\KhoLienHe;

// Đọc tất cả liên hệ, mới nhất đứng đầu
$khoLienHe = new KhoLienHe(__DIR__ . '/storage/lien-he.jsonl');
$danhSachLienHe = $khoLienHe->tatCa();

$tieuDe = 'Quản trị | ITeduShare';
$trang  = '';
require __DIR__ . '/inc/header.php';
?>

    <main class="trang__chinh">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
            <h1>🛠️ Trang Quản trị</h1>
            <div>
                <span style="color: #666;">Xin chào, <strong><?= e($_SESSION['user'] ?? '') ?></strong></span>
                &nbsp;|&nbsp;
                <a href="dang-xuat.php" style="color: #e74c3c;">Đăng xuất</a>
            </div>
        </div>

        <section>
            <h2>📬 Danh sách liên hệ đã nhận
                <small style="font-size: 0.75em; color: #666;">(<?= count($danhSachLienHe) ?> liên hệ)</small>
            </h2>

            <?php if (empty($danhSachLienHe)): ?>
                <p style="color: #666; font-style: italic; padding: 2rem 0;">
                    Chưa có liên hệ nào được gửi đến.
                </p>
            <?php else: ?>
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse; background: #fff;">
                        <thead style="background: #f8f9fa;">
                            <tr>
                                <th style="padding: 0.75rem; border: 1px solid #ddd; text-align: left;">#</th>
                                <th style="padding: 0.75rem; border: 1px solid #ddd; text-align: left;">Thời gian</th>
                                <th style="padding: 0.75rem; border: 1px solid #ddd; text-align: left;">Họ tên</th>
                                <th style="padding: 0.75rem; border: 1px solid #ddd; text-align: left;">Email</th>
                                <th style="padding: 0.75rem; border: 1px solid #ddd; text-align: left;">Nội dung</th>
                                <th style="padding: 0.75rem; border: 1px solid #ddd; text-align: left;">Ảnh đính kèm</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($danhSachLienHe as $i => $lh): ?>
                                <tr style="<?= $i % 2 === 0 ? '' : 'background: #f8f9fa;' ?>">
                                    <td style="padding: 0.75rem; border: 1px solid #ddd;"><?= $i + 1 ?></td>
                                    <td style="padding: 0.75rem; border: 1px solid #ddd; white-space: nowrap;">
                                        <?= e($lh['ngay_tao'] ?? '') ?>
                                    </td>
                                    <td style="padding: 0.75rem; border: 1px solid #ddd;">
                                        <?= e($lh['hoten'] ?? '') ?>
                                    </td>
                                    <td style="padding: 0.75rem; border: 1px solid #ddd;">
                                        <a href="mailto:<?= e($lh['email'] ?? '') ?>">
                                            <?= e($lh['email'] ?? '') ?>
                                        </a>
                                    </td>
                                    <td style="padding: 0.75rem; border: 1px solid #ddd; max-width: 300px;">
                                        <?= e($lh['noidung'] ?? '') ?>
                                    </td>
                                    <td style="padding: 0.75rem; border: 1px solid #ddd;">
                                        <?php if (!empty($lh['anh'])): ?>
                                            <img src="uploads/<?= e($lh['anh']) ?>"
                                                 alt="Ảnh đính kèm từ <?= e($lh['hoten'] ?? '') ?>"
                                                 style="max-width: 120px; max-height: 80px; border-radius: 4px; object-fit: cover;">
                                        <?php else: ?>
                                            <span style="color: #999; font-style: italic;">Không có</span>
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

    <script type="module" src="js/main.js"></script>
</body>
</html>

