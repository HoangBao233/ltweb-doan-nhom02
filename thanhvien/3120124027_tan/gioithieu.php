<?php
// thanhvien/3120124027_tan/gioithieu.php — Trang cá nhân Nguyễn Cảnh Tấn
// Phụ trách: Tấn (MSSV 3120124027) — Phần C Bài tập 5
// Cách thử: mở http://localhost/ltweb-doan-nhom02/thanhvien/3120124027_tan/gioithieu.php
//           thử ?nhom=backend, ?nhom=frontend, ?nhom=database để lọc kỹ năng
//           mỗi lần mở (phiên mới) bộ đếm tăng 1
require __DIR__ . '/../../inc/config.php';

$goc    = '../../';
$tieuDe = 'Nguyễn Cảnh Tấn | ITeduShare';
$trang  = '';

// =====================================================================
// CHỨC NĂNG PHP 1 — Bộ đếm lượt xem (mỗi phiên chỉ tính 1 lần)
// =====================================================================
$tepDem = __DIR__ . '/../../storage/3120124027_luotxem.json';

// Đọc dữ liệu JSON hiện tại (nếu có)
$luotXem = 0;
if (file_exists($tepDem)) {
    $duLieuJson = json_decode(file_get_contents($tepDem), true);
    if (is_array($duLieuJson) && isset($duLieuJson['luotXem'])) {
        $luotXem = (int)$duLieuJson['luotXem'];
    }
}

// Kiểm tra xem phiên này đã được tính chưa
if (empty($_SESSION['da_xem_tan'])) {
    $_SESSION['da_xem_tan'] = true;
    
    // Tăng số đếm
    $luotXem++;
    
    // Ghi lại vào file dưới định dạng chuẩn JSON
    file_put_contents($tepDem, json_encode(['luotXem' => $luotXem]), LOCK_EX);
}

// =====================================================================
// CHỨC NĂNG PHP 2 — Danh sách kỹ năng lọc theo nhóm bằng tham số GET
// =====================================================================
$tatCaKyNang = [
    ['ten' => 'Python (Tkinter, Pygame, OpenCV)',   'nhom' => 'backend'],
    ['ten' => 'PHP 8 (OOP, Composer)',               'nhom' => 'backend'],
    ['ten' => 'PostgreSQL, SQL Server',              'nhom' => 'database'],
    ['ten' => 'Quản trị Linux',                      'nhom' => 'backend'],
    ['ten' => 'HTML5, CSS3 (BEM, Responsive)',        'nhom' => 'frontend'],
    ['ten' => 'JavaScript ES6+',                     'nhom' => 'frontend'],
    ['ten' => 'Git & GitHub',                        'nhom' => 'devops'],
    ['ten' => 'Gamification trong dạy học',          'nhom' => 'giaoduc'],
    ['ten' => 'Phương pháp dạy học Tin học',         'nhom' => 'giaoduc'],
];

$nhomHopLe  = ['tat-ca', 'frontend', 'backend', 'database', 'devops', 'giaoduc'];
$nhomChon   = trim($_GET['nhom'] ?? 'tat-ca');
if (!in_array($nhomChon, $nhomHopLe, true)) $nhomChon = 'tat-ca';

// Lọc theo nhóm
$kyNangHienThi = $nhomChon === 'tat-ca'
    ? $tatCaKyNang
    : array_values(array_filter($tatCaKyNang, fn($k) => $k['nhom'] === $nhomChon));

