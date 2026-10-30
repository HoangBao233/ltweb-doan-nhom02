<?php
/**
 * chi-tiet-giao-an.php — Trang chi tiết giáo án
 * 
 * Đồ án Lập trình Web - Nhóm 02
 * Phụ trách: Lê Hoàng Bảo (MSSV: 3120124003) — Phần A, Chức năng 1
 */

require_once 'inc/config.php';
use App\Data\KhoGiaoAn;
use App\Services\DanhSachLuu;

// 1. Kiểm tra ID hợp lệ từ URL (?id=...)
$id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
if ($id === false || $id === null) {
    http_response_code(404);
    require_once __DIR__ . '/404.php';
    exit;
}

// 2. Truy vấn giáo án từ CSDL MySQL qua KhoGiaoAn (kèm tên danh mục qua JOIN)
$kho = new KhoGiaoAn();
$ga  = $kho->timTheoId($id);

// Nếu không tìm thấy giáo án → Trả mã HTTP 404 và nạp 404.php
if ($ga === null) {
    http_response_code(404);
    require_once __DIR__ . '/404.php';
    exit;
}

// 3. Lưu lịch sử xem vào Cookie (tối đa 4 ID mới nhất)
$cu  = array_values(array_filter(array_map('intval', explode(',', $_COOKIE['da_xem'] ?? '')), fn($v) => $v > 0));
$moi = array_unique([$ga->id, ...$cu]);
setcookie('da_xem', implode(',', array_slice($moi, 0, 4)), [
    'expires'  => time() + 30 * 24 * 3600,
    'path'     => '/',
    'httponly' => true,
    'samesite' => 'Lax',
]);

// 4. Lấy 4 giáo án liên quan cùng danh mục bằng 1 câu truy vấn MySQL
$dsLienQuan = $ga->danh_muc_id > 0 ? $kho->cungDanhMuc($ga->danh_muc_id, $ga->id, 4) : [];

$tieu_de_trang  = e($ga->ten);
$meta_mo_ta     = e($ga->mo_ta);
$trang_hien_tai = 'chi-tiet-giao-an';

