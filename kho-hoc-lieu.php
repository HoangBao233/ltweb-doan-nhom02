<?php
// kho-hoc-lieu.php — Trang danh sách giáo án, tìm kiếm, lọc, sắp xếp và phân trang từ CSDL MySQL
// Phụ trách: Tấn (MSSV 3120124027) — Chức năng 2 Bảng 1 & Bước 2
// Cách thử: kho-hoc-lieu.php?q=python&dm=1&gia=50000&sx=gia-tang&trang=1
require __DIR__ . '/inc/config.php';
use App\Data\KhoGiaoAn;

// --- PHẦN XỬ LÝ (không echo) ---
$kho   = new KhoGiaoAn(db());
$duong = ['options' => ['min_range' => 1]]; // điều kiện số nguyên dương

// Đọc và kiểm tra tham số lọc GET
$loc = [
    'q'        => trim($_GET['q'] ?? ''),
    'dm'       => filter_var($_GET['dm'] ?? '', FILTER_VALIDATE_INT, $duong),
    'giaToiDa' => filter_var($_GET['gia'] ?? '', FILTER_VALIDATE_INT, $duong),
    'cap_hoc'  => trim($_GET['cap_hoc'] ?? 'Tất cả'),
];

// Chặn cấp học không hợp lệ
$capHocHopLe = ['Tất cả', 'THCS', 'THPT'];
if (!in_array($loc['cap_hoc'], $capHocHopLe, true)) {
    $loc['cap_hoc'] = 'Tất cả';
}

// Bảng tùy chọn sắp xếp an toàn qua whitelist
$kieuSx = [
    'moi'      => 'Mới nhất',
    'gia-tang' => 'Giá tăng dần',
    'gia-giam' => 'Giá giảm dần',
    'ten'      => 'Tên (A–Z)',
];
$sx = $_GET['sx'] ?? 'moi';
if (!array_key_exists($sx, $kieuSx)) {
    $sx = 'moi';
}

// Xử lý phân trang
$moiTrang = 8;
$tong     = $kho->dem($loc);
$soTrang  = max(1, (int) ceil($tong / $moiTrang));

// Kiểm tra trang hiện tại (?trang=abc hoặc ?trang=-1 tự về 1, ?trang=999 tự về trang cuối)
$hienTai  = filter_var($_GET['trang'] ?? 1, FILTER_VALIDATE_INT, [
    'options' => ['min_range' => 1, 'default' => 1]
]);
if ($hienTai === false || $hienTai < 1) {
    $hienTai = 1;
}
$hienTai  = min($hienTai, $soTrang);

$boQua    = ($hienTai - 1) * $moiTrang;
$danhSach = $kho->trang($loc, $sx, $moiTrang, $boQua);

// Lấy danh sách danh mục từ MySQL để hiển thị ở bộ lọc
$dsDanhMuc = db()->query('SELECT id, ten FROM danh_muc ORDER BY ten')->fetchAll();

/**
 * Tạo URL chuyển trang, giữ nguyên mọi tham số lọc hiện tại (q, dm, gia, sx, cap_hoc).
 */
function lienKetTrang(int $so): string
{
    return '?' . http_build_query(array_merge($_GET, ['trang' => $so]));
}

