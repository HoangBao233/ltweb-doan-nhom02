<?php
// thanhvien/3120124003_bao/gioithieu.php
require __DIR__ . '/../../inc/config.php';
$goc = '../../';
$tieu_de_trang = 'Hồ sơ: Lê Hoàng Bảo';
$trang_hien_tai = '';

// =========================================================================
// CHỨC NĂNG PHP 1: SỔ LƯU BÚT (GUESTBOOK)
// =========================================================================
$fileLuuBut = __DIR__ . '/../../storage/3120124003_luubut.jsonl';
$loiLuuBut = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'luu_but') {
    $ten = trim($_POST['ten'] ?? '');
    $loi_nhan = trim($_POST['loi_nhan'] ?? '');

    if (mb_strlen($ten, 'UTF-8') < 2) {
        $loiLuuBut = 'Tên phải có ít nhất 2 kí tự.';
    } elseif (mb_strlen($loi_nhan, 'UTF-8') < 5) {
        $loiLuuBut = 'Lời nhắn quá ngắn (ít nhất 5 kí tự).';
    } else {
        $data = [
            'ten' => $ten,
            'loi_nhan' => $loi_nhan,
            'thoi_gian' => date('d/m/Y H:i:s')
        ];
        // Lưu vào file JSONL
        file_put_contents($fileLuuBut, json_encode($data, JSON_UNESCAPED_UNICODE) . PHP_EOL, FILE_APPEND);
        
        // Dùng PRG
        $_SESSION['flash_luubut'] = 'Cảm ơn bạn đã để lại lời nhắn!';
        header("Location: gioithieu.php");
        exit;
    }
}

// Đọc 5 lời nhắn mới nhất
$danhSachLuuBut = [];
if (file_exists($fileLuuBut)) {
    $lines = file($fileLuuBut, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $d = json_decode($line, true);
        if ($d) $danhSachLuuBut[] = $d;
    }
}
$danhSachLuuBut = array_slice(array_reverse($danhSachLuuBut), 0, 5); // Lấy 5 dòng cuối cùng lật ngược lại
$flashLuuBut = $_SESSION['flash_luubut'] ?? '';
unset($_SESSION['flash_luubut']);

// =========================================================================
// CHỨC NĂNG PHP 2: BÌNH CHỌN CÔNG NGHỆ (MINI POLL)
// =========================================================================
$fileBinhChon = __DIR__ . '/../../storage/3120124003_binhchon.json';
$loiBinhChon = '';
$cacTuyChon = ['PHP', 'Python', 'JavaScript', 'C++'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'binh_chon') {
    $lua_chon = $_POST['ngon_ngu'] ?? '';
    
    if (in_array($lua_chon, $cacTuyChon)) {
        // Đọc file cũ
        $ketQua = file_exists($fileBinhChon) ? json_decode(file_get_contents($fileBinhChon), true) : [];
        if (!isset($ketQua[$lua_chon])) $ketQua[$lua_chon] = 0;
        $ketQua[$lua_chon]++;
        
        file_put_contents($fileBinhChon, json_encode($ketQua));
        $_SESSION['flash_binhchon'] = 'Bình chọn thành công!';
        header("Location: gioithieu.php");
        exit;
    } else {
        $loiBinhChon = 'Vui lòng chọn một ngôn ngữ hợp lệ.';
    }
}

$ketQuaBinhChon = file_exists($fileBinhChon) ? json_decode(file_get_contents($fileBinhChon), true) : [];
$tongPhieu = array_sum($ketQuaBinhChon);
$flashBinhChon = $_SESSION['flash_binhchon'] ?? '';
unset($_SESSION['flash_binhchon']);

require __DIR__ . '/../../inc/header.php';
?>
<!-- Link CSS cá nhân -->
<link rel="stylesheet" href="css/canhan-bao.css">

<!-- Nút điều hướng quay lại -->
<nav class="thanh-dieu-huong" aria-label="Menu chính" style="margin-bottom: 1rem;">
  <ul class="menu-chinh" style="display: flex; gap: 1rem; list-style: none; background: #f4f6f8; padding: 1rem; border-radius: 8px;">
    <li><a href="../../index.php" style="text-decoration: none; font-weight: bold; color: #004085;">⬅ Về Trang chủ</a></li>
    <li><a href="../../ve-chung-toi.php" style="text-decoration: none; font-weight: bold; color: #004085;">⬅ Về trang Nhóm</a></li>
  </ul>
</nav>

