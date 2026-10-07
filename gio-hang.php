<?php
/**
 * gio-hang.php — Danh sách giáo án đã lưu (giỏ yêu thích)
 * Xử lý cả action: them, xoa, xoa_tat_ca qua POST
 */
require_once 'inc/config.php';
require_once 'src/Data/KhoGiaoAn.php';
require_once 'src/Services/DanhSachLuu.php';

// Xử lý POST actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action  = $_POST['action']   ?? '';
    $id      = (int)($_POST['id'] ?? 0);
    $quay_lai = $_POST['quay_lai'] ?? 'gio-hang.php';

    match ($action) {
        'them'        => DanhSachLuu::them($id),
        'xoa'         => DanhSachLuu::xoa($id),
        'xoa_tat_ca'  => DanhSachLuu::xoaToanBo(),
        default       => null,
    };

    chuyen_trang($quay_lai);
}

// Lấy danh sách giáo án đã lưu
$kho  = new KhoGiaoAn();
$danhSachId = DanhSachLuu::tatCa();
$danhSach   = $kho->layNhieuTheoId($danhSachId);

$tieu_de_trang  = 'Giáo án đã lưu';
$meta_mo_ta     = 'Danh sách giáo án bạn đã lưu lại trên ITeduShare.';
$trang_hien_tai = 'gio-hang';

require_once 'inc/header.php';
?>

    <main class="trang__chinh">
        <h1 class="can-giua-chu">🔖 Giáo án đã lưu</h1>
        <p class="can-giua-chu" style="color:#666;">Bạn đang lưu <strong><?= count($danhSach) ?></strong> giáo án.</p>

        <?php if (empty($danhSach)): ?>
            <div style="text-align: center; padding: 3rem;">
                <p style="font-size: 1.1rem; color: #888; margin-bottom: 1.5rem;">Bạn chưa lưu giáo án nào.</p>
                <a href="kho-hoc-lieu.php" class="nut-thao-tac nut-thao-tac--xem">Khám phá kho học liệu</a>
            </div>
        <?php else: ?>
            <!-- Nút xoá tất cả -->
            <div style="text-align: right; margin-bottom: 1rem;">
                <form method="POST" action="gio-hang.php"
                      onsubmit="return confirm('Xoá toàn bộ danh sách đã lưu?');">
                    <input type="hidden" name="action" value="xoa_tat_ca">
                    <input type="hidden" name="quay_lai" value="gio-hang.php">
                    <button type="submit" class="nut-thao-tac"
                            style="background:#e57373; color:#fff;">🗑 Xoá tất cả</button>
                </form>
            </div>

            <div class="danh-sach-the__luoi">
                <?php foreach ($danhSach as $ga): ?>
                <article class="the-hoc-lieu">
                    <?php if ($ga->hinh_anh): ?>
                    <img class="the-hoc-lieu__anh"
                         src="<?= e($ga->hinh_anh) ?>"
                         alt="<?= e($ga->ten) ?>" loading="lazy">
                    <?php endif; ?>
                    <div class="the-hoc-lieu__noi-dung">
                        <span style="font-size:0.8rem; background:#e3f2fd; padding:2px 6px; border-radius:4px;"><?= e($ga->cap_hoc) ?></span>
                        <h3 class="the-hoc-lieu__tieu-de"><?= e($ga->ten) ?></h3>
                        <p class="the-hoc-lieu__mo-ta"><?= e($ga->mo_ta) ?></p>
                        <p style="font-weight:bold; color: var(--mau-nhan, #ff5722);"><?= vnd($ga->gia) ?></p>
                    </div>
                    <div class="the-hoc-lieu__hanh-dong" style="padding: 0.5rem 1rem 1rem; display:flex; gap:0.5rem; flex-wrap:wrap;">
                        <a href="<?= url('chi-tiet-giao-an.php', ['id' => $ga->id]) ?>"
                           class="nut-thao-tac nut-thao-tac--xem">Xem chi tiết</a>

                        <form method="POST" action="gio-hang.php" style="display:inline;">
                            <input type="hidden" name="action" value="xoa">
                            <input type="hidden" name="id" value="<?= $ga->id ?>">
                            <input type="hidden" name="quay_lai" value="gio-hang.php">
                            <button type="submit" class="nut-thao-tac"
                                    style="background:#ef9a9a; color:#fff;">✕ Bỏ lưu</button>
                        </form>
                    </div>
                </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>

<?php require_once 'inc/footer.php'; ?>

    <script type="module" src="js/main.js"></script>
</body>
</html>
