<?php
/**
 * kho-hoc-lieu.php — Danh sách học liệu (render từ PHP + JSON)
 */
require_once 'inc/config.php';
require_once 'src/Data/KhoGiaoAn.php';

// Lấy tham số lọc từ GET
$cap_hoc  = $_GET['cap_hoc']  ?? '';
$sap_xep  = $_GET['sap_xep']  ?? 'macdinh';
$tim_kiem = trim($_GET['q']   ?? '');

// Lọc và sắp xếp
$kho  = new KhoGiaoAn();
$danhSach = $kho->locVaSapXep($cap_hoc, $sap_xep, $tim_kiem);

$tieu_de_trang  = 'Kho học liệu';
$meta_mo_ta     = 'Danh sách các giáo án và tài liệu tham khảo môn Tin học dành cho cấp THCS và THPT chuẩn chương trình GDPT 2018.';
$trang_hien_tai = 'kho-hoc-lieu';

require_once 'inc/header.php';
?>

    <main class="trang__chinh">
        <h1 class="tieu-de-chinh can-giua-chu">Kho giáo án bộ môn Tin học</h1>

        <!-- Bộ lọc — dùng GET để có thể bookmark/share URL -->
        <section class="bo-loc-hoc-lieu" aria-label="Tìm kiếm và lọc học liệu"
                 style="background: var(--nen-phu, #f8f9fa); padding: 1.5rem; border-radius: 8px; margin-bottom: 2rem;">
            <form method="GET" action="kho-hoc-lieu.php"
                  style="display: flex; flex-wrap: wrap; gap: 1rem; align-items: flex-end;">
                <div style="flex: 1; min-width: 250px;">
                    <label for="o-tim-kiem" style="font-weight: bold; display: block; margin-bottom: 0.5rem;">🔍 Tìm kiếm giáo án:</label>
                    <input type="text" id="o-tim-kiem" name="q"
                           value="<?= e($tim_kiem) ?>"
                           placeholder="Nhập tên bài học..."
                           style="width: 100%; padding: 0.5rem; border: 1px solid #ccc; border-radius: 4px;">
                </div>

                <div>
                    <label for="loc-cap-hoc" style="font-weight: bold; display: block; margin-bottom: 0.5rem;">Cấp học:</label>
                    <select id="loc-cap-hoc" name="cap_hoc" style="padding: 0.5rem; border: 1px solid #ccc; border-radius: 4px;">
                        <option value=""       <?= $cap_hoc === ''      ? 'selected' : '' ?>>Tất cả</option>
                        <option value="THCS"   <?= $cap_hoc === 'THCS'  ? 'selected' : '' ?>>THCS</option>
                        <option value="THPT"   <?= $cap_hoc === 'THPT'  ? 'selected' : '' ?>>THPT</option>
                    </select>
                </div>

                <div>
                    <label for="sap-xep" style="font-weight: bold; display: block; margin-bottom: 0.5rem;">Sắp xếp theo:</label>
                    <select id="sap-xep" name="sap_xep" style="padding: 0.5rem; border: 1px solid #ccc; border-radius: 4px;">
                        <option value="macdinh" <?= $sap_xep === 'macdinh'  ? 'selected' : '' ?>>Mặc định</option>
                        <option value="ten-az"  <?= $sap_xep === 'ten-az'   ? 'selected' : '' ?>>Tên (A-Z)</option>
                        <option value="gia-tang"<?= $sap_xep === 'gia-tang' ? 'selected' : '' ?>>Giá (Thấp đến Cao)</option>
                        <option value="gia-giam"<?= $sap_xep === 'gia-giam' ? 'selected' : '' ?>>Giá (Cao xuống Thấp)</option>
                    </select>
                </div>

                <div>
                    <button type="submit" class="nut-thao-tac nut-thao-tac--xem">Tìm kiếm</button>
                    <a href="kho-hoc-lieu.php" style="margin-left: 0.5rem; font-size: 0.9rem;">Xoá bộ lọc</a>
                </div>
            </form>
        </section>

        <!-- Danh sách giáo án -->
        <section class="danh-sach-the" aria-label="Học liệu nổi bật">
            <h2 class="can-giua-chu">Danh sách Giáo án &amp; Tài liệu
                <small style="font-size: 0.75em; color: #666;">(<?= count($danhSach) ?> kết quả)</small>
            </h2>

            <?php if (empty($danhSach)): ?>
                <p style="text-align: center; padding: 3rem; color: #666; font-style: italic;">
                    Không tìm thấy giáo án nào phù hợp. <a href="kho-hoc-lieu.php">Xem tất cả</a>
                </p>
            <?php else: ?>
            <div class="danh-sach-the__luoi">
                <?php foreach ($danhSach as $ga): ?>
                <article class="the-hoc-lieu">
                    <?php if ($ga->hinh_anh): ?>
                    <img class="the-hoc-lieu__anh" src="<?= e($ga->hinh_anh) ?>" alt="Ảnh minh họa cho <?= e($ga->ten) ?>" loading="lazy">
                    <?php endif; ?>
                    <div class="the-hoc-lieu__noi-dung">
                        <span class="the-hoc-lieu__cap-hoc" style="font-size:0.8rem; background:#e3f2fd; padding:2px 6px; border-radius:4px;"><?= e($ga->cap_hoc) ?></span>
                        <h3 class="the-hoc-lieu__tieu-de"><?= e($ga->ten) ?></h3>
                        <p class="the-hoc-lieu__mo-ta"><?= e($ga->mo_ta) ?></p>
                        <p class="the-hoc-lieu__gia" style="font-weight:bold; color: var(--mau-nhan, #ff5722);">
                            <?= vnd($ga->gia) ?>
                        </p>
                    </div>
                    <div class="the-hoc-lieu__hanh-dong" style="padding: 0.5rem 1rem 1rem;">
                        <a href="<?= url('chi-tiet-giao-an.php', ['id' => $ga->id]) ?>"
                           class="nut-thao-tac nut-thao-tac--xem">Xem chi tiết</a>

                        <!-- Thêm vào giỏ lưu qua POST -->
                        <form method="POST" action="gio-hang.php" style="display:inline;">
                            <input type="hidden" name="action" value="them">
                            <input type="hidden" name="id" value="<?= $ga->id ?>">
                            <input type="hidden" name="quay_lai" value="kho-hoc-lieu.php">
                            <button type="submit" class="nut-thao-tac" title="Lưu giáo án">🔖 Lưu</button>
                        </form>
                    </div>
                </article>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </section>
    </main>

<?php require_once 'inc/footer.php'; ?>

    <script type="module" src="js/main.js"></script>
</body>
</html>