<main class="trang__chinh phan-lien-he">
  <h1 class="phan-lien-he__tieu-de can-giua-chu">Hồ sơ thành viên: Lê Hoàng Bảo</h1>

  <div style="display: flex; flex-wrap: wrap; gap: 2rem; margin-bottom: 2rem;">
    <!-- Cột trái: Giới thiệu bản thân (Lấy từ HTML tĩnh) -->
    <section class="bieu-mau" style="flex: 1; min-width: 300px;">
      <h2>Giới thiệu bản thân</h2>
      <div class="gioi-thieu-grid">
          <div class="anh-dai-dien">
             <!-- Giữ lại class anh-chan-dung để JS hoạt động -->
             <img src="avatar-bao.jpg" alt="Ảnh chân dung của Lê Hoàng Bảo" class="anh-chan-dung" style="width: 200px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
          </div>
          <div class="noi-dung-gioi-thieu">
             <p>Xin chào, tôi là <strong>Lê Hoàng Bảo</strong>, sinh viên Khoa Toán - Tin, Trường Đại học Sư phạm - Đại học Đà Nẵng. Tôi là người đảm nhận nhiệm vụ trưởng nhóm cho dự án website này.</p>
             
             <h3>Danh sách kỹ năng nổi bật</h3>
             <ul class="danh-sach-ky-nang">
               <li>Lập trình Python (Pygame, Tkinter, OpenCV)</li>
               <li>Thiết kế CSDL (PostgreSQL, SQL Server)</li>
               <li>HTML5 & CSS3, PHP</li>
               <li>Quản lý mã nguồn với Git/GitHub</li>
             </ul>
          </div>
      </div>
    </section>

    <!-- Cột phải: 2 Chức năng động (Sổ lưu bút + Khảo sát) -->
    <div style="flex: 1; min-width: 300px; display: flex; flex-direction: column; gap: 2rem;">
      <!-- Chức năng 1 -->
      <section class="bieu-mau" style="background-color: #fffdf5; border-color: #ffeeba;">
        <h2 style="color: #856404;">📝 Sổ Lưu Bút Khách Thăm</h2>
        
        <?php if ($flashLuuBut): ?>
            <div style="padding: 10px; background: #d4edda; color: #155724; margin-bottom: 10px; border-radius: 4px;">
                <?= e($flashLuuBut) ?>
            </div>
        <?php endif; ?>
        <?php if ($loiLuuBut): ?>
            <div style="padding: 10px; background: #f8d7da; color: #721c24; margin-bottom: 10px; border-radius: 4px;">
                <?= e($loiLuuBut) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="gioithieu.php">
            <input type="hidden" name="action" value="luu_but">
            <div style="margin-bottom: 10px;">
                <label for="ten_luubut" style="font-weight: bold; display: block; margin-bottom: 5px;">Tên của bạn:</label>
                <input type="text" id="ten_luubut" name="ten" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;" required>
            </div>
            <div style="margin-bottom: 10px;">
                <label for="loi_nhan_luubut" style="font-weight: bold; display: block; margin-bottom: 5px;">Lời nhắn:</label>
                <textarea id="loi_nhan_luubut" name="loi_nhan" rows="3" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;" required></textarea>
            </div>
            <button type="submit" class="nut-thao-tac nut-thao-tac--sua" style="width: 100%;">Gửi Lời Nhắn</button>
        </form>

        <hr style="margin: 15px 0; border: 0; border-top: 1px solid #ddd;">
        
        <h3 style="font-size: 1.1rem; margin-bottom: 10px;">5 Lời nhắn gần đây:</h3>
        <?php if (empty($danhSachLuuBut)): ?>
            <p style="color: #666; font-style: italic;">Chưa có lời nhắn nào.</p>
        <?php else: ?>
            <ul style="list-style: none; padding: 0;">
                <?php foreach ($danhSachLuuBut as $lb): ?>
                    <li style="background: #fff; padding: 10px; border: 1px solid #eee; border-radius: 4px; margin-bottom: 10px;">
                        <strong style="color: #0056b3;"><?= e($lb['ten'] ?? 'Ẩn danh') ?></strong> 
                        <span style="font-size: 0.85em; color: #999;">(<?= e($lb['thoi_gian'] ?? '') ?>)</span>
                        <p style="margin: 5px 0 0 0;"><?= nl2br(e($lb['loi_nhan'] ?? '')) ?></p>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
      </section>

      <!-- Chức năng 2 -->
      <section class="bieu-mau" style="background-color: #f0f8ff; border-color: #b8daff;">
        <h2 style="color: #004085;">📊 Khảo sát Công Nghệ</h2>
        <p>Ngôn ngữ lập trình bạn yêu thích nhất là gì?</p>

        <?php if ($flashBinhChon): ?>
            <div style="padding: 10px; background: #d4edda; color: #155724; margin-bottom: 10px; border-radius: 4px;">
                <?= e($flashBinhChon) ?>
            </div>
        <?php endif; ?>
        <?php if ($loiBinhChon): ?>
            <div style="padding: 10px; background: #f8d7da; color: #721c24; margin-bottom: 10px; border-radius: 4px;">
                <?= e($loiBinhChon) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="gioithieu.php">
            <input type="hidden" name="action" value="binh_chon">
            <div style="display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 15px;">
                <?php foreach ($cacTuyChon as $tc): ?>
                    <label style="background: #fff; padding: 5px 10px; border: 1px solid #ccc; border-radius: 4px; cursor: pointer;">
                        <input type="radio" name="ngon_ngu" value="<?= e($tc) ?>" required> <?= e($tc) ?>
                    </label>
                <?php endforeach; ?>
            </div>
            <button type="submit" class="nut-thao-tac nut-thao-tac--xem" style="width: 100%;">Bình chọn</button>
        </form>

        <hr style="margin: 15px 0; border: 0; border-top: 1px solid #ddd;">

        <h3 style="font-size: 1.1rem; margin-bottom: 10px;">Kết quả (<?= $tongPhieu ?> phiếu):</h3>
        <?php if ($tongPhieu > 0): ?>
            <div style="display: flex; flex-direction: column; gap: 10px;">
                <?php foreach ($cacTuyChon as $tc): 
                    $soPhieu = $ketQuaBinhChon[$tc] ?? 0;
                    $phanTram = $tongPhieu > 0 ? round(($soPhieu / $tongPhieu) * 100) : 0;
                ?>
                <div>
                    <div style="display: flex; justify-content: space-between; font-size: 0.9em; margin-bottom: 3px;">
                        <span><?= e($tc) ?></span>
                        <span><?= $phanTram ?>% (<?= $soPhieu ?> phiếu)</span>
                    </div>
                    <div style="background: #e9ecef; height: 10px; border-radius: 5px; overflow: hidden;">
                        <div style="background: #28a745; height: 100%; width: <?= $phanTram ?>%;"></div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p style="color: #666; font-style: italic;">Chưa có bình chọn nào.</p>
        <?php endif; ?>
      </section>
    </div>
  </div>

  <article class="bieu-mau" style="margin-bottom: 2rem;">
    <h2>Vai trò trong dự án ITeduShare & Sở thích</h2>
    <p>Hiện tại tôi đang đảm nhận vai trò trưởng nhóm trong dự án IT-EduShare. Ngoài ra, tôi cũng có nhiều sở thích như đọc sách, chơi game và tham gia các hoạt động thể thao.</p>
    <button type="button" class="nut-thao-tac nut-thao-tac--xem nut-lien-he-ca-nhan">Kết nối với tôi</button>
  </article>

  <section class="thong-tin">
    <h2>Thời khóa biểu cá nhân</h2>
    <div class="khung-cuon-bang">
      <table class="bang-hoc-lieu" style="width: 100%; border-collapse: collapse; text-align: left;">
        <caption class="bang-hoc-lieu__chu-thich">Kế hoạch học tập và làm việc trong tuần</caption>
        <thead>
          <tr>
            <th scope="col" style="padding: 0.75rem; border: 1px solid #ddd; background: #004085; color: white;">Thứ</th>
            <th scope="col" style="padding: 0.75rem; border: 1px solid #ddd; background: #004085; color: white;">Sáng</th>
            <th scope="col" style="padding: 0.75rem; border: 1px solid #ddd; background: #004085; color: white;">Chiều</th>
            <th scope="col" style="padding: 0.75rem; border: 1px solid #ddd; background: #004085; color: white;">Tối</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <th scope="row" style="padding: 0.75rem; border: 1px solid #ddd; background: #f9f9f9;">Thứ Hai</th>
            <td style="padding: 0.75rem; border: 1px solid #ddd;">Học trên trường</td>
            <td style="padding: 0.75rem; border: 1px solid #ddd;">Tự học HTML5</td>
            <td style="padding: 0.75rem; border: 1px solid #ddd;">Dạy kèm tin học</td>
          </tr>
          <tr>
            <th scope="row" style="padding: 0.75rem; border: 1px solid #ddd; background: #f9f9f9;">Thứ Ba</th>
            <td style="padding: 0.75rem; border: 1px solid #ddd;">Nghỉ</td>
            <td style="padding: 0.75rem; border: 1px solid #ddd;">Học trên trường</td>
            <td style="padding: 0.75rem; border: 1px solid #ddd;">Làm bài tập nhóm</td>
          </tr>
          <tr>
            <th scope="row" style="padding: 0.75rem; border: 1px solid #ddd; background: #f9f9f9;">Thứ Tư</th>
            <td style="padding: 0.75rem; border: 1px solid #ddd;">Học trên trường</td>
            <td style="padding: 0.75rem; border: 1px solid #ddd;">Họp nhóm</td>
            <td style="padding: 0.75rem; border: 1px solid #ddd;">Coding</td>
          </tr>
        </tbody>
      </table>
    </div>
  </section>
</main>

<?php require __DIR__ . '/../../inc/footer.php'; ?>
<script type="module" src="js/canhan.js"></script>
<script type="module" src="../../js/main.js"></script>
</body>
</html>