require __DIR__ . '/../../inc/header.php';
?>
    <!-- CSS Cá nhân của Tấn -->
    <link rel="stylesheet" href="<?= $goc ?>thanhvien/3120124027_tan/css/canhan-tan.css">

    <!-- Nút Quay về -->
    <nav class="thanh-dieu-huong" aria-label="Điều hướng phụ" style="background: var(--nen-phu, #f4f6f8); padding: 0.75rem 1rem;">
        <ul style="display: flex; gap: 1rem; list-style: none; padding: 0; margin: 0; flex-wrap: wrap;">
            <li><a href="<?= $goc ?>index.php" class="menu-chinh__lien-ket">⬅ Quay về Trang chủ</a></li>
            <li><a href="<?= $goc ?>ve-chung-toi.php" class="menu-chinh__lien-ket">⬅ Quay về Giới thiệu Nhóm</a></li>
        </ul>
    </nav>

    <main class="trang-ca-nhan">
        <!-- CỘT TRÁI -->
        <div class="cot-trai">
            <img src="<?= $goc ?>thanhvien/3120124027_tan/avatar-tan.jpg"
                 alt="Nguyễn Cảnh Tấn" class="anh-dai-dien">
            <h1>Nguyễn Cảnh Tấn</h1>
            <p>MSSV: 3120124027</p>
            <p>Ngành: Sư phạm Tin học</p>
            <p>Trường Đại học Sư phạm – ĐH Đà Nẵng</p>

            <!-- ===================================================== -->
            <!-- CHỨC NĂNG PHP 1 — Bộ đếm lượt xem (phía máy chủ)     -->
            <!-- ===================================================== -->
            <div class="cong-cu-doc" style="margin-top: 1.5rem; padding: 1rem;
                 background: #f0f8ff; border: 1px solid #bee3f8; border-radius: 8px;">
                <h2 style="font-size: 1rem; margin-bottom: 0.5rem;">📊 Lượt xem trang</h2>
                <p style="font-size: 2rem; font-weight: bold; color: #2b6cb0; margin: 0;">
                    <?= number_format($luotXem) ?>
                </p>
                <p style="font-size: 0.8rem; color: #666; margin-top: 0.25rem;">
                    lượt xem (mỗi phiên tính 1 lần)
                </p>
            </div>

            <!-- Hỗ trợ đọc (từ bài 4, giữ nguyên) -->
            <div class="cong-cu-doc" style="margin-top: 1rem;">
                <h2>Hỗ trợ đọc</h2>
                <div class="nhom-nut">
                    <button type="button" id="btn-giam-chu" class="nut-cong-cu">A-</button>
                    <button type="button" id="btn-mac-dinh" class="nut-cong-cu">A</button>
                    <button type="button" id="btn-tang-chu" class="nut-cong-cu">A+</button>
                </div>
            </div>
        </div>

        <!-- CỘT PHẢI -->
        <div class="cot-phai">
            <!-- Đồng hồ đếm ngược (giữ từ bài 4) -->
            <div class="khung-dem-nguoc">
                <h2>⏳ Đếm ngược đến ngày báo cáo Thiết kế lập trình Web</h2>
                <div id="dong-ho-thi" class="dong-ho" aria-live="polite" aria-atomic="true">
                    Đang tải thời gian...
                </div>
            </div>

            <!-- ===================================================== -->
            <!-- CHỨC NĂNG PHP 2 — Danh sách kỹ năng lọc theo nhóm    -->
            <!-- ===================================================== -->
            <section aria-labelledby="tieu-de-ky-nang">
                <h2 id="tieu-de-ky-nang">Kỹ năng &amp; Chuyên môn</h2>

                <!-- Bộ lọc nhóm — form GET để tắt JS vẫn dùng được -->
                <form method="GET" action="" style="margin-bottom: 1rem; display: flex; flex-wrap: wrap; gap: 0.5rem;">
                    <?php
                    $nhanNhom = ['tat-ca' => '🌐 Tất cả', 'frontend' => '🎨 Frontend',
                                 'backend' => '⚙️ Backend', 'database' => '🗄️ Database',
                                 'devops'  => '🔧 DevOps',  'giaoduc'  => '📚 Giáo dục'];
                    foreach ($nhanNhom as $val => $nhan):
                        $active = $val === $nhomChon;
                    ?>
                        <button type="submit" name="nhom" value="<?= e($val) ?>"
                                style="padding: 0.4rem 0.9rem; border-radius: 20px; border: 1px solid #ccc; cursor: pointer;
                                       <?= $active ? 'background:#2b6cb0; color:#fff; font-weight:bold;' : 'background:#fff;' ?>">
                            <?= e($nhan) ?>
                        </button>
                    <?php endforeach; ?>
                </form>

                <?php if (empty($kyNangHienThi)): ?>
                    <p style="color: #666; font-style: italic;">Không có kỹ năng nào trong nhóm này.</p>
                <?php else: ?>
                    <ul class="danh-sach-ky-nang" role="list">
                        <?php foreach ($kyNangHienThi as $ky): ?>
                            <li><?= e($ky['ten']) ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </section>

            <!-- Thời khóa biểu (giữ nguyên từ bài 4) -->
            <h2>Thời khóa biểu (Học kỳ hiện tại)</h2>
            <div class="bang-cuon-ngang">
                <table>
                    <thead>
                        <tr><th>Thứ</th><th>Thời gian</th><th>Môn học</th><th>Phòng học</th><th>Giảng viên</th></tr>
                    </thead>
                    <tbody>
                        <tr><td rowspan="2"><strong>Thứ 2</strong></td><td>07:00 - 08:40</td><td>An Toàn Thông Tin</td><td>Phòng B3-206</td><td>Đoàn Duy Bình</td></tr>
                        <tr><td>18:00</td><td><strong>Học Tiếng Anh</strong></td><td>Thể thao</td><td></td></tr>
                        <tr><td><strong>Thứ 3</strong></td><td>07:00 - 09:35</td><td>Phân Tích và Thiết Kế HTTT</td><td>Phòng B1.106</td><td>Lê Thị Thanh Bình</td></tr>
                        <tr><td rowspan="3"><strong>Thứ 4</strong></td><td>09:40 - 12:15</td><td>Phương Pháp Dạy Học Bộ Môn Tin Học</td><td>Phòng A5-406</td><td>Lê Viết Chung</td></tr>
                        <tr><td>13:00 - 16:30</td><td>Hệ Quản Trị Cơ Sở Dữ Liệu</td><td>Phòng B3-404</td><td>Đoàn Duy Bình</td></tr>
                        <tr><td>18:00</td><td><strong>Học Tiếng Anh</strong></td><td>Thể thao</td><td></td></tr>
                        <tr><td><strong>Thứ 5</strong></td><td>07:00 - 08:40</td><td>Lịch Sử Đảng Cộng Sản Việt Nam</td><td>Phòng A6-502</td><td>Nguyễn Thế Hà</td></tr>
                        <tr><td><strong>Thứ 6</strong></td><td>18:00</td><td><strong>Học Tiếng Anh</strong></td><td>Thể thao</td><td></td></tr>
                        <tr><td><strong>Thứ 7</strong></td><td>07:00 - 09:35</td><td>Thiết Kế và Lập Trình Web</td><td>Phòng B3-303</td><td>Nguyễn Hoàng Hải</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- JS cá nhân (đồng hồ, hỗ trợ đọc — giữ từ bài 4) -->
    <script type="module" src="<?= $goc ?>thanhvien/3120124027_tan/js/canhan.js"></script>

<?php require __DIR__ . '/../../inc/footer.php'; ?>

