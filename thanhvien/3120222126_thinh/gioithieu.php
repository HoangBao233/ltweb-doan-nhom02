<?php
/**
 * thanhvien/3120222126_thinh/gioithieu.php — Trang cá nhân Nguyễn Tiến Thịnh
 * MSSV: 3120222126
 * Chức năng PHP máy chủ 1: Máy tính điểm tổng kết môn học & quy đổi GPA (20% - 30% - 50%, validate 0-10, xếp loại hệ 10/4/chữ).
 * Chức năng PHP máy chủ 2: Form Yêu cầu nhận CV qua Email (Validation email server-side, lưu storage/3120222126_yeucau_cv.jsonl, PRG pattern).
 * 
 * Cách thử:
 *   - Nhập điểm A1, A2, A3 (từ 0.0 đến 10.0) ở khối Máy tính điểm để xem kết quả quy đổi hệ 10, hệ 4, điểm chữ và xếp loại.
 *   - Nhập email ở khối Yêu cầu nhận CV để thử gửi yêu cầu; kiểm tra tệp storage/3120222126_yeucau_cv.jsonl.
 */

require_once __DIR__ . '/../../inc/config.php';

$goc    = '../../';
$tieuDe = 'Nguyễn Tiến Thịnh | ITeduShare';
$trang  = '';

// ============================================================================
// CHỨC NĂNG PHP 1: MÁY TÍNH ĐIỂM TỔNG KẾT MÔN HỌC & QUY ĐỔI GPA
// ============================================================================
$diem = [
    'a1' => '', // Chuyên cần (20%)
    'a2' => '', // Giữa kỳ (30%)
    'a3' => ''  // Cuối kỳ (50%)
];
$loiDiem = [];
$ketQuaDiem = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'tinh_diem') {
    $diem['a1'] = trim($_POST['a1'] ?? '');
    $diem['a2'] = trim($_POST['a2'] ?? '');
    $diem['a3'] = trim($_POST['a3'] ?? '');

    // Validate A1
    if ($diem['a1'] === '' || !is_numeric($diem['a1'])) {
        $loiDiem['a1'] = 'Vui lòng nhập điểm số hợp lệ.';
    } else {
        $val1 = (float)$diem['a1'];
        if ($val1 < 0.0 || $val1 > 10.0) {
            $loiDiem['a1'] = 'Điểm phải từ 0.0 đến 10.0.';
        }
    }

    // Validate A2
    if ($diem['a2'] === '' || !is_numeric($diem['a2'])) {
        $loiDiem['a2'] = 'Vui lòng nhập điểm số hợp lệ.';
    } else {
        $val2 = (float)$diem['a2'];
        if ($val2 < 0.0 || $val2 > 10.0) {
            $loiDiem['a2'] = 'Điểm phải từ 0.0 đến 10.0.';
        }
    }

    // Validate A3
    if ($diem['a3'] === '' || !is_numeric($diem['a3'])) {
        $loiDiem['a3'] = 'Vui lòng nhập điểm số hợp lệ.';
    } else {
        $val3 = (float)$diem['a3'];
        if ($val3 < 0.0 || $val3 > 10.0) {
            $loiDiem['a3'] = 'Điểm phải từ 0.0 đến 10.0.';
        }
    }

    if (empty($loiDiem)) {
        $a1 = (float)$diem['a1'];
        $a2 = (float)$diem['a2'];
        $a3 = (float)$diem['a3'];

        // Điểm tổng kết hệ 10
        $tkn10 = round(($a1 * 0.2) + ($a2 * 0.3) + ($a3 * 0.5), 2);

        // Quy đổi điểm chữ, hệ 4, xếp loại
        if ($tkn10 >= 8.5) {
            $diemChu = 'A';  $he4 = 4.0; $xepLoai = 'Xuất sắc'; $mauBadge = '#2e7d32';
        } elseif ($tkn10 >= 7.7) {
            $diemChu = 'B+'; $he4 = 3.5; $xepLoai = 'Giỏi';     $mauBadge = '#388e3c';
        } elseif ($tkn10 >= 7.0) {
            $diemChu = 'B';  $he4 = 3.0; $xepLoai = 'Khá';     $mauBadge = '#1976d2';
        } elseif ($tkn10 >= 6.2) {
            $diemChu = 'C+'; $he4 = 2.5; $xepLoai = 'Khá';     $mauBadge = '#0288d1';
        } elseif ($tkn10 >= 5.5) {
            $diemChu = 'C';  $he4 = 2.0; $xepLoai = 'Trung bình'; $mauBadge = '#f57c00';
        } elseif ($tkn10 >= 4.7) {
            $diemChu = 'D+'; $he4 = 1.5; $xepLoai = 'Trung bình yếu'; $mauBadge = '#e65100';
        } elseif ($tkn10 >= 4.0) {
            $diemChu = 'D';  $he4 = 1.0; $xepLoai = 'Yếu';     $mauBadge = '#d32f2f';
        } else {
            $diemChu = 'F';  $he4 = 0.0; $xepLoai = 'Kém';     $mauBadge = '#c62828';
        }

        $ketQuaDiem = [
            'he10'    => $tkn10,
            'diemChu' => $diemChu,
            'he4'     => $he4,
            'xepLoai' => $xepLoai,
            'mau'     => $mauBadge
        ];
    }
}

// ============================================================================
// CHỨC NĂNG PHP 2: YÊU CẦU NHẬN CV QUA EMAIL (PRG PATTERN)
// ============================================================================
$emailCV = '';
$loiCV = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'yeu_cau_cv') {
    $emailCV = trim($_POST['email_cv'] ?? '');

    if (empty($emailCV)) {
        $loiCV = 'Vui lòng nhập địa chỉ Email.';
    } elseif (!filter_var($emailCV, FILTER_VALIDATE_EMAIL)) {
        $loiCV = 'Địa chỉ Email không đúng định dạng (VD: ten@gmail.com).';
    } else {
        // Đảm bảo thư mục storage/ tồn tại
        $thuMucStorage = __DIR__ . '/../../storage/';
        if (!is_dir($thuMucStorage)) {
            mkdir($thuMucStorage, 0777, true);
        }

        $tepCV = $thuMucStorage . '3120222126_yeucau_cv.jsonl';
        $ghiChu = [
            'email'    => $emailCV,
            'ngay_tao' => date('Y-m-d H:i:s'),
            'ip'       => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1'
        ];

        file_put_contents($tepCV, json_encode($ghiChu, JSON_UNESCAPED_UNICODE) . PHP_EOL, FILE_APPEND | LOCK_EX);

        $_SESSION['flash_cv_thinh'] = '✅ Cảm ơn bạn! Yêu cầu nhận CV của bạn đã được ghi nhận thành công.';

        header('Location: gioithieu.php');
        exit;
    }
}

// Đọc và xóa flash message
$flashCV = $_SESSION['flash_cv_thinh'] ?? '';
unset($_SESSION['flash_cv_thinh']);