require_once 'inc/header.php';
?>

    <main class="trang__chinh chi-tiet-giao-an">
        <div id="vung-chi-tiet" aria-live="polite">
            <!-- Breadcrumb hiển thị cấp học & danh mục (JOIN) -->
            <nav aria-label="Đường dẫn điều hướng" style="margin-bottom: 0.75rem; font-size: 0.9rem; color: #666;">
                <a href="index.php" style="color: var(--mau-chinh); text-decoration: none;">Trang chủ</a> &raquo; 
                <a href="kho-hoc-lieu.php" style="color: var(--mau-chinh); text-decoration: none;">Kho học liệu</a> &raquo; 
                <a href="kho-hoc-lieu.php?dm=<?= $ga->danh_muc_id ?>" style="color: var(--mau-chinh); text-decoration: none; font-weight: 600;"><?= e($ga->ten_danh_muc ?: 'Tin học') ?></a>
            </nav>

            <h1 class="chi-tiet-giao-an__tieu-de"><?= e($ga->ten) ?></h1>

            <div style="margin-bottom: 2rem; display: flex; align-items: center; gap: 1rem; flex-wrap: wrap;">
                <?php
                $dsLuu = new DanhSachLuu();
                $daLuu = $dsLuu->daLuu($ga->id);
                ?>
                <form action="gio-hang.php" method="POST" class="form-luu-giao-an" style="margin: 0;">
                    <input type="hidden" name="action" value="<?= $daLuu ? 'xoa' : 'them' ?>">
                    <input type="hidden" name="id" value="<?= $ga->id ?>">
                    <input type="hidden" name="sl" value="1">
                    <button type="submit" class="nut-thao-tac" style="cursor: pointer; border: none; font-size: 1rem; font-family: inherit; min-width: 140px; text-align: center;">
                        <?= $daLuu ? 'Bỏ khỏi giỏ' : 'Thêm vào giỏ' ?>
                    </button>
                </form>

                <a href="kho-hoc-lieu.php" style="font-size: 0.95rem; text-decoration: none; color: var(--mau-chinh);">&larr; Quay lại kho học liệu</a>
            </div>

            <!-- Nội dung chính -->
            <article class="chi-tiet-giao-an__noi-dung">
                <h2>Mô tả chi tiết bài học</h2>
                <p><?= e($ga->mo_ta) ?></p>

                <?php if (!empty($ga->hinh_anh)): ?>
                <figure>
                    <img src="<?= e($ga->hinh_anh) ?>"
                         alt="Hình ảnh minh họa cho <?= e($ga->ten) ?>"
                         loading="lazy" width="600" height="400"
                         style="max-width: 100%; height: auto; border-radius: 8px;">
                </figure>
                <?php endif; ?>

                <?php if (!empty($ga->video_url)): ?>
                <div style="margin-top: 2rem;">
                    <h2>Video bài giảng minh họa</h2>
                    <iframe class="chi-tiet-giao-an__khung-xem"
                            height="315"
                            style="width: 100%; max-width: 600px; border-radius: 8px; border: none;"
                            src="<?= e($ga->video_url) ?>"
                            title="Video hướng dẫn: <?= e($ga->ten) ?>"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                            allowfullscreen loading="lazy">
                    </iframe>
                </div>
                <?php endif; ?>
            </article>

            <!-- Cột phụ thông tin -->
            <aside class="chi-tiet-giao-an__thong-tin">
                <h2>Bảng thông số tài liệu</h2>
                <div class="khung-cuon-bang">
                    <table class="bang-hoc-lieu">
                        <caption class="bang-hoc-lieu__chu-thich">Thông số kỹ thuật của giáo án</caption>
                        <thead>
                            <tr>
                                <th scope="col" class="bang-hoc-lieu__dau-cot">Thuộc tính</th>
                                <th scope="col" class="bang-hoc-lieu__dau-cot">Thông tin chi tiết</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="bang-hoc-lieu__hang">
                                <th scope="row" class="bang-hoc-lieu__o-tieu-de">Mã tài liệu</th>
                                <td class="bang-hoc-lieu__o">#<?= $ga->id ?></td>
                            </tr>
                            <tr class="bang-hoc-lieu__hang">
                                <th scope="row" class="bang-hoc-lieu__o-tieu-de">Danh mục</th>
                                <td class="bang-hoc-lieu__o">
                                    <a href="kho-hoc-lieu.php?dm=<?= $ga->danh_muc_id ?>" style="color: var(--mau-chinh); font-weight: bold; text-decoration: none;">
                                        <?= e($ga->ten_danh_muc ?: 'Chưa phân loại') ?>
                                    </a>
                                </td>
                            </tr>
                            <tr class="bang-hoc-lieu__hang">
                                <th scope="row" class="bang-hoc-lieu__o-tieu-de">Cấp học</th>
                                <td class="bang-hoc-lieu__o"><?= e($ga->cap_hoc) ?></td>
                            </tr>
                            <tr class="bang-hoc-lieu__hang">
                                <th scope="row" class="bang-hoc-lieu__o-tieu-de">Tình trạng tồn kho</th>
                                <td class="bang-hoc-lieu__o">
                                    <?php if ($ga->ton_kho > 0): ?>
                                        <span style="color: #2e7d32; font-weight: bold;">Còn <?= $ga->ton_kho ?> lượt tải</span>
                                    <?php else: ?>
                                        <span style="color: #c62828; font-weight: bold;">Đã hết hàng</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr class="bang-hoc-lieu__hang">
                                <th scope="row" class="bang-hoc-lieu__o-tieu-de">Giá tài liệu</th>
                                <td class="bang-hoc-lieu__o" style="font-weight: bold; color: var(--mau-nhan, #e65100); font-size: 1.1rem;">
                                    <?= $ga->gia === 0 ? 'Miễn phí' : vnd($ga->gia) ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </aside>
        </div>

        <!-- KHU VỰC GIÁO ÁN LIÊN QUAN CÙNG DANH MỤC -->
        <?php if (!empty($dsLienQuan)): ?>
            <section class="gioi-thieu-tinh-nang" style="margin-top: 4rem; border-top: 1px solid #eee; padding-top: 2rem;">
                <h2>Giáo án liên quan cùng danh mục</h2>
                <div class="danh-sach-the__luoi">
                    <?php foreach ($dsLienQuan as $lq): ?>
                        <article class="the-hoc-lieu">
                            <img src="<?= e($lq->hinh_anh ?: 'images/banner-trang-chu.jpg') ?>"
                                 alt="<?= e($lq->ten) ?>"
                                 class="the-hoc-lieu__anh"
                                 width="300" height="180" loading="lazy">
                            <div class="the-hoc-lieu__noi-dung">
                                <span class="nhan-nho <?= $lq->cap_hoc === 'THCS' ? 'nhan-nho--xanh' : ($lq->cap_hoc === 'THPT' ? 'nhan-nho--cam' : 'nhan-nho--tim') ?>">
                                    <?= e($lq->cap_hoc) ?>
                                </span>
                                <h3 class="the-hoc-lieu__tieu-de"><?= e($lq->ten) ?></h3>
                                <div style="margin-top: auto; text-align: center;">
                                    <p style="font-weight: bold; color: var(--mau-chinh, #007bff); margin-bottom: 0.75rem;">
                                        <?= $lq->gia === 0 ? 'Miễn phí' : vnd($lq->gia) ?>
                                    </p>
                                    <a href="chi-tiet-giao-an.php?id=<?= $lq->id ?>"
                                       class="nut-thao-tac nut-thao-tac--xem"
                                       style="display: block; text-align: center; text-decoration: none;">
                                        Xem chi tiết
                                    </a>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>
    </main>

<?php require_once 'inc/footer.php'; ?>

    <script type="module" src="js/main.js"></script>
    <script type="module" src="js/gio-hang.js"></script>
</body>
</html>