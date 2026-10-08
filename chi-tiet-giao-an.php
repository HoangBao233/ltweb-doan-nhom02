<?php
/**
 * chi-tiet-giao-an.php — Trang chi tiết một giáo án
 */
require_once 'inc/config.php';
use App\Data\KhoGiaoAn;

// Lấy id từ URL
$id  = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$kho = new KhoGiaoAn(__DIR__ . '/data/giao-an.json');
$ga  = $id > 0 ? $kho->timTheoId($id) : null;

// Không tìm thấy → 404
if ($ga === null) {
    http_response_code(404);
    include '404.php';
    exit;
}

// Lưu lịch sử xem vào Cookie (Châu phụ trách - Chức năng 3)
$cu  = array_map('intval', explode(',', $_COOKIE['da_xem'] ?? ''));
$moi = array_unique([$ga->id, ...array_filter($cu)]);
setcookie('da_xem', implode(',', array_slice($moi, 0, 4)), [
    'expires'  => time() + 30 * 24 * 3600,
    'path'     => '/',
    'httponly' => true,
    'samesite' => 'Lax',
]);

$tieu_de_trang  = e($ga->ten);
$meta_mo_ta     = e($ga->mo_ta);
$trang_hien_tai = 'chi-tiet-giao-an';

require_once 'inc/header.php';
?>

    <main class="trang__chinh chi-tiet-giao-an">

        <div id="vung-chi-tiet" aria-live="polite">
            <h1 class="chi-tiet-giao-an__tieu-de"><?= e($ga->ten) ?></h1>

            <div style="margin-bottom: 2rem; display: flex; align-items: center; gap: 1rem;">
                <?php
                $dsLuu = new \App\Services\DanhSachLuu();
                $daLuu = $dsLuu->daLuu($ga->id);
                ?>
                <form action="gio-hang.php" method="POST" style="margin: 0;">
                    <input type="hidden" name="action" value="<?= $daLuu ? 'xoa' : 'them' ?>">
                    <input type="hidden" name="id" value="<?= $ga->id ?>">
                    <button type="submit" class="nut-thao-tac" style="cursor: pointer; border: none; font-size: 1rem; font-family: inherit; min-width: 120px; text-align: center;">
                        🔖 <?= $daLuu ? 'Đã lưu' : 'Lưu' ?>
                    </button>
                </form>
                <a href="kho-hoc-lieu.php" style="font-size: 0.9rem;">← Quay lại kho học liệu</a>
            </div>

            <!-- Nội dung chính -->
            <article class="chi-tiet-giao-an__noi-dung">
                <h2>Mô tả bài học</h2>
                <p><?= e($ga->mo_ta) ?></p>

                <?php if ($ga->hinh_anh): ?>
                <figure>
                    <img src="<?= e($ga->hinh_anh) ?>"
                         alt="Hình ảnh minh họa cho <?= e($ga->ten) ?>"
                         loading="lazy" width="600" height="400"
                         style="max-width: 100%; height: auto;">
                </figure>
                <?php endif; ?>

                <?php if ($ga->video_url): ?>
                <div style="margin-top: 2rem;">
                    <h2>Video hướng dẫn</h2>
                    <iframe class="chi-tiet-giao-an__khung-xem"
                            height="315"
                            style="width: 100%; max-width: 600px;"
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
                                <th scope="row" class="bang-hoc-lieu__o-tieu-de">Cấp học</th>
                                <td class="bang-hoc-lieu__o"><?= e($ga->cap_hoc) ?></td>
                            </tr>
                            <tr class="bang-hoc-lieu__hang">
                                <th scope="row" class="bang-hoc-lieu__o-tieu-de">Giá tiền</th>
                                <td class="bang-hoc-lieu__o" style="font-weight: bold; color: var(--mau-nhan);">
                                    <?= vnd($ga->gia) ?>
                                </td>
                            </tr>
                            <tr class="bang-hoc-lieu__hang">
                                <th scope="row" class="bang-hoc-lieu__o-tieu-de">Mã tài liệu</th>
                                <td class="bang-hoc-lieu__o">#<?= $ga->id ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </aside>
        </div>
    </main>

<?php require_once 'inc/footer.php'; ?>

    <script type="module" src="js/main.js"></script>
</body>
</html>
