<?php
// kho-hoc-lieu.php — Trang danh sách giáo án, tìm kiếm, lọc, sắp xếp
// Phụ trách: Tấn (MSSV 3120124027) — Chức năng 2 Bảng 1
// Cách thử: kho-hoc-lieu.php?q=python&cap_hoc=THPT&sx=gia-tang
require __DIR__ . '/inc/config.php';
use App\Data\KhoGiaoAn;

// --- PHẦN XỬ LÝ (không echo) ---
$kho = new KhoGiaoAn(__DIR__ . '/data/giao-an.json');

// Đọc và validate tham số GET
$q       = trim($_GET['q']       ?? '');
$capHoc  = trim($_GET['cap_hoc'] ?? 'Tất cả');
$sapXep  = trim($_GET['sx']      ?? 'ten-az');

// Chặn giá trị không hợp lệ
$capHocHopLe = ['Tất cả', 'THCS', 'THPT'];
$sapXepHopLe = ['ten-az', 'gia-tang', 'gia-giam'];
if (!in_array($capHoc, $capHocHopLe, true)) $capHoc = 'Tất cả';
if (!in_array($sapXep, $sapXepHopLe, true)) $sapXep = 'ten-az';

// Lấy kết quả từ lớp truy cập dữ liệu
$danhSach = $kho->timKiem($q, $capHoc, $sapXep);

$tieuDe = 'Kho học liệu | ITeduShare';
$trang  = 'kho-hoc-lieu';
require __DIR__ . '/inc/header.php';
?>

    <main class="trang__chinh">
        <h1 class="tieu-de-chinh can-giua-chu">Kho giáo án bộ môn Tin học</h1>

        <!-- Biểu mẫu tìm kiếm & lọc — method GET để URL chia sẻ được, tắt JS vẫn dùng được -->
        <section class="bo-loc-hoc-lieu" aria-label="Tìm kiếm và lọc học liệu"
                 style="background: var(--nen-phu, #f8f9fa); padding: 1.5rem; border-radius: 8px; margin-bottom: 2rem;">
            <form method="GET" action="kho-hoc-lieu.php" role="search"
                  style="display: flex; flex-wrap: wrap; gap: 1rem; align-items: flex-end;">

                <!-- Ô tìm kiếm — giữ lại từ khoá đã nhập -->
                <div style="flex: 1; min-width: 250px;">
                    <label for="o-tim-kiem" style="font-weight: bold; display: block; margin-bottom: 0.5rem;">
                        🔍 Tìm kiếm giáo án:
                    </label>
                    <input type="text" id="o-tim-kiem" name="q"
                           value="<?= e($q) ?>"
                           placeholder="Nhập tên bài học..."
                           style="width: 100%; padding: 0.5rem; border: 1px solid #ccc; border-radius: 4px;">
                </div>

                <!-- Lọc cấp học — giữ lại lựa chọn -->
                <div>
                    <label for="loc-cap-hoc" style="font-weight: bold; display: block; margin-bottom: 0.5rem;">
                        Cấp học:
                    </label>
                    <select id="loc-cap-hoc" name="cap_hoc"
                            style="padding: 0.5rem; border: 1px solid #ccc; border-radius: 4px;">
                        <?php foreach (['Tất cả', 'THCS', 'THPT'] as $opt): ?>
                            <option value="<?= e($opt) ?>"<?= $opt === $capHoc ? ' selected' : '' ?>>
                                <?= e($opt) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Sắp xếp — giữ lại lựa chọn -->
                <div>
                    <label for="sap-xep" style="font-weight: bold; display: block; margin-bottom: 0.5rem;">
                        Sắp xếp theo:
                    </label>
                    <select id="sap-xep" name="sx"
                            style="padding: 0.5rem; border: 1px solid #ccc; border-radius: 4px;">
                        <?php
                        $tuySapXep = ['ten-az' => 'Tên (A-Z)', 'gia-tang' => 'Giá (Thấp → Cao)', 'gia-giam' => 'Giá (Cao → Thấp)'];
                        foreach ($tuySapXep as $val => $nhan):
                        ?>
                            <option value="<?= e($val) ?>"<?= $val === $sapXep ? ' selected' : '' ?>>
                                <?= e($nhan) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <button type="submit" class="nut-thao-tac nut-thao-tac--xem"
                        style="padding: 0.5rem 1.5rem;">
                    Tìm kiếm
                </button>

                <?php if ($q !== '' || $capHoc !== 'Tất cả'): ?>
                    <a href="kho-hoc-lieu.php" style="padding: 0.5rem 1rem; color: #666;">✕ Xoá bộ lọc</a>
                <?php endif; ?>
            </form>
        </section>

        <!-- Danh sách kết quả — do PHP sinh ra từ JSON -->
        <section class="danh-sach-the" aria-label="Danh sách giáo án">
            <h2 class="can-giua-chu">
                Danh sách Giáo án &amp; Tài liệu
                <small style="font-size: 0.75em; color: #666;">(<?= count($danhSach) ?> kết quả)</small>
            </h2>

            <?php if (empty($danhSach)): ?>
                <!-- Thông báo khi không tìm thấy kết quả -->
                <p role="status" style="text-align: center; padding: 2rem; color: #666; font-style: italic;">
                    😕 Không tìm thấy giáo án nào phù hợp với
                    <?php if ($q !== ''): ?>từ khoá <strong>"<?= e($q) ?>"</strong><?php endif; ?>
                    <?php if ($capHoc !== 'Tất cả'): ?>cấp học <strong><?= e($capHoc) ?></strong><?php endif; ?>.
                    <br><a href="kho-hoc-lieu.php">Xem tất cả giáo án</a>
                </p>
            <?php else: ?>
                <div class="danh-sach-the__luoi">
                    <?php foreach ($danhSach as $ga): ?>
                        <article class="the-hoc-lieu">
                            <img src="<?= e($ga->hinh_anh) ?>"
                                 alt="<?= e($ga->ten) ?>"
                                 class="the-hoc-lieu__anh"
                                 width="300" height="180" loading="lazy">
                            <div class="the-hoc-lieu__noi-dung">
                                <span class="nhan-nho <?= $ga->cap_hoc === 'THCS' ? 'nhan-nho--xanh' : ($ga->cap_hoc === 'THPT' ? 'nhan-nho--cam' : 'nhan-nho--tim') ?>">
                                    <?= e($ga->cap_hoc) ?>
                                </span>
                                <h3 class="the-hoc-lieu__tieu-de"><?= e($ga->ten) ?></h3>
                                <p class="the-hoc-lieu__mo-ta"><?= e($ga->mo_ta) ?></p>
                                <p style="font-weight: bold; color: var(--mau-chinh, #007bff);">
                                    <?= $ga->gia === 0 ? 'Miễn phí' : vnd($ga->gia) ?>
                                </p>
                                <a href="chi-tiet-giao-an.php?id=<?= $ga->id ?>"
                                   class="nut-thao-tac nut-thao-tac--xem">
                                    Xem chi tiết bài học
                                </a>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
    </main>

<?php require __DIR__ . '/inc/footer.php'; ?>