require_once __DIR__ . '/../../inc/header.php';
?>

    <link rel="stylesheet" href="css/canhan-thinh.css">

    <nav class="trang__dieu-huong" aria-label="Menu điều hướng trang cá nhân" style="background: var(--nen-phu, #f4f6f8); padding: 0.75rem 1rem;">
        <ul class="menu-chinh" style="display: flex; gap: 1rem; list-style: none; padding: 0; margin: 0; flex-wrap: wrap;">
            <li><a href="<?= $goc ?>index.php" class="menu-chinh__lien-ket">&laquo; Quay về Trang Chủ Nhóm</a></li>
            <li><a href="<?= $goc ?>ve-chung-toi.php" class="menu-chinh__lien-ket">&laquo; Quay về Trang Giới Thiệu Nhóm</a></li>
        </ul>
    </nav>

    <main class="trang__chinh" style="max-width: 900px; margin: 0 auto; padding: 1.5rem 1rem;">
        <h1 class="can-giua-chu">Hồ sơ thành viên: Nguyễn Tiến Thịnh</h1>

        <!-- SECTION 1: Hồ sơ cá nhân + Các khối tĩnh cũ (Sao chép email + Đánh giá 5 sao) -->
        <section class="ho-so-ca-nhan" style="display: flex; gap: 1.5rem; flex-wrap: wrap; margin-bottom: 2rem; background: #fff; padding: 1.5rem; border-radius: 8px; border: 1px solid #ddd;">
            <div style="flex: 0 0 200px; text-align: center;">
                <img src="avatar.jpg" alt="Ảnh chân dung Nguyễn Tiến Thịnh" class="anh-chan-dung" width="200" height="200"
                     style="width: 100%; height: auto; border-radius: 8px; border: 2px solid var(--mau-chinh, #004d40); object-fit: cover;" loading="lazy">
            </div>

            <div class="thong-tin-ca-nhan" style="flex: 1; min-width: 280px;">
                <h2 style="margin-top: 0; color: var(--mau-chinh, #004d40);">Giới thiệu bản thân</h2>
                <p>Xin chào! Tôi là <strong>Nguyễn Tiến Thịnh</strong> (MSSV: 3120222126), sinh viên ngành Công nghệ thông tin/Sư phạm Tin học. Tôi là thành viên chịu trách nhiệm xây dựng trang <strong>Kho học liệu</strong> cho dự án website ITeduShare.</p>

                <h3>Danh sách kỹ năng nổi bật</h3>
                <ul class="danh-sach-ky-nang" style="line-height: 1.6;">
                    <li>HTML5 &amp; CSS3 Responsive (BEM Architecture)</li>
                    <li>Lập trình JavaScript / PHP 8</li>
                    <li>Quản lý mã nguồn với Git / GitHub</li>
                    <li>Tối ưu Accessibility &amp; SEO</li>
                </ul>

                <!-- TÍNH NĂNG TĨNH CŨ 1: Nút sao chép email -->
                <div class="khoi-saochep-email" style="margin-top: 1rem; margin-bottom: 1rem;">
                    <button type="button" id="btn-saochep-email" class="nut-thao-tac nut-thao-tac--xem"
                            aria-label="Sao chép địa chỉ Email của Thịnh"
                            style="padding: 0.5rem 1rem; background: #004d40; color: white; border: none; border-radius: 4px; font-weight: bold; cursor: pointer;">
                        📋 Sao chép Email liên hệ
                    </button>
                </div>

                <!-- TÍNH NĂNG TĨNH CŨ 2: Đánh giá hồ sơ 5 sao (JS client-side) -->
                <div class="khoi-danh-gia-ca-nhan" style="background: #fafafa; padding: 1rem; border-radius: 6px; border: 1px dashed #ccc; margin-top: 1rem;">
                    <h3 style="margin-top: 0; font-size: 1.05rem;">Đánh giá hồ sơ cá nhân</h3>
                    <div id="khung-danh-gia-sao" class="danh-sach-sao" role="radiogroup" aria-label="Đánh giá chất lượng hồ sơ" style="display: flex; gap: 0.3rem; font-size: 1.5rem; color: #ffb300; margin-bottom: 0.5rem;">
                        <button type="button" class="nut-danh-gia-sao" data-val="1" aria-label="Đánh giá 1 sao" role="radio" aria-checked="false" style="background: none; border: none; cursor: pointer; font-size: 1.5rem; color: #ffb300;">★</button>
                        <button type="button" class="nut-danh-gia-sao" data-val="2" aria-label="Đánh giá 2 sao" role="radio" aria-checked="false" style="background: none; border: none; cursor: pointer; font-size: 1.5rem; color: #ffb300;">★</button>
                        <button type="button" class="nut-danh-gia-sao" data-val="3" aria-label="Đánh giá 3 sao" role="radio" aria-checked="false" style="background: none; border: none; cursor: pointer; font-size: 1.5rem; color: #ffb300;">★</button>
                        <button type="button" class="nut-danh-gia-sao" data-val="4" aria-label="Đánh giá 4 sao" role="radio" aria-checked="false" style="background: none; border: none; cursor: pointer; font-size: 1.5rem; color: #ffb300;">★</button>
                        <button type="button" class="nut-danh-gia-sao" data-val="5" aria-label="Đánh giá 5 sao" role="radio" aria-checked="false" style="background: none; border: none; cursor: pointer; font-size: 1.5rem; color: #ffb300;">★</button>
                    </div>
                    <p id="thong-bao-danh-gia" class="thong-bao-danh-gia" aria-live="polite" style="margin: 0; font-size: 0.9rem; color: #666;">Bạn chưa đánh giá. Hãy chọn số sao để gửi đánh giá nhé!</p>
                </div>
            </div>
        </section>

        <!-- ============================================================================ -->
        <!-- MÁY TÍNH ĐIỂM TỔNG KẾT MÔN HỌC & GPA (PHP SERVER-SIDE FEATURE 1)             -->
        <!-- ============================================================================ -->
        <section class="khoi-tinh-diem" style="margin-bottom: 2rem; padding: 1.75rem; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.04);">
            <h2 style="margin-top: 0; color: #004d40; display: flex; align-items: center; gap: 0.5rem; font-size: 1.35rem;">
                <span>🧮</span> Máy Tính Điểm Học Phần &amp; GPA
            </h2>
            <p style="font-size: 0.95rem; color: #64748b; margin-bottom: 1.25rem; line-height: 1.5;">Nhập điểm các cột đánh giá (từ 0.0 đến 10.0) để tính điểm tổng kết môn học theo trọng số: <strong>Chuyên cần (20%)</strong>, <strong>Giữa kỳ (30%)</strong>, <strong>Cuối kỳ (50%)</strong>.</p>

            <form method="POST" action="gioithieu.php" novalidate>
                <input type="hidden" name="action" value="tinh_diem">
                
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.25rem;">
                    <div style="background: #f8fafc; padding: 1rem; border-radius: 8px; border: 1px solid #f1f5f9;">
                        <label for="a1" style="font-weight: 600; display: block; margin-bottom: 0.4rem; color: #334155;">Điểm Chuyên cần (20%):</label>
                        <input type="number" step="0.1" min="0" max="10" id="a1" name="a1"
                               style="width: 100%; padding: 0.6rem 0.75rem; border: 1.5px solid <?= isset($loiDiem['a1']) ? '#ef4444' : '#cbd5e1' ?>; border-radius: 6px; font-size: 0.95rem; outline: none;"
                               value="<?= e($diem['a1']) ?>" required placeholder="VD: 9.0">
                        <?php if (isset($loiDiem['a1'])): ?>
                            <span style="color: #ef4444; font-size: 0.85rem; margin-top: 0.3rem; display: block; font-weight: 500;"><?= e($loiDiem['a1']) ?></span>
                        <?php endif; ?>
                    </div>

                    <div style="background: #f8fafc; padding: 1rem; border-radius: 8px; border: 1px solid #f1f5f9;">
                        <label for="a2" style="font-weight: 600; display: block; margin-bottom: 0.4rem; color: #334155;">Điểm Giữa kỳ (30%):</label>
                        <input type="number" step="0.1" min="0" max="10" id="a2" name="a2"
                               style="width: 100%; padding: 0.6rem 0.75rem; border: 1.5px solid <?= isset($loiDiem['a2']) ? '#ef4444' : '#cbd5e1' ?>; border-radius: 6px; font-size: 0.95rem; outline: none;"
                               value="<?= e($diem['a2']) ?>" required placeholder="VD: 8.5">
                        <?php if (isset($loiDiem['a2'])): ?>
                            <span style="color: #ef4444; font-size: 0.85rem; margin-top: 0.3rem; display: block; font-weight: 500;"><?= e($loiDiem['a2']) ?></span>
                        <?php endif; ?>
                    </div>

                    <div style="background: #f8fafc; padding: 1rem; border-radius: 8px; border: 1px solid #f1f5f9;">
                        <label for="a3" style="font-weight: 600; display: block; margin-bottom: 0.4rem; color: #334155;">Điểm Cuối kỳ (50%):</label>
                        <input type="number" step="0.1" min="0" max="10" id="a3" name="a3"
                               style="width: 100%; padding: 0.6rem 0.75rem; border: 1.5px solid <?= isset($loiDiem['a3']) ? '#ef4444' : '#cbd5e1' ?>; border-radius: 6px; font-size: 0.95rem; outline: none;"
                               value="<?= e($diem['a3']) ?>" required placeholder="VD: 8.0">
                        <?php if (isset($loiDiem['a3'])): ?>
                            <span style="color: #ef4444; font-size: 0.85rem; margin-top: 0.3rem; display: block; font-weight: 500;"><?= e($loiDiem['a3']) ?></span>
                        <?php endif; ?>
                    </div>
                </div>

                <button type="submit" style="margin-top: 1.25rem; padding: 0.7rem 1.75rem; background: #004d40; color: #fff; border: none; border-radius: 6px; font-weight: 600; font-size: 0.95rem; cursor: pointer; transition: background 0.2s ease;">
                    🧮 Tính Điểm Tổng Kết
                </button>
            </form>

            <?php if ($ketQuaDiem): ?>
                <div style="margin-top: 1.5rem; padding: 1.25rem; background: #f0fdf4; border: 1px solid #bbf7d0; border-left: 5px solid <?= e($ketQuaDiem['mau']) ?>; border-radius: 8px; box-shadow: 0 2px 6px rgba(0,0,0,0.02);">
                    <h3 style="margin-top: 0; color: #166534; font-size: 1.1rem; margin-bottom: 0.75rem;">🎯 Kết quả tính điểm môn học:</h3>
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 1rem;">
                        <div style="background: #ffffff; padding: 0.75rem 1rem; border-radius: 8px; border: 1px solid #e2e8f0; text-align: center;">
                            <span style="display: block; font-size: 0.85rem; color: #64748b; font-weight: 500;">Điểm Hệ 10</span>
                            <span style="font-size: 1.5rem; color: #004d40; font-weight: 700;"><?= number_format($ketQuaDiem['he10'], 2) ?></span>
                        </div>
                        <div style="background: #ffffff; padding: 0.75rem 1rem; border-radius: 8px; border: 1px solid #e2e8f0; text-align: center;">
                            <span style="display: block; font-size: 0.85rem; color: #64748b; font-weight: 500;">Điểm Chữ</span>
                            <span style="font-size: 1.5rem; color: #004d40; font-weight: 700;"><?= e($ketQuaDiem['diemChu']) ?></span>
                        </div>
                        <div style="background: #ffffff; padding: 0.75rem 1rem; border-radius: 8px; border: 1px solid #e2e8f0; text-align: center;">
                            <span style="display: block; font-size: 0.85rem; color: #64748b; font-weight: 500;">Điểm Hệ 4</span>
                            <span style="font-size: 1.5rem; color: #004d40; font-weight: 700;"><?= number_format($ketQuaDiem['he4'], 1) ?></span>
                        </div>
                        <div style="background: #ffffff; padding: 0.75rem 1rem; border-radius: 8px; border: 1px solid #e2e8f0; text-align: center; display: flex; flex-direction: column; justify-content: center; align-items: center;">
                            <span style="display: block; font-size: 0.85rem; color: #64748b; font-weight: 500; margin-bottom: 0.3rem;">Xếp Loại</span>
                            <span style="display: inline-block; padding: 0.3rem 0.8rem; background: <?= e($ketQuaDiem['mau']) ?>; color: white; border-radius: 20px; font-weight: 700; font-size: 0.85rem;"><?= e($ketQuaDiem['xepLoai']) ?></span>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </section>

        <!-- ============================================================================ -->
        <!-- YÊU CẦU NHẬN CV QUA EMAIL (PHP SERVER-SIDE FEATURE 2)                        -->
        <!-- ============================================================================ -->
        <section class="khoi-yeu-cau-cv" style="margin-bottom: 2rem; padding: 1.75rem; background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%); border: 1px solid #e2e8f0; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.04);">
            <h2 style="margin-top: 0; color: #1e293b; display: flex; align-items: center; gap: 0.5rem; font-size: 1.35rem;">
                <span>📩</span> Đăng Ký Nhận CV Cá Nhân
            </h2>
            <p style="font-size: 0.95rem; color: #64748b; margin-bottom: 1.25rem; line-height: 1.5;">Nếu bạn muốn nhận bản CV đầy đủ thông tin năng lực của Thịnh, vui lòng nhập Email bên dưới để gửi yêu cầu.</p>

            <?php if (!empty($flashCV)): ?>
                <div role="alert" style="background: #f0fdf4; color: #166534; padding: 0.9rem 1.25rem; border-radius: 8px; margin-bottom: 1.25rem; font-weight: 600; border: 1px solid #bbf7d0; display: flex; align-items: center; gap: 0.5rem;">
                    <span>✅</span> <?= e($flashCV) ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="gioithieu.php" novalidate style="display: flex; gap: 0.75rem; flex-wrap: wrap; align-items: flex-start;">
                <input type="hidden" name="action" value="yeu_cau_cv">
                
                <div style="flex: 1; min-width: 280px;">
                    <input type="email" id="email_cv" name="email_cv"
                           style="width: 100%; padding: 0.7rem 0.9rem; border: 1.5px solid <?= !empty($loiCV) ? '#ef4444' : '#cbd5e1' ?>; border-radius: 6px; font-size: 0.95rem; outline: none; background: #ffffff;"
                           placeholder="Nhập địa chỉ email của bạn (VD: email@gmail.com)"
                           value="<?= e($emailCV) ?>" required>
                    <?php if (!empty($loiCV)): ?>
                        <span style="color: #ef4444; font-size: 0.85rem; margin-top: 0.3rem; display: block; font-weight: 500;"><?= e($loiCV) ?></span>
                    <?php endif; ?>
                </div>

                <button type="submit" style="padding: 0.7rem 1.5rem; background: #0f172a; color: #fff; border: none; border-radius: 6px; font-weight: 600; font-size: 0.95rem; cursor: pointer; transition: background 0.2s ease;">
                    Gửi Yêu Cầu CV
                </button>
            </form>
        </section>

        <!-- SECTION 3: Vai trò & Thời khóa biểu -->
        <section class="khoi-thoi-khoa-bieu" style="margin-bottom: 2rem;">
            <h2>Vai trò trong dự án ITeduShare &amp; Sở thích</h2>
            <p class="doan-van-dai">Trong dự án ITeduShare, tôi đảm nhận nhiệm vụ thiết kế trang Kho học liệu, chuẩn hóa cấu trúc BEM, viết CSS responsive cho bảng giáo án và thẻ bài học. Khi rảnh rỗi, tôi thích tìm hiểu các công nghệ lập trình mới và chơi game giải trí cùng bạn bè.</p>
        </section>

        <section class="khoi-thoi-khoa-bieu">
            <h2>Thời khóa biểu cá nhân</h2>
            <div class="khung-cuon-bang-ca-nhan" style="overflow-x: auto;">
                <table class="bang-thoi-khoa-bieu" style="width: 100%; border-collapse: collapse; margin-top: 0.5rem;">
                    <caption style="caption-side: top; text-align: left; font-style: italic; margin-bottom: 0.5rem;">Kế hoạch học tập và làm việc trong tuần</caption>
                    <thead>
                        <tr style="background: var(--mau-chinh, #004d40); color: white;">
                            <th scope="col" style="padding: 0.6rem; border: 1px solid #ccc;">Thứ</th>
                            <th scope="col" style="padding: 0.6rem; border: 1px solid #ccc;">Buổi Sáng</th>
                            <th scope="col" style="padding: 0.6rem; border: 1px solid #ccc;">Buổi Chiều</th>
                            <th scope="col" style="padding: 0.6rem; border: 1px solid #ccc;">Buổi Tối</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th scope="row" style="padding: 0.6rem; border: 1px solid #ccc; background: #f9f9f9;">Thứ Hai</th>
                            <td style="padding: 0.6rem; border: 1px solid #ccc;">Lập trình Web</td>
                            <td style="padding: 0.6rem; border: 1px solid #ccc;">Tự học Git / CSS BEM</td>
                            <td style="padding: 0.6rem; border: 1px solid #ccc;">Làm bài tập nhóm</td>
                        </tr>
                        <tr>
                            <th scope="row" style="padding: 0.6rem; border: 1px solid #ccc; background: #f9f9f9;">Thứ Tư</th>
                            <td style="padding: 0.6rem; border: 1px solid #ccc;">Cơ sở dữ liệu</td>
                            <td style="padding: 0.6rem; border: 1px solid #ccc;">Làm bài tập nhóm ITeduShare</td>
                            <td style="padding: 0.6rem; border: 1px solid #ccc;">Tự học nâng cao</td>
                        </tr>
                        <tr>
                            <th scope="row" style="padding: 0.6rem; border: 1px solid #ccc; background: #f9f9f9;">Thứ Sáu</th>
                            <td style="padding: 0.6rem; border: 1px solid #ccc;">Mạng máy tính</td>
                            <td style="padding: 0.6rem; border: 1px solid #ccc;">Sinh hoạt CLB Tin học</td>
                            <td style="padding: 0.6rem; border: 1px solid #ccc;">Giải trí cá nhân</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>
    </main>

    <!-- Toast notification element cho JS client-side -->
    <div id="toast-thong-bao" class="toast-thong-bao" role="status" aria-live="polite" aria-hidden="true"></div>

    <!-- Script JS cá nhân cho nút sao chép email & đánh giá 5 sao client-side -->
    <script src="js/canhan.js" defer></script>
    <script type="module" src="<?= $goc ?>js/main.js"></script>

<?php require_once __DIR__ . '/../../inc/footer.php'; ?>
