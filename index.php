<?php
/**
 * index.php — Trang chủ ITeduShare
 */
require_once 'inc/config.php';
use App\Data\KhoGiaoAn;

$tieu_de_trang  = 'Trang chủ';
$meta_mo_ta     = 'Trang chủ ITeduShare: Nền tảng chia sẻ học liệu và giáo án môn Tin học chuẩn Chương trình GDPT 2018.';
$trang_hien_tai = 'index';

require_once 'inc/header.php';
?>

    <main class="trang__chinh">
        <!-- Khu vực nổi bật (hero) -->
        <section class="khu-vuc-noi-bat">
            <h1>Tổng quan về nền tảng ITeduShare</h1>
            <p>ITeduShare là kho lưu trữ bài giảng số, hỗ trợ giáo viên và sinh viên Sư phạm tải xuống các tài liệu giảng dạy môn Tin học chất lượng cao.</p>
            <img src="images/banner-trang-chu.jpg" alt="Học sinh trung học cơ sở đang thực hành tin học trên máy tính" width="800" height="400">
        </section>

        <!-- WIDGET THỜI TIẾT (Hiển thị API qua JS) -->
        <div class="bieu-mau" style="margin: 1rem auto; text-align: center; max-width: 350px; padding: 1rem; border-radius: 12px; background: var(--nen-phu, #f4f6f8);">
            <h2 style="font-size: 1.1rem; margin-bottom: 0.5rem; color: var(--mau-chinh, #004085);">⛅ Thời tiết Đà Nẵng hôm nay</h2>
            <div id="khung-thoi-tiet" aria-live="polite" style="min-height: 50px; display: flex; flex-direction: column; justify-content: center; align-items: center;">
                <p id="loi-thoi-tiet" style="font-size: 0.95rem; font-weight: bold; color: var(--chu, #333);">Đang đồng bộ dữ liệu...</p>
                <button type="button" id="nut-tai-lai-thoi-tiet" class="nut-thao-tac nut-thao-tac--xem" style="margin-top: 0.5rem; display: none; padding: 0.3rem 0.8rem; font-size: 0.85rem;">Thử lại</button>
            </div>
        </div>

        <!-- Hàng ba thẻ giới thiệu -->
        <section class="gioi-thieu-tinh-nang">
            <h2>Điểm nổi bật của nền tảng</h2>
            <div class="danh-sach-the__luoi">
                <article class="the-hoc-lieu">
                    <div class="the-hoc-lieu__noi-dung">
                        <h3 class="the-hoc-lieu__tieu-de">Giáo án chuẩn GDPT 2018</h3>
                        <p class="the-hoc-lieu__mo-ta">Hàng trăm giáo án được biên soạn kỹ lưỡng, bám sát chương trình mới nhất của Bộ GD&amp;ĐT.</p>
                    </div>
                </article>
                <article class="the-hoc-lieu">
                    <div class="the-hoc-lieu__noi-dung">
                        <h3 class="the-hoc-lieu__tieu-de">Hoàn toàn miễn phí</h3>
                        <p class="the-hoc-lieu__mo-ta">Tất cả tài liệu giảng dạy, bài tập và slide bài giảng đều có thể xem và tải xuống 0 đồng.</p>
                    </div>
                </article>
                <article class="the-hoc-lieu">
                    <div class="the-hoc-lieu__noi-dung">
                        <h3 class="the-hoc-lieu__tieu-de">Cộng đồng đóng góp</h3>
                        <p class="the-hoc-lieu__mo-ta">Giáo viên trên toàn quốc có thể dễ dàng chia sẻ tài liệu của mình để cùng lan tỏa tri thức.</p>
                    </div>
                </article>
            </div>
        </section>
        <?php
        // Đọc Cookie lịch sử xem
        $idsDaXem = array_filter(array_map('intval', explode(',', $_COOKIE['da_xem'] ?? '')));
        if (!empty($idsDaXem)):
            $kho = new KhoGiaoAn(__DIR__ . '/data/giao-an.json');
        ?>
        <section class="gioi-thieu-tinh-nang" style="margin-top: 3rem;">
            <h2>Đã xem gần đây</h2>
            <div class="danh-sach-the__luoi">
                <?php
                foreach ($idsDaXem as $idDaXem) {
                    $ga = $kho->timTheoId($idDaXem);
                    if ($ga):
                ?>
                    <article class="the-hoc-lieu">
                        <img src="<?= e($ga->hinh_anh) ?>" alt="<?= e($ga->ten) ?>" class="the-hoc-lieu__anh" width="300" height="180" loading="lazy">
                        <div class="the-hoc-lieu__noi-dung">
                            <span class="nhan-nho <?= $ga->cap_hoc === 'THCS' ? 'nhan-nho--xanh' : ($ga->cap_hoc === 'THPT' ? 'nhan-nho--cam' : 'nhan-nho--tim') ?>">
                                <?= e($ga->cap_hoc) ?>
                            </span>
                            <h3 class="the-hoc-lieu__tieu-de"><?= e($ga->ten) ?></h3>
                            <a href="chi-tiet-giao-an.php?id=<?= $ga->id ?>" class="nut-thao-tac nut-thao-tac--xem">Xem lại</a>
                        </div>
                    </article>
                <?php
                    endif;
                }
                ?>
            </div>
        </section>
        <?php endif; ?>
    </main>

<?php require_once 'inc/footer.php'; ?>

    <!-- JS chung cho mọi trang -->
    <script type="module" src="js/main.js"></script>
    <!-- JS riêng cho trang chủ (Widget thời tiết) -->
    <script type="module" src="js/trang-chu.js"></script>
</body>
</html>
