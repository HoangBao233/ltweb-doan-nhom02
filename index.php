<?php
/**
 * index.php — Trang chủ ITeduShare
 * 
 * Đồ án Lập trình Web - Nhóm 02
 * Phụ trách: Lê Hoàng Bảo (MSSV: 3120124003) — Phần A, Chức năng 1
 */

require_once 'inc/config.php';
use App\Data\KhoGiaoAn;

$tieu_de_trang  = 'Trang chủ';
$meta_mo_ta     = 'Trang chủ ITeduShare: Nền tảng chia sẻ học liệu và giáo án môn Tin học chuẩn Chương trình GDPT 2018.';
$trang_hien_tai = 'index';

// Khởi tạo KhoGiaoAn kết nối PDO MySQL
$khoGiaoAn = new KhoGiaoAn();

// 1. Lấy 8 giáo án mới nhất từ MySQL
$dsMoiNhat = $khoGiaoAn->moiNhat(8);

// 2. Lấy danh sách giáo án đã xem từ Cookie bằng 1 CÂU TRUY VẤN DUY NHẤT (chống lỗi N+1)
$idsDaXem = array_values(array_filter(array_map('intval', explode(',', $_COOKIE['da_xem'] ?? '')), fn($id) => $id > 0));
$dsDaXem = !empty($idsDaXem) ? $khoGiaoAn->theoDanhSachId($idsDaXem) : [];

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
        <div class="bieu-mau" style="margin: 1.5rem auto; text-align: center; max-width: 350px; padding: 1rem; border-radius: 12px; background: var(--nen-phu, #f4f6f8);">
            <h2 style="font-size: 1.1rem; margin-bottom: 0.5rem; color: var(--mau-chinh, #004085);">⛅ Thời tiết Đà Nẵng hôm nay</h2>
            <div id="khung-thoi-tiet" aria-live="polite" style="min-height: 50px; display: flex; flex-direction: column; justify-content: center; align-items: center;">
                <p id="loi-thoi-tiet" style="font-size: 0.95rem; font-weight: bold; color: var(--chu, #333);">Đang đồng bộ dữ liệu...</p>
                <button type="button" id="nut-tai-lai-thoi-tiet" class="nut-thao-tac nut-thao-tac--xem" style="margin-top: 0.5rem; display: none; padding: 0.3rem 0.8rem; font-size: 0.85rem;">Thử lại</button>
            </div>
        </div>

        <!-- Hàng ba thẻ giới thiệu điểm nổi bật -->
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
                        <h3 class="the-hoc-lieu__tieu-de">Hoàn toàn minh bạch</h3>
                        <p class="the-hoc-lieu__mo-ta">Tất cả tài liệu giảng dạy, bài tập và slide bài giảng đều được quản lý trực tiếp trên hệ thống CSDL MySQL.</p>
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

        <!-- KHU VỰC 8 GIÁO ÁN MỚI NHẤT TỪ MYSQL -->
        <section class="gioi-thieu-tinh-nang" style="margin-top: 3rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
                <h2 style="margin: 0;">📚 Giáo án &amp; Học liệu mới cập nhật</h2>
                <a href="kho-hoc-lieu.php" class="nut-thao-tac nut-thao-tac--xem" style="text-decoration: none;">Xem tất cả kho học liệu &rarr;</a>
            </div>

            <?php if (!empty($dsMoiNhat)): ?>
                <div class="danh-sach-the__luoi">
                    <?php foreach ($dsMoiNhat as $ga): ?>
                        <article class="the-hoc-lieu">
                            <img src="<?= e($ga->hinh_anh ?: 'images/banner-trang-chu.jpg') ?>"
                                 alt="<?= e($ga->ten) ?>"
                                 class="the-hoc-lieu__anh"
                                 width="300" height="180" loading="lazy">
                            <div class="the-hoc-lieu__noi-dung">
                                <div style="display: flex; justify-content: space-between; align-items: center;">
                                    <span class="nhan-nho <?= $ga->cap_hoc === 'THCS' ? 'nhan-nho--xanh' : ($ga->cap_hoc === 'THPT' ? 'nhan-nho--cam' : 'nhan-nho--tim') ?>">
                                        <?= e($ga->cap_hoc) ?>
                                    </span>
                                    <span style="font-size: 0.85rem; color: #666;">
                                        <?= $ga->ton_kho > 0 ? "Còn {$ga->ton_kho}" : '<strong style="color: red;">Hết hàng</strong>' ?>
                                    </span>
                                </div>
                                <h3 class="the-hoc-lieu__tieu-de"><?= e($ga->ten) ?></h3>
                                <p class="the-hoc-lieu__mo-ta"><?= e($ga->mo_ta) ?></p>
                                <div style="margin-top: auto; text-align: center;">
                                    <p style="font-weight: bold; color: var(--mau-chinh, #007bff); margin-bottom: 0.75rem;">
                                        <?= $ga->gia === 0 ? 'Miễn phí' : vnd($ga->gia) ?>
                                    </p>
                                    <a href="chi-tiet-giao-an.php?id=<?= $ga->id ?>"
                                       class="nut-thao-tac nut-thao-tac--xem"
                                       style="display: block; text-align: center; text-decoration: none;">
                                        Xem chi tiết
                                    </a>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p style="text-align: center; color: #666;">Hiện chưa có giáo án nào trong cơ sở dữ liệu.</p>
            <?php endif; ?>
        </section>

        <!-- KHU VỰC ĐÃ XEM GẦN ĐÂY (TRUY VẤN 1 CÂU SQL DUY NHẤT) -->
        <?php if (!empty($dsDaXem)): ?>
            <section class="gioi-thieu-tinh-nang" style="margin-top: 3rem;">
                <h2>🕒 Giáo án bạn đã xem gần đây</h2>
                <div class="danh-sach-the__luoi">
                    <?php foreach ($dsDaXem as $ga): ?>
                        <article class="the-hoc-lieu">
                            <img src="<?= e($ga->hinh_anh ?: 'images/banner-trang-chu.jpg') ?>"
                                 alt="<?= e($ga->ten) ?>"
                                 class="the-hoc-lieu__anh"
                                 width="300" height="180" loading="lazy">
                            <div class="the-hoc-lieu__noi-dung">
                                <span class="nhan-nho <?= $ga->cap_hoc === 'THCS' ? 'nhan-nho--xanh' : ($ga->cap_hoc === 'THPT' ? 'nhan-nho--cam' : 'nhan-nho--tim') ?>">
                                    <?= e($ga->cap_hoc) ?>
                                </span>
                                <h3 class="the-hoc-lieu__tieu-de"><?= e($ga->ten) ?></h3>
                                <div style="margin-top: auto; text-align: center;">
                                    <p style="font-weight: bold; color: var(--mau-chinh, #007bff); margin-bottom: 0.75rem;">
                                        <?= $ga->gia === 0 ? 'Miễn phí' : vnd($ga->gia) ?>
                                    </p>
                                    <a href="chi-tiet-giao-an.php?id=<?= $ga->id ?>"
                                       class="nut-thao-tac nut-thao-tac--xem"
                                       style="display: block; text-align: center; text-decoration: none;">
                                        Xem lại
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

    <!-- JS chung cho mọi trang -->
    <script type="module" src="js/main.js"></script>
    <!-- JS riêng cho trang chủ (Widget thời tiết) -->
    <script type="module" src="js/trang-chu.js"></script>
</body>
</html>
