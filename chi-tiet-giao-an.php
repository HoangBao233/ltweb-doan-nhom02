<?php
/**
 * chi-tiet-giao-an.php — Trang chi tiết một giáo án
 */
require_once 'inc/config.php';
require_once 'src/Data/KhoGiaoAn.php';
require_once 'src/Services/DanhSachLuu.php';

// Lấy id từ URL
$id  = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$kho = new KhoGiaoAn();
$ga  = $id > 0 ? $kho->timTheoId($id) : null;

// Không tìm thấy → 404
if ($ga === null) {
    http_response_code(404);
    include '404.php';
    exit;
}

$da_luu = DanhSachLuu::daLuu($ga->id);

$tieu_de_trang  = e($ga->ten);
$meta_mo_ta     = e($ga->mo_ta);
$trang_hien_tai = 'chi-tiet-giao-an';

require_once 'inc/header.php';
?>

    <main class="trang__chinh chi-tiet-giao-an">

        <div id="vung-chi-tiet" aria-live="polite">
            <h1 class="chi-tiet-giao-an__tieu-de"><?= e($ga->ten) ?></h1>

            <!-- Nút Lưu / Đã lưu -->
            <div style="margin-bottom: 2rem;">
                <?php if ($da_luu): ?>
                    <form method="POST" action="gio-hang.php" style="display:inline;">
                        <input type="hidden" name="action" value="xoa">
                        <input type="hidden" name="id" value="<?= $ga->id ?>">
                        <input type="hidden" name="quay_lai" value="chi-tiet-giao-an.php?id=<?= $ga->id ?>">
                        <button type="submit" class="nut-thao-tac" style="background:#e57373; color:#fff;">✅ Đã lưu — Bỏ lưu</button>
                    </form>
                <?php else: ?>
                    <form method="POST" action="gio-hang.php" style="display:inline;">
                        <input type="hidden" name="action" value="them">
                        <input type="hidden" name="id" value="<?= $ga->id ?>">
                        <input type="hidden" name="quay_lai" value="chi-tiet-giao-an.php?id=<?= $ga->id ?>">
                        <button type="submit" class="nut-thao-tac">🔖 Lưu bài học</button>
                    </form>
                <?php endif; ?>
                <a href="kho-hoc-lieu.php" style="margin-left: 1rem; font-size: 0.9rem;">← Quay lại kho học liệu</a>
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
