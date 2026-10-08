<?php
/**
 * Tệp: chi-tiet-giao-an.php — Trang chi tiết giáo án (Chức năng 3 - Châu phụ trách)
 * - Kiểm tra id hợp lệ qua filter_var (số nguyên dương), nếu không tìm thấy trả về 404 thân thiện.
 * - Ghi nhận cookie 'da_xem' (tối đa 4 ID giáo án mới nhất, 30 ngày, HttpOnly, SameSite=Lax) TRƯỚC mọi output.
 * - Nút "Thêm vào danh sách lưu" dưới dạng form POST gửi id giáo án tới gio-hang.php.
 */
require_once 'inc/config.php';
require_once 'src/Data/KhoGiaoAn.php';

// 1. Kiểm tra id từ URL bằng filter_var (chuẩn Rubric đề bài)
$id  = filter_var($_GET['id'] ?? '', FILTER_VALIDATE_INT, [
    'options' => ['min_range' => 1]
]);
$kho = new KhoGiaoAn();
$ga  = $id ? $kho->timTheoId($id) : null;

// Dự phòng an toàn: tự động xử lý nếu lớp KhoGiaoAn của nhóm bị lỗi UTF-8 BOM hoặc lỗi đường dẫn trên Windows
if ($ga === null && $id) {
    if (!class_exists('GiaoAn') && is_file(__DIR__ . '/src/Models/GiaoAn.php')) {
        require_once __DIR__ . '/src/Models/GiaoAn.php';
    }
    $tepJson = __DIR__ . '/data/giao-an.json';
    if (is_file($tepJson)) {
        $raw = file_get_contents($tepJson);
        if ($raw !== false) {
            // Loại bỏ ký tự UTF-8 BOM ẩn (\xEF\xBB\xBF) của Windows để json_decode phân tích chuẩn xác
            $raw = preg_replace('/^\xEF\xBB\xBF/', '', $raw);
            $mang = json_decode($raw, true);
            if (is_array($mang)) {
                foreach ($mang as $item) {
                    if ((int)($item['id'] ?? 0) === $id) {
                        $ga = GiaoAn::tuMang($item);
                        break;
                    }
                }
            }
        }
    }
}

// 2. Không tìm thấy giáo án (id thiếu, id sai kiểu, hoặc không tồn tại) → Trả về 404
if ($ga === null) {
    http_response_code(404);
    require_once __DIR__ . '/404.php';
    exit;
}

// 3. Ghi nhận Cookie "Đã xem gần đây": tối đa 4 id, mới nhất đứng đầu
// setcookie() gửi HTTP Header nên BẮT BUỘC phải gọi TRƯỚC mọi output (trước header.php)
$da_xem_cu  = array_map('intval', explode(',', $_COOKIE['da_xem'] ?? ''));
$da_xem_moi = array_unique([$ga->id, ...array_filter($da_xem_cu, fn($x) => $x > 0)]);
setcookie('da_xem', implode(',', array_slice($da_xem_moi, 0, 4)), [
    'expires'  => time() + 30 * 24 * 3600, // 30 ngày
    'path'     => '/',
    'httponly' => true,
    'samesite' => 'Lax',
]);

// 4. Thiết lập tiêu đề và meta mô tả cho khung trang dùng chung
$tieu_de_trang  = $ga->ten;
$meta_mo_ta     = $ga->mo_ta;
$trang_hien_tai = 'chi-tiet-giao-an';

require_once 'inc/header.php';
?>

    <main class="trang__chinh chi-tiet-giao-an">

        <div id="vung-chi-tiet" aria-live="polite">
            <h1 class="chi-tiet-giao-an__tieu-de"><?= e($ga->ten) ?></h1>

            <div style="margin-bottom: 2rem; display: flex; align-items: center; flex-wrap: wrap; gap: 1rem;">
                <!-- Form POST Thêm vào danh sách lưu (Tương đương giỏ hàng) -->
                <form method="POST" action="gio-hang.php" style="display: inline-block; margin: 0;">
                    <input type="hidden" name="action" value="them">
                    <input type="hidden" name="id" value="<?= $ga->id ?>">
                    <button type="submit" class="nut-thao-tac nut-thao-tac--xem" style="cursor: pointer; border: none; font-family: inherit;">🔖 Thêm vào danh sách lưu</button>
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