$tieuDe = 'Kho học liệu | ITeduShare';
$trang  = 'kho-hoc-lieu';
require __DIR__ . '/inc/header.php';
?>

    <main class="trang__chinh">
        <h1 class="tieu-de-chinh can-giua-chu">Kho giáo án bộ môn Tin học</h1>

        <!-- Biểu mẫu tìm kiếm & lọc — method GET để URL chia sẻ được, giữ bộ lọc khi reload/gửi form -->
        <section class="bo-loc-hoc-lieu" aria-label="Tìm kiếm và lọc học liệu"
                 style="background: var(--nen-phu, #f8f9fa); padding: 1.5rem; border-radius: 8px; margin-bottom: 2rem;">
            <form method="GET" action="kho-hoc-lieu.php" role="search"
                  style="display: flex; flex-wrap: wrap; gap: 1rem; align-items: flex-end;">

                <!-- Ô tìm kiếm — giữ lại từ khoá đã nhập -->
                <div style="flex: 1; min-width: 220px;">
                    <label for="o-tim-kiem" style="font-weight: bold; display: block; margin-bottom: 0.5rem;">
                        Từ khóa:
                    </label>
                    <input type="text" id="o-tim-kiem" name="q"
                           value="<?= e($loc['q']) ?>"
                           placeholder="Nhập tên giáo án..."
                           style="width: 100%; padding: 0.5rem; border: 1px solid #ccc; border-radius: 4px;">
                </div>

                <!-- Lọc danh mục từ CSDL MySQL -->
                <div>
                    <label for="loc-danh-muc" style="font-weight: bold; display: block; margin-bottom: 0.5rem;">
                        Danh mục:
                    </label>
                    <select id="loc-danh-muc" name="dm"
                            style="padding: 0.5rem; border: 1px solid #ccc; border-radius: 4px;">
                        <option value="">-- Tất cả danh mục --</option>
                        <?php foreach ($dsDanhMuc as $dm): ?>
                            <option value="<?= (int)$dm['id'] ?>"<?= ($loc['dm'] === (int)$dm['id']) ? ' selected' : '' ?>>
                                <?= e($dm['ten']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Lọc cấp học -->
                <div>
                    <label for="loc-cap-hoc" style="font-weight: bold; display: block; margin-bottom: 0.5rem;">
                        Cấp học:
                    </label>
                    <select id="loc-cap-hoc" name="cap_hoc"
                            style="padding: 0.5rem; border: 1px solid #ccc; border-radius: 4px;">
                        <?php foreach ($capHocHopLe as $opt): ?>
                            <option value="<?= e($opt) ?>"<?= $opt === $loc['cap_hoc'] ? ' selected' : '' ?>>
                                <?= e($opt) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Lọc theo giá tối đa -->
                <div>
                    <label for="loc-gia" style="font-weight: bold; display: block; margin-bottom: 0.5rem;">
                        Giá tối đa:
                    </label>
                    <input type="number" id="loc-gia" name="gia" min="0" step="5000"
                           value="<?= $loc['giaToiDa'] ? (int)$loc['giaToiDa'] : '' ?>"
                           placeholder="VD: 50000"
                           style="padding: 0.5rem; border: 1px solid #ccc; border-radius: 4px; width: 130px;">
                </div>

                <!-- Sắp xếp an toàn -->
                <div>
                    <label for="sap-xep" style="font-weight: bold; display: block; margin-bottom: 0.5rem;">
                        Sắp xếp theo:
                    </label>
                    <select id="sap-xep" name="sx"
                            style="padding: 0.5rem; border: 1px solid #ccc; border-radius: 4px;">
                        <?php foreach ($kieuSx as $val => $nhan): ?>
                            <option value="<?= e($val) ?>"<?= $val === $sx ? ' selected' : '' ?>>
                                <?= e($nhan) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <button type="submit" class="nut-thao-tac nut-thao-tac--xem"
                        style="padding: 0.5rem 1.5rem;">
                    Tìm kiếm
                </button>

                <?php if ($loc['q'] !== '' || $loc['dm'] || $loc['giaToiDa'] || $loc['cap_hoc'] !== 'Tất cả' || $sx !== 'moi'): ?>
                    <a href="kho-hoc-lieu.php" style="padding: 0.5rem 1rem; color: #666; text-decoration: none;">✕ Xoá bộ lọc</a>
                <?php endif; ?>
            </form>
        </section>

        <!-- Danh sách kết quả — lấy từ CSDL MySQL qua KhoGiaoAn -->
        <section class="danh-sach-the" aria-label="Danh sách giáo án">
            <h2 class="can-giua-chu">
                Danh sách tài liệu
                <small style="font-size: 0.75em; color: #666;">(<?= $tong ?> kết quả)</small>
            </h2>

            <?php if (empty($danhSach)): ?>
                <!-- Thông báo thân thiện khi không tìm thấy kết quả -->
                <p role="status" style="text-align: center; padding: 2rem; color: #666; font-style: italic;">
                    Không tìm thấy giáo án nào phù hợp với bộ lọc hiện tại.
                    <br><a href="kho-hoc-lieu.php" style="display: inline-block; margin-top: 0.5rem; color: var(--mau-chinh, #007bff);">Xem tất cả giáo án</a>
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
                                <div style="display: flex; justify-content: space-between; align-items: center;">
                                    <span class="nhan-nho <?= $ga->cap_hoc === 'THCS' ? 'nhan-nho--xanh' : ($ga->cap_hoc === 'THPT' ? 'nhan-nho--cam' : 'nhan-nho--tim') ?>">
                                        <?= e($ga->cap_hoc) ?>
                                    </span>
                                    <button type="button" class="nut-yeu-thich nut-luu-giao-an" data-id="<?= $ga->id ?>" title="Lưu giáo án"
                                            style="background: none; border: none; cursor: pointer; font-size: 1.4rem; color: #ccc; transition: color 0.2s;"
                                            onclick="this.classList.toggle('da-thich'); this.style.color = this.classList.contains('da-thich') ? '#e74c3c' : '#ccc';">
                                        ♥
                                    </button>
                                </div>
                                <h3 class="the-hoc-lieu__tieu-de"><?= e($ga->ten) ?></h3>
                                <p class="the-hoc-lieu__mo-ta"><?= e($ga->mo_ta) ?></p>
                                <div style="margin-top: auto; text-align: center;">
                                    <p style="font-weight: bold; color: var(--mau-chinh, #007bff); margin-bottom: 0.75rem;">
                                        <?= $ga->gia === 0 ? 'Miễn phí' : vnd($ga->gia) ?>
                                    </p>
                                    <a href="chi-tiet-giao-an.php?id=<?= $ga->id ?>"
                                       class="nut-thao-tac nut-thao-tac--xem"
                                       style="display: block; text-align: center;">
                                        Xem chi tiết
                                    </a>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>

                <!-- Thanh phân trang có cấu trúc chuẩn HTML và CSS của dự án -->
                <?php if ($soTrang > 1): ?>
                    <nav class="phan-trang" aria-label="Phân trang">
                        <ul class="phan-trang__danh-sach">
                            <?php if ($hienTai > 1): ?>
                                <li>
                                    <a href="<?= e(lienKetTrang($hienTai - 1)) ?>" class="phan-trang__lien-ket" aria-label="Trang trước">
                                        &laquo; Trước
                                    </a>
                                </li>
                            <?php endif; ?>

                            <?php for ($i = 1; $i <= $soTrang; $i++): ?>
                                <li>
                                    <?php if ($i === $hienTai): ?>
                                        <span class="phan-trang__lien-ket phan-trang__lien-ket--hien-tai" aria-current="page">
                                            <?= $i ?>
                                        </span>
                                    <?php else: ?>
                                        <a href="<?= e(lienKetTrang($i)) ?>" class="phan-trang__lien-ket">
                                            <?= $i ?>
                                        </a>
                                    <?php endif; ?>
                                </li>
                            <?php endfor; ?>

                            <?php if ($hienTai < $soTrang): ?>
                                <li>
                                    <a href="<?= e(lienKetTrang($hienTai + 1)) ?>" class="phan-trang__lien-ket" aria-label="Trang sau">
                                        Sau &raquo;
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                <?php endif; ?>
            <?php endif; ?>
        </section>
    </main>

<?php require __DIR__ . '/inc/footer.php'; ?>

    <script type="module" src="js/main.js"></script>
    <script type="module" src="js/tim-kiem.js"></script>
    <script type="module" src="js/gio-hang.js"></script>
</body>
</html>