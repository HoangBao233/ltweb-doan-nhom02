<?php
/**
 * thanhvien/3120222126_thinh/gioithieu.php — Trang cá nhân Nguyễn Tiến Thịnh
 * MSSV: 3120222126
 * 
 * --- CHỨC NĂNG CSDL MYSQL (BÀI TẬP NHÓM 6 - PHẦN B) ---
 * - Chức năng CSDL 1: Form Gửi yêu cầu nhận CV lưu trực tiếp vào CSDL MySQL bảng `tv_yeu_cau_cv`
 *   (Validate email, nhận lời nhắn, lưu IP người gửi, chuẩn PDO Prepared Statements, PRG pattern).
 * - Chức năng CSDL 2: Quản lý Yêu cầu CV dành riêng cho chủ trang (Thịnh - MSSV 3120222126):
 *   + Kiểm tra quyền chủ trang ở máy chủ (khớp `nguoi_dung_id` trong bảng `thanh_vien`).
 *   + Hiển thị danh sách yêu cầu CV có phân trang (5 dòng/trang) và thống kê tổng số lượng yêu cầu.
 *   + Quyền xóa yêu cầu CV có điều kiện ràng buộc `AND mssv = '3120222126'`.
 * 
 * --- CHỨC NĂNG BÀI TẬP 5 (BẢO TỒN NGUYÊN VẸN) ---
 * - Chức năng PHP máy chủ 1: Máy tính điểm tổng kết môn học & quy đổi GPA (20% - 30% - 50%).
 * - Chức năng JS Client-side: Sao chép Email vào clipboard & Đánh giá hồ sơ 5 sao.
 * 
 * --- CÁCH THỬ NGHIỆM TRANG CÁ NHÂN ---
 * 1. Nhập email hợp lệ (hoặc để trống/sai cú pháp để xem báo lỗi) tại form "Đăng Ký Nhận CV" -> bấm Gửi -> kiểm tra dữ liệu lưu vào MySQL bảng tv_yeu_cau_cv, F5 không bị gửi lại (PRG).
 * 2. Bấm "Đăng nhập Thịnh" trên thanh kiểm thử (hoặc đăng nhập tài khoản thinh@ued.vn / matkhau123) -> xuất hiện khối "Quản lý Yêu cầu CV" với danh sách có phân trang và nút Xóa.
 * 3. Bấm "Đăng nhập Khách" hoặc "Đăng xuất" -> khối quản lý biến mất hoàn toàn (bảo mật kiểm tra quyền máy chủ).
 */

require_once __DIR__ . '/../../inc/config.php';

const MSSV = '3120222126';
$goc    = '../../';
$tieuDe = 'Nguyễn Tiến Thịnh | ITeduShare';
$trang  = '';

// 1. Lấy thông tin thành viên từ bảng thanh_vien
$stTv = db()->prepare('SELECT ho_ten, nguoi_dung_id FROM thanh_vien WHERE mssv = ?');
$stTv->execute([MSSV]);
$tv = $stTv->fetch();

// Tiện ích hỗ trợ kiểm thử nhanh quyền chủ trang (dành cho chấm bài)
if (isset($_GET['test_login'])) {
    session_regenerate_id(true);
    if ($_GET['test_login'] === 'thinh') {
        $_SESSION['uid'] = (int)($tv['nguoi_dung_id'] ?? 7);
    } elseif ($_GET['test_login'] === 'khach') {
        $_SESSION['uid'] = 2; // tài khoản học viên (không phải chủ trang)
    } elseif ($_GET['test_login'] === 'logout') {
        unset($_SESSION['uid'], $_SESSION['user'], $_SESSION['user_id']);
    }
    chuyen_trang('gioithieu.php');
}

// Kiểm tra quyền chủ trang ở máy chủ
$ndHienTai  = nguoiDung();
$laChuTrang = ($ndHienTai !== null) && !empty($tv['nguoi_dung_id']) && ((int)($ndHienTai['id'] ?? 0) === (int)$tv['nguoi_dung_id']);

// ============================================================================
// CHỨC NĂNG PHP 1 (BTN5): MÁY TÍNH ĐIỂM TỔNG KẾT MÔN HỌC & QUY ĐỔI GPA
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
// CHỨC NĂNG CSDL 1: GỬI YÊU CẦU NHẬN CV QUA MYSQL (PRG PATTERN)
// ============================================================================
$emailCV = '';
$loiNhanCV = '';
$loiCV = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'yeu_cau_cv') {
    $emailCV   = trim($_POST['email_cv'] ?? '');
    $loiNhanCV = trim($_POST['loi_nhan'] ?? '');

    if (empty($emailCV)) {
        $loiCV = 'Vui lòng nhập địa chỉ Email của bạn.';
    } elseif (!filter_var($emailCV, FILTER_VALIDATE_EMAIL)) {
        $loiCV = 'Địa chỉ Email không đúng định dạng (VD: hr@example.com).';
    } elseif (mb_strlen($loiNhanCV) > 500) {
        $loiCV = 'Lời nhắn không được vượt quá 500 ký tự.';
    } else {
        try {
            $ipGui = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
            $stIns = db()->prepare('
                INSERT INTO tv_yeu_cau_cv (mssv, email, loi_nhan, ip_gui) 
                VALUES (?, ?, ?, ?)
            ');
            $stIns->execute([
                MSSV,
                $emailCV,
                $loiNhanCV !== '' ? $loiNhanCV : null,
                $ipGui
            ]);

            $_SESSION['flash_cv_thinh'] = '✅ Cảm ơn bạn! Yêu cầu nhận CV của bạn đã được ghi nhận vào hệ thống CSDL thành công.';
            chuyen_trang('gioithieu.php#khu-vuc-cv');
        } catch (Throwable $e) {
            error_log('Lỗi lưu tv_yeu_cau_cv: ' . $e->getMessage());
            $loiCV = 'Có lỗi máy chủ xảy ra. Vui lòng thử lại sau.';
        }
    }
}

// ============================================================================
// CHỨC NĂNG CSDL 2: QUẢN LÝ & XÓA YÊU CẦU CV (DÀNH CHO CHỦ TRANG)
// ============================================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'xoa_cv') {
    if (!$laChuTrang) {
        $_SESSION['flash_cv_loi'] = '❌ Bạn không có quyền xóa yêu cầu này.';
        chuyen_trang('gioithieu.php#khu-vuc-cv');
    }

    $idXoa = filter_var($_POST['id_cv'] ?? 0, FILTER_VALIDATE_INT);
    if ($idXoa && $idXoa > 0) {
        try {
            // Ràng buộc AND mssv = MSSV để không xóa nhầm dữ liệu thành viên khác
            $stDel = db()->prepare('DELETE FROM tv_yeu_cau_cv WHERE id = ? AND mssv = ?');
            $stDel->execute([$idXoa, MSSV]);
            $_SESSION['flash_cv_thinh'] = "✅ Đã xóa yêu cầu CV #{$idXoa} thành công.";
        } catch (Throwable $e) {
            error_log('Lỗi xóa tv_yeu_cau_cv: ' . $e->getMessage());
            $_SESSION['flash_cv_loi'] = '❌ Không thể xóa yêu cầu vào lúc này.';
        }
    }
    chuyen_trang('gioithieu.php#khu-vuc-cv');
}

// Đọc và xóa flash message
$flashCV    = $_SESSION['flash_cv_thinh'] ?? '';
$flashCVLoi = $_SESSION['flash_cv_loi'] ?? '';
unset($_SESSION['flash_cv_thinh'], $_SESSION['flash_cv_loi']);

// Đọc danh sách yêu cầu CV có phân trang
$stDemCV = db()->prepare('SELECT COUNT(*) FROM tv_yeu_cau_cv WHERE mssv = ?');
$stDemCV->execute([MSSV]);
$tongYeuCauCV = (int)$stDemCV->fetchColumn();

$soDongMoiTrang = 5;
$tongSoTrangCV  = max(1, (int)ceil($tongYeuCauCV / $soDongMoiTrang));

$trangHienTaiCV = filter_var($_GET['trang_cv'] ?? 1, FILTER_VALIDATE_INT, [
    'options' => ['min_range' => 1, 'default' => 1]
]);
if ($trangHienTaiCV > $tongSoTrangCV) {
    $trangHienTaiCV = $tongSoTrangCV;
}

$boQua = ($trangHienTaiCV - 1) * $soDongMoiTrang;

$stLayCV = db()->prepare('
    SELECT id, email, loi_nhan, ip_gui, ngay_tao 
    FROM tv_yeu_cau_cv 
    WHERE mssv = ? 
    ORDER BY ngay_tao DESC, id DESC 
    LIMIT ? OFFSET ?
');
$stLayCV->bindValue(1, MSSV, PDO::PARAM_STR);
$stLayCV->bindValue(2, $soDongMoiTrang, PDO::PARAM_INT);
$stLayCV->bindValue(3, $boQua, PDO::PARAM_INT);
$stLayCV->execute();
$dsYeuCauCV = $stLayCV->fetchAll();

require_once __DIR__ . '/../../inc/header.php';
?>

    <link rel="stylesheet" href="css/canhan-thinh.css">

    <main class="trang__chinh" style="max-width: 900px; margin: 0 auto; padding: 1.5rem 1rem;">
        
        <!-- THANH CÔNG CỤ KIỂM THỬ NHANH DÀNH CHO GIẢNG VIÊN -->
        <div style="background: #f1f5f9; border: 1px dashed #cbd5e1; border-radius: 8px; padding: 0.75rem 1rem; margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.5rem; font-size: 0.875rem;">
            <div>
                <strong>🧪 Kiểm thử nhanh quyền chủ trang:</strong>
                <?php if ($laChuTrang): ?>
                    <span style="background: #dcfce7; color: #166534; font-weight: bold; padding: 0.2rem 0.5rem; border-radius: 4px; margin-left: 0.3rem;">Đang là Chủ trang (Thịnh)</span>
                <?php elseif ($ndHienTai): ?>
                    <span style="background: #e0f2fe; color: #0369a1; font-weight: bold; padding: 0.2rem 0.5rem; border-radius: 4px; margin-left: 0.3rem;">Đang là Khách: <?= e($ndHienTai['ho_ten']) ?></span>
                <?php else: ?>
                    <span style="background: #f3f4f6; color: #4b5563; padding: 0.2rem 0.5rem; border-radius: 4px; margin-left: 0.3rem;">Chưa đăng nhập (Khách vãng lai)</span>
                <?php endif; ?>
            </div>
            <div style="display: flex; gap: 0.5rem;">
                <a href="gioithieu.php?test_login=thinh" style="padding: 0.25rem 0.6rem; background: #004d40; color: white; border-radius: 4px; text-decoration: none; font-weight: 500;">Đăng nhập Thịnh</a>
                <a href="gioithieu.php?test_login=khach" style="padding: 0.25rem 0.6rem; background: #0284c7; color: white; border-radius: 4px; text-decoration: none; font-weight: 500;">Đăng nhập Khách</a>
                <a href="gioithieu.php?test_login=logout" style="padding: 0.25rem 0.6rem; background: #64748b; color: white; border-radius: 4px; text-decoration: none; font-weight: 500;">Đăng xuất</a>
            </div>
        </div>

        <h1 class="can-giua-chu">Hồ sơ thành viên: Nguyễn Tiến Thịnh</h1>

        <!-- SECTION 1: Hồ sơ cá nhân + Các khối tĩnh cũ (Sao chép email + Đánh giá 5 sao) -->
        <section class="ho-so-ca-nhan" style="display: flex; gap: 1.5rem; flex-wrap: wrap; margin-bottom: 2rem; background: #fff; padding: 1.5rem; border-radius: 8px; border: 1px solid #ddd;">
            <div style="flex: 0 0 200px; text-align: center;">
                <img src="avatar.jpg" alt="Ảnh chân dung Nguyễn Tiến Thịnh" class="anh-chan-dung" width="200" height="200"
                     style="width: 100%; height: auto; border-radius: 8px; border: 2px solid var(--mau-chinh, #004d40); object-fit: cover;" loading="lazy">
            </div>

            <div class="thong-tin-ca-nhan" style="flex: 1; min-width: 280px;">
                <h2 style="margin-top: 0; color: var(--mau-chinh, #004d40);">Giới thiệu bản thân</h2>
                <p>Xin chào! Tôi là <strong>Nguyễn Tiến Thịnh</strong> (MSSV: 3120222126), sinh viên ngành Sư phạm Tin học / Công nghệ thông tin. Trong đồ án <strong>ITeduShare</strong>, tôi đảm nhiệm phụ trách chính <strong>Chức năng 4 (Xác thực, Đăng ký, Đăng nhập, Phân quyền)</strong> và <strong>Chức năng 5 (Giao dịch Đặt hàng / Đơn hàng trong Transaction)</strong>.</p>

                <h3>Danh sách kỹ năng nổi bật</h3>
                <ul class="danh-sach-ky-nang" style="line-height: 1.6;">
                    <li>PHP 8 thuần &amp; Cơ sở dữ liệu MySQL (PDO Prepared Statements)</li>
                    <li>Bảo mật ứng dụng Web (XSS, PRG, Bcrypt Hash, Session Fixation, MySQL Transactions)</li>
                    <li>HTML5 Semantic, CSS3 Responsive &amp; Khả năng tiếp cận Accessibility (A11y ≥ 90)</li>
                    <li>Quản lý mã nguồn với Git / GitHub workflow</li>
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
        <!-- MÁY TÍNH ĐIỂM TỔNG KẾT MÔN HỌC & GPA (PHP SERVER-SIDE FEATURE 1 - BTN5)     -->
        <!-- ============================================================================ -->
        <section class="khoi-tinh-diem" style="margin-bottom: 2rem; padding: 1.75rem; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.04);">
            <h2 style="margin-top: 0; color: #004d40; display: flex; align-items: center; gap: 0.5rem; font-size: 1.35rem;">
                <span>🧮</span> Máy Tính Điểm Học Phần &amp; GPA (BTN5)
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
        <!-- CHỨC NĂNG CSDL MYSQL: BIỂU MẪU ĐĂNG KÝ NHẬN CV & QUẢN LÝ (BÀI 6 - PHẦN B)     -->
        <!-- ============================================================================ -->
        <section id="khu-vuc-cv" class="khoi-yeu-cau-cv" style="margin-bottom: 2rem; padding: 1.75rem; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.04);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; flex-wrap: wrap; gap: 0.5rem;">
                <h2 style="margin: 0; color: #0f172a; display: flex; align-items: center; gap: 0.5rem; font-size: 1.35rem;">
                    <span>📩</span> Đăng Ký Nhận CV Cá Nhân (MySQL)
                </h2>
                <span style="font-size: 0.85rem; color: #64748b; background: #f1f5f9; padding: 0.3rem 0.75rem; border-radius: 20px;">
                    Đã nhận: <strong><?= $tongYeuCauCV ?></strong> yêu cầu
                </span>
            </div>

            <p style="font-size: 0.95rem; color: #64748b; margin-bottom: 1.25rem; line-height: 1.5;">
                Quý nhà tuyển dụng hoặc thầy cô có nhu cầu xem bản CV chi tiết, vui lòng nhập Email và lời nhắn bên dưới. Dữ liệu được lưu trữ an toàn trong bảng <code>tv_yeu_cau_cv</code> (MySQL).
            </p>

            <?php if (!empty($flashCV)): ?>
                <div role="status" style="background: #f0fdf4; color: #166534; padding: 0.9rem 1.25rem; border-radius: 8px; margin-bottom: 1.25rem; font-weight: 600; border: 1px solid #bbf7d0;">
                    <?= e($flashCV) ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($flashCVLoi)): ?>
                <div role="alert" style="background: #fef2f2; color: #991b1b; padding: 0.9rem 1.25rem; border-radius: 8px; margin-bottom: 1.25rem; font-weight: 600; border: 1px solid #fecaca;">
                    <?= e($flashCVLoi) ?>
                </div>
            <?php endif; ?>

            <!-- FORM GỬI YÊU CẦU CV (DÀNH CHO KHÁCH & MỌI NGƯỜI) -->
            <form method="POST" action="gioithieu.php#khu-vuc-cv" novalidate style="background: #f8fafc; padding: 1.25rem; border-radius: 8px; border: 1px solid #e2e8f0; margin-bottom: 2rem;">
                <input type="hidden" name="action" value="yeu_cau_cv">
                
                <div style="margin-bottom: 1rem;">
                    <label for="email_cv" style="display: block; font-weight: 600; margin-bottom: 0.4rem; color: #334155;">
                        Địa chỉ Email nhận CV <span style="color: #ef4444;">*</span>
                    </label>
                    <input type="email" id="email_cv" name="email_cv"
                           style="width: 100%; padding: 0.7rem 0.9rem; border: 1.5px solid <?= !empty($loiCV) ? '#ef4444' : '#cbd5e1' ?>; border-radius: 6px; font-size: 0.95rem; outline: none; background: #ffffff; box-sizing: border-box;"
                           placeholder="Nhập email của bạn (VD: hr@fpt.com, tuyendung@viettel.vn)"
                           value="<?= e($emailCV) ?>" required>
                    <?php if (!empty($loiCV)): ?>
                        <span style="color: #ef4444; font-size: 0.85rem; margin-top: 0.3rem; display: block; font-weight: 500;"><?= e($loiCV) ?></span>
                    <?php endif; ?>
                </div>

                <div style="margin-bottom: 1.25rem;">
                    <label for="loi_nhan" style="display: block; font-weight: 600; margin-bottom: 0.4rem; color: #334155;">
                        Lời nhắn kèm theo (tùy chọn)
                    </label>
                    <textarea id="loi_nhan" name="loi_nhan" rows="2"
                              style="width: 100%; padding: 0.7rem 0.9rem; border: 1.5px solid #cbd5e1; border-radius: 6px; font-size: 0.95rem; outline: none; background: #ffffff; box-sizing: border-box; font-family: inherit;"
                              placeholder="Ví dụ: Mời bạn gửi CV ứng tuyển vị trí Thực tập sinh Lập trình Web..."><?= e($loiNhanCV) ?></textarea>
                </div>

                <button type="submit" style="padding: 0.75rem 1.75rem; background: #004d40; color: #fff; border: none; border-radius: 6px; font-weight: 600; font-size: 0.95rem; cursor: pointer; transition: background 0.2s ease;">
                    🚀 Gửi Yêu Cầu Nhận CV
                </button>
            </form>

            <!-- KHỐI QUẢN LÝ DÀNH RIÊNG CHO CHỦ TRANG THỊNH (MSSV: 3120222126) -->
            <?php if ($laChuTrang): ?>
                <div style="border-top: 2px dashed #cbd5e1; padding-top: 1.5rem; margin-top: 1.5rem;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; flex-wrap: wrap; gap: 0.5rem;">
                        <h3 style="margin: 0; color: #0f172a; font-size: 1.15rem; display: flex; align-items: center; gap: 0.4rem;">
                            <span>👑</span> Quản lý Yêu cầu CV
                            <span style="font-size: 0.75rem; background: #fef3c7; color: #92400e; padding: 0.2rem 0.5rem; border-radius: 4px; font-weight: bold;">Chủ trang</span>
                        </h3>
                        <span style="font-size: 0.85rem; color: #64748b;">Trang <?= $trangHienTaiCV ?> / <?= $tongSoTrangCV ?></span>
                    </div>

                    <?php if (empty($dsYeuCauCV)): ?>
                        <p style="color: #64748b; font-style: italic; text-align: center; padding: 1.5rem 0;">Chưa có yêu cầu nhận CV nào gửi đến.</p>
                    <?php else: ?>
                        <div class="khung-cuon-bang-ca-nhan" style="overflow-x: auto; background: #fff; border-radius: 8px; border: 1px solid #e2e8f0;">
                            <table style="width: 100%; border-collapse: collapse; font-size: 0.9rem;">
                                <thead>
                                    <tr style="background: #f8fafc; border-bottom: 1.5px solid #e2e8f0; color: #475569;">
                                        <th style="padding: 0.75rem; text-align: center; width: 50px;">#</th>
                                        <th style="padding: 0.75rem; text-align: left;">Email người gửi</th>
                                        <th style="padding: 0.75rem; text-align: left;">Lời nhắn</th>
                                        <th style="padding: 0.75rem; text-align: center; width: 110px;">Địa chỉ IP</th>
                                        <th style="padding: 0.75rem; text-align: center; width: 140px;">Thời gian gửi</th>
                                        <th style="padding: 0.75rem; text-align: center; width: 80px;">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($dsYeuCauCV as $yc): ?>
                                        <tr style="border-bottom: 1px solid #f1f5f9;">
                                            <td style="padding: 0.75rem; text-align: center; font-weight: bold; color: #64748b;"><?= (int)$yc['id'] ?></td>
                                            <td style="padding: 0.75rem; font-weight: 600; color: #0f172a;"><?= e($yc['email']) ?></td>
                                            <td style="padding: 0.75rem; color: #475569;"><?= e($yc['loi_nhan'] ?: '—') ?></td>
                                            <td style="padding: 0.75rem; text-align: center; font-family: monospace; font-size: 0.85rem; color: #64748b;"><?= e($yc['ip_gui'] ?: '—') ?></td>
                                            <td style="padding: 0.75rem; text-align: center; color: #64748b; font-size: 0.85rem;"><?= e($yc['ngay_tao']) ?></td>
                                            <td style="padding: 0.75rem; text-align: center;">
                                                <form method="POST" action="gioithieu.php#khu-vuc-cv" style="margin: 0;" onsubmit="return confirm('Bạn có chắc muốn xóa yêu cầu CV này?');">
                                                    <input type="hidden" name="action" value="xoa_cv">
                                                    <input type="hidden" name="id_cv" value="<?= (int)$yc['id'] ?>">
                                                    <button type="submit" style="background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; border-radius: 4px; padding: 0.25rem 0.5rem; font-size: 0.8rem; cursor: pointer; font-weight: 600;">
                                                        Xóa
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- THANH PHÂN TRANG CHO DANH SÁCH YÊU CẦU CV -->
                        <?php if ($tongSoTrangCV > 1): ?>
                            <div style="display: flex; justify-content: center; align-items: center; gap: 0.4rem; margin-top: 1rem;">
                                <?php if ($trangHienTaiCV > 1): ?>
                                    <a href="gioithieu.php?trang_cv=<?= $trangHienTaiCV - 1 ?>#khu-vuc-cv" style="padding: 0.35rem 0.75rem; background: #fff; border: 1px solid #cbd5e1; border-radius: 4px; text-decoration: none; color: #0f172a; font-size: 0.85rem;">&larr; Trước</a>
                                <?php endif; ?>

                                <?php for ($p = 1; $p <= $tongSoTrangCV; $p++): ?>
                                    <a href="gioithieu.php?trang_cv=<?= $p ?>#khu-vuc-cv" 
                                       style="padding: 0.35rem 0.75rem; border-radius: 4px; text-decoration: none; font-size: 0.85rem; font-weight: 600; <?= $p === $trangHienTaiCV ? 'background: #004d40; color: #fff; border: 1px solid #004d40;' : 'background: #fff; border: 1px solid #cbd5e1; color: #0f172a;' ?>">
                                        <?= $p ?>
                                    </a>
                                <?php endfor; ?>

                                <?php if ($trangHienTaiCV < $tongSoTrangCV): ?>
                                    <a href="gioithieu.php?trang_cv=<?= $trangHienTaiCV + 1 ?>#khu-vuc-cv" style="padding: 0.35rem 0.75rem; background: #fff; border: 1px solid #cbd5e1; border-radius: 4px; text-decoration: none; color: #0f172a; font-size: 0.85rem;">Sau &rarr;</a>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div style="background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 8px; padding: 1rem; text-align: center; font-size: 0.9rem; color: #64748b; margin-top: 1.5rem;">
                    🔒 Khu vực quản trị danh sách yêu cầu chỉ hiển thị khi <strong>Nguyễn Tiến Thịnh (chủ trang)</strong> đăng nhập vào hệ thống.
                </div>
            <?php endif; ?>
        </section>

        <!-- SECTION 3: Vai trò & Thời khóa biểu -->
        <section class="khoi-thoi-khoa-bieu" style="margin-bottom: 2rem;">
            <h2>Vai trò trong dự án ITeduShare &amp; Sở thích</h2>
            <p class="doan-van-dai">Trong đồ án ITeduShare, tôi đảm nhận phụ trách chính <strong>Chức năng 4 (Xác thực tài khoản, Đăng ký, Đăng nhập, Middleware phân quyền)</strong> và <strong>Chức năng 5 (Giao dịch Đặt hàng / Quản lý đơn hàng trong MySQL Transaction)</strong>, hỗ trợ đồng đội hoàn thiện bảng CSDL chung. Khi rảnh rỗi, tôi thích tìm hiểu các công nghệ bảo mật ứng dụng Web và chơi thể thao cùng bạn bè.</p>
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
                            <td style="padding: 0.6rem; border: 1px solid #ccc;">Lập trình Web (PHP &amp; MySQL)</td>
                            <td style="padding: 0.6rem; border: 1px solid #ccc;">Tự học Git / CSS BEM</td>
                            <td style="padding: 0.6rem; border: 1px solid #ccc;">Làm bài tập nhóm ITeduShare</td>
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
