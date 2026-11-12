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
            <nav aria-label="Đường dẫn điều hướng" class="duong-dan-dieu-huong">
                <a href="index.php">Trang chủ</a> &raquo; 
                <a href="kho-hoc-lieu.php">Kho học liệu</a> &raquo; 
                <a href="kho-hoc-lieu.php?dm=<?= $ga->danh_muc_id ?>" class="duong-dan-dieu-huong__muc-cuoi"><?= e($ga->ten_danh_muc ?: 'Tin học') ?></a>
            </nav>

            <h1 class="chi-tiet-giao-an__tieu-de"><?= e($ga->ten) ?></h1>

            <div class="hanh-dong-chi-tiet">
                <?php
                $dsLuu = new DanhSachLuu();
                $daLuu = $dsLuu->daLuu($ga->id);
                ?>
                <form action="gio-hang.php" method="POST" class="form-luu-giao-an form-noi-tuyen">
                    <input type="hidden" name="action" value="<?= $daLuu ? 'xoa' : 'them' ?>">
                    <input type="hidden" name="id" value="<?= $ga->id ?>">
                    <input type="hidden" name="sl" value="1">
                    <button type="submit" class="nut-thao-tac nut-thao-tac--luu">
                        <?= $daLuu ? 'Bỏ khỏi giỏ' : 'Thêm vào giỏ' ?>
                    </button>
                </form>

                <a href="kho-hoc-lieu.php" class="lien-ket-quay-lai">&larr; Quay lại kho học liệu</a>
            </div>

            <!-- Nội dung chính -->
            <article class="chi-tiet-giao-an__noi-dung">
                <h2>Mô tả chi tiết bài học</h2>
                <p><?= e($ga->mo_ta) ?></p>

                <?php if (!empty($ga->hinh_anh)): ?>
                <figure>
                    <img src="<?= e(anhGiaoAn($ga->hinh_anh)) ?>"
                         alt="Hình ảnh minh họa cho <?= e($ga->ten) ?>"
                         loading="lazy" width="600" height="400"
                         class="anh-chi-tiet">
                </figure>
                <?php endif; ?>

                <?php if (!empty($ga->video_url)): ?>
                <div class="khoi-video-chi-tiet">
                    <h2>Video bài giảng minh họa</h2>
                    <iframe class="chi-tiet-giao-an__khung-xem chi-tiet-giao-an__khung-xem--chuan"
                            height="315"
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
                                    <a href="kho-hoc-lieu.php?dm=<?= $ga->danh_muc_id ?>" class="lien-ket-danh-muc">
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
                                        <span class="trang-thai-ton--con">Còn <?= $ga->ton_kho ?> lượt tải</span>
                                    <?php else: ?>
                                        <span class="trang-thai-ton--het">Đã hết hàng</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr class="bang-hoc-lieu__hang">
                                <th scope="row" class="bang-hoc-lieu__o-tieu-de">Giá tài liệu</th>
                                <td class="bang-hoc-lieu__o gia-noi-bat">
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
            <section class="gioi-thieu-tinh-nang gioi-thieu-tinh-nang--lien-quan">
                <h2>Giáo án liên quan cùng danh mục</h2>
                <div class="danh-sach-the__luoi">
                    <?php foreach ($dsLienQuan as $lq): ?>
                        <article class="the-hoc-lieu">
                            <img src="<?= e(anhGiaoAn($lq->hinh_anh)) ?>"
                                 alt="<?= e($lq->ten) ?>"
                                 class="the-hoc-lieu__anh"
                                 width="300" height="180" loading="lazy">
                            <div class="the-hoc-lieu__noi-dung">
                                <span class="nhan-nho <?= $lq->cap_hoc === 'THCS' ? 'nhan-nho--xanh' : ($lq->cap_hoc === 'THPT' ? 'nhan-nho--cam' : 'nhan-nho--tim') ?>">
                                    <?= e($lq->cap_hoc) ?>
                                </span>
                                <h3 class="the-hoc-lieu__tieu-de"><?= e($lq->ten) ?></h3>
                                <div class="the-hoc-lieu__chan">
                                    <p class="the-hoc-lieu__gia">
                                        <?= $lq->gia === 0 ? 'Miễn phí' : vnd($lq->gia) ?>
                                    </p>
                                    <a href="chi-tiet-giao-an.php?id=<?= $lq->id ?>"
                                       class="nut-thao-tac nut-thao-tac--xem nut-thao-tac--khoi">
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