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
 * --- GIA CỐ BẢO MẬT (BÀI TẬP NHÓM 7 - CHƯƠNG 7) ---
 * - Gắn mã CSRF (csrfField) vào toàn bộ biểu mẫu POST (chống CSRF theo Biện pháp 1).
 * - Loại bỏ toàn bộ CSS/JS nội tuyến (chuyển sang canhan-thinh.css và canhan.js) phục vụ CSP (Biện pháp 3).
 * - Chuyển onsubmit nội tuyến sang thuộc tính data-confirm được xử lý an toàn trong tệp script ngoài.
 */

require_once __DIR__ . '/../../inc/config.php';
require_once __DIR__ . '/../../inc/csrf.php';

const MSSV = '3120222126';
$goc    = '../../';
$tieuDe = 'Hồ sơ cá nhân: Nguyễn Tiến Thịnh';
$trang  = '';

// 1. Lấy thông tin thành viên từ bảng thanh_vien
$stTv = db()->prepare('SELECT ho_ten, nguoi_dung_id FROM thanh_vien WHERE mssv = ?');
$stTv->execute([MSSV]);
$tv = $stTv->fetch();

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

            $_SESSION['flash_cv_thinh'] = 'Cảm ơn bạn! Yêu cầu nhận CV của bạn đã được ghi nhận vào hệ thống CSDL thành công.';
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
        $_SESSION['flash_cv_loi'] = 'Bạn không có quyền xóa yêu cầu này.';
        chuyen_trang('gioithieu.php#khu-vuc-cv');
    }

    $idXoa = filter_var($_POST['id_cv'] ?? 0, FILTER_VALIDATE_INT);
    if ($idXoa && $idXoa > 0) {
        try {
            // Ràng buộc AND mssv = MSSV để không xóa nhầm dữ liệu thành viên khác
            $stDel = db()->prepare('DELETE FROM tv_yeu_cau_cv WHERE id = ? AND mssv = ?');
            $stDel->execute([$idXoa, MSSV]);
            $_SESSION['flash_cv_thinh'] = "Đã xóa yêu cầu CV #{$idXoa} thành công.";
        } catch (Throwable $e) {
            error_log('Lỗi xóa tv_yeu_cau_cv: ' . $e->getMessage());
            $_SESSION['flash_cv_loi'] = 'Không thể xóa yêu cầu vào lúc này.';
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

    <main class="trang__chinh trang-ca-nhan-thinh">
        
        <!-- THANH ĐIỀU HƯỚNG LIÊN KẾT NHANH (BREADCRUMB) -->
        <nav class="thanh-dieu-huong-ca-nhan" aria-label="Menu cá nhân">
            <ul class="menu-ca-nhan">
                <li><a href="../../index.php">&larr; Về Trang chủ</a></li>
                <li><a href="../../ve-chung-toi.php">&larr; Danh sách thành viên nhóm</a></li>
            </ul>
        </nav>

        <!-- THANH TRẠNG THÁI QUYỀN CHỦ TRANG -->
        <div class="thanh-kiem-thu">
            <div class="thanh-kiem-thu__trang-thai">
                <strong>Trạng thái phân quyền:</strong>
                <?php if ($laChuTrang): ?>
                    <span class="nhan-chu-trang">Đang là Chủ trang (Thịnh)</span>
                <?php elseif ($ndHienTai): ?>
                    <span class="nhan-khach">Đang là Khách: <?= e($ndHienTai['ho_ten']) ?></span>
                <?php else: ?>
                    <span class="nhan-chua-dang-nhap">Chưa đăng nhập (Khách vãng lai)</span>
                <?php endif; ?>
            </div>
            <div class="thanh-kiem-thu__nut">
                <?php if ($ndHienTai): ?>
                    <a href="<?= $goc ?>dang-xuat.php" class="nut-kiem-thu nut-kiem-thu--dang-xuat">Đăng xuất</a>
                <?php else: ?>
                    <a href="<?= $goc ?>dang-nhap.php" class="nut-kiem-thu nut-kiem-thu--dang-nhap">Đăng nhập</a>
                <?php endif; ?>
            </div>
        </div>

        <h1 class="can-giua-chu">Hồ sơ thành viên: Nguyễn Tiến Thịnh</h1>

        <!-- SECTION 1: Hồ sơ cá nhân + Các khối tĩnh cũ (Sao chép email + Đánh giá 5 sao) -->
        <section class="ho-so-ca-nhan">
            <div class="ho-so-ca-nhan__avatar-wrap">
                <img src="avatar.jpg" alt="Ảnh chân dung Nguyễn Tiến Thịnh" class="anh-chan-dung" width="200" height="200" loading="lazy">
            </div>

            <div class="thong-tin-ca-nhan">
                <h2>Giới thiệu bản thân</h2>
                <p>Xin chào! Tôi là <strong>Nguyễn Tiến Thịnh</strong> (MSSV: 3120222126), sinh viên ngành Sư phạm Tin học / Công nghệ thông tin. Trong đồ án <strong>ITeduShare</strong>, tôi đảm nhiệm phụ trách chính <strong>Chức năng 4 (Xác thực, Đăng ký, Đăng nhập, Phân quyền)</strong> và <strong>Chức năng 5 (Giao dịch Đặt hàng / Đơn hàng trong Transaction)</strong>.</p>

                <h3>Danh sách kỹ năng nổi bật</h3>
                <ul class="danh-sach-ky-nang">
                    <li>PHP 8 thuần &amp; Cơ sở dữ liệu MySQL (PDO Prepared Statements)</li>
                    <li>Bảo mật ứng dụng Web (CSRF, XSS, PRG, Bcrypt Hash, Session Fixation, MySQL Transactions)</li>
                    <li>HTML5 Semantic, CSS3 Responsive &amp; Khả năng tiếp cận Accessibility (A11y ≥ 90)</li>
                    <li>Quản lý mã nguồn với Git / GitHub workflow</li>
                </ul>

                <!-- TÍNH NĂNG TĨNH CŨ 1: Nút sao chép email -->
                <div class="khoi-saochep-email">
                    <button type="button" id="btn-saochep-email" class="nut-thao-tac nut-thao-tac--xem"
                            aria-label="Sao chép địa chỉ Email của Thịnh">
                        Sao chép Email liên hệ
                    </button>
                </div>

                <!-- TÍNH NĂNG TĨNH CŨ 2: Đánh giá hồ sơ 5 sao (JS client-side) -->
                <div class="khoi-danh-gia-ca-nhan">
                    <h3>Đánh giá hồ sơ cá nhân</h3>
                    <div id="khung-danh-gia-sao" class="danh-sach-sao" role="radiogroup" aria-label="Đánh giá chất lượng hồ sơ">
                        <button type="button" class="nut-danh-gia-sao" data-val="1" aria-label="Đánh giá 1 sao" role="radio" aria-checked="false">★</button>
                        <button type="button" class="nut-danh-gia-sao" data-val="2" aria-label="Đánh giá 2 sao" role="radio" aria-checked="false">★</button>
                        <button type="button" class="nut-danh-gia-sao" data-val="3" aria-label="Đánh giá 3 sao" role="radio" aria-checked="false">★</button>
                        <button type="button" class="nut-danh-gia-sao" data-val="4" aria-label="Đánh giá 4 sao" role="radio" aria-checked="false">★</button>
                        <button type="button" class="nut-danh-gia-sao" data-val="5" aria-label="Đánh giá 5 sao" role="radio" aria-checked="false">★</button>
                    </div>
                    <p id="thong-bao-danh-gia" class="thong-bao-danh-gia" aria-live="polite">Bạn chưa đánh giá. Hãy chọn số sao để gửi đánh giá nhé!</p>
                </div>
            </div>
        </section>

        <!-- ============================================================================ -->
        <!-- MÁY TÍNH ĐIỂM TỔNG KẾT MÔN HỌC & GPA (PHP SERVER-SIDE FEATURE 1 - BTN5)     -->
        <!-- ============================================================================ -->
        <section class="khoi-tinh-diem">
            <h2>Máy Tính Điểm Học Phần &amp; GPA (BTN5)</h2>
            <p class="khoi-tinh-diem__mo-ta">Nhập điểm các cột đánh giá (từ 0.0 đến 10.0) để tính điểm tổng kết môn học theo trọng số: <strong>Chuyên cần (20%)</strong>, <strong>Giữa kỳ (30%)</strong>, <strong>Cuối kỳ (50%)</strong>.</p>

            <form method="POST" action="gioithieu.php" novalidate>
                <input type="hidden" name="action" value="tinh_diem">
                <?= csrfField() ?>
                
                <div class="luoi-nhap-diem">
                    <div class="o-nhap-diem">
                        <label for="a1">Điểm Chuyên cần (20%):</label>
                        <input type="number" step="0.1" min="0" max="10" id="a1" name="a1"
                               class="<?= isset($loiDiem['a1']) ? 'o-nhap-diem--loi' : '' ?>"
                               value="<?= e($diem['a1']) ?>" required placeholder="VD: 9.0">
                        <?php if (isset($loiDiem['a1'])): ?>
                            <span class="bao-loi-nhap-diem"><?= e($loiDiem['a1']) ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="o-nhap-diem">
                        <label for="a2">Điểm Giữa kỳ (30%):</label>
                        <input type="number" step="0.1" min="0" max="10" id="a2" name="a2"
                               class="<?= isset($loiDiem['a2']) ? 'o-nhap-diem--loi' : '' ?>"
                               value="<?= e($diem['a2']) ?>" required placeholder="VD: 8.5">
                        <?php if (isset($loiDiem['a2'])): ?>
                            <span class="bao-loi-nhap-diem"><?= e($loiDiem['a2']) ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="o-nhap-diem">
                        <label for="a3">Điểm Cuối kỳ (50%):</label>
                        <input type="number" step="0.1" min="0" max="10" id="a3" name="a3"
                               class="<?= isset($loiDiem['a3']) ? 'o-nhap-diem--loi' : '' ?>"
                               value="<?= e($diem['a3']) ?>" required placeholder="VD: 8.0">
                        <?php if (isset($loiDiem['a3'])): ?>
                            <span class="bao-loi-nhap-diem"><?= e($loiDiem['a3']) ?></span>
                        <?php endif; ?>
                    </div>
                </div>

                <button type="submit" class="nut-tinh-diem">
                    Tính Điểm Tổng Kết
                </button>
            </form>

            <?php if ($ketQuaDiem): ?>
                <div class="ket-qua-diem">
                    <h3>Kết quả tính điểm môn học:</h3>
                    <div class="luoi-ket-qua-diem">
                        <div class="the-ket-qua">
                            <span class="the-ket-qua__tieu-de">Điểm Hệ 10</span>
                            <span class="the-ket-qua__gia-tri"><?= number_format($ketQuaDiem['he10'], 2) ?></span>
                        </div>
                        <div class="the-ket-qua">
                            <span class="the-ket-qua__tieu-de">Điểm Chữ</span>
                            <span class="the-ket-qua__gia-tri"><?= e($ketQuaDiem['diemChu']) ?></span>
                        </div>
                        <div class="the-ket-qua">
                            <span class="the-ket-qua__tieu-de">Điểm Hệ 4</span>
                            <span class="the-ket-qua__gia-tri"><?= number_format($ketQuaDiem['he4'], 1) ?></span>
                        </div>
                        <div class="the-ket-qua">
                            <span class="the-ket-qua__tieu-de">Xếp Loại</span>
                            <span class="the-ket-qua__xep-loai the-ket-qua__xep-loai--<?= strtolower(str_replace('+', '-plus', $ketQuaDiem['diemChu'])) ?>"><?= e($ketQuaDiem['xepLoai']) ?></span>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </section>

        <!-- ============================================================================ -->
        <!-- CHỨC NĂNG CSDL MYSQL: BIỂU MẪU ĐĂNG KÝ NHẬN CV & QUẢN LÝ (BÀI 6 - PHẦN B)     -->
        <!-- ============================================================================ -->
        <section id="khu-vuc-cv" class="khoi-yeu-cau-cv">
            <div class="tieu-de-cv-box">
                <h2>Đăng Ký Nhận CV Cá Nhân (MySQL)</h2>
                <span class="thong-ke-cv-badge">
                    Đã nhận: <strong><?= $tongYeuCauCV ?></strong> yêu cầu
                </span>
            </div>

            <p class="khoi-yeu-cau-cv__mo-ta">
                Quý nhà tuyển dụng hoặc thầy cô có nhu cầu xem bản CV chi tiết, vui lòng nhập Email và lời nhắn bên dưới. Dữ liệu được lưu trữ an toàn trong bảng <code>tv_yeu_cau_cv</code> (MySQL).
            </p>

            <?php if (!empty($flashCV)): ?>
                <div role="status" class="thong-bao-cv--thanh-cong">
                    <?= e($flashCV) ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($flashCVLoi)): ?>
                <div role="alert" class="thong-bao-cv--loi">
                    <?= e($flashCVLoi) ?>
                </div>
            <?php endif; ?>

            <!-- FORM GỬI YÊU CẦU CV (DÀNH CHO KHÁCH & MỌI NGƯỜI) -->
            <form method="POST" action="gioithieu.php#khu-vuc-cv" novalidate class="form-gui-cv">
                <input type="hidden" name="action" value="yeu_cau_cv">
                <?= csrfField() ?>
                
                <div class="form-gui-cv__hang">
                    <label for="email_cv">
                        Địa chỉ Email nhận CV <span class="dau-sao-do">*</span>
                    </label>
                    <input type="email" id="email_cv" name="email_cv" maxlength="191"
                           class="<?= !empty($loiCV) ? 'o-nhap--loi' : '' ?>"
                           placeholder="Nhập email của bạn (VD: hr@fpt.com, tuyendung@viettel.vn)"
                           value="<?= e($emailCV) ?>" required>
                    <?php if (!empty($loiCV)): ?>
                        <span class="bao-loi-nhap-diem"><?= e($loiCV) ?></span>
                    <?php endif; ?>
                </div>

                <div class="form-gui-cv__hang">
                    <label for="loi_nhan">
                        Lời nhắn kèm theo (tùy chọn)
                    </label>
                    <textarea id="loi_nhan" name="loi_nhan" rows="2" maxlength="500"
                              placeholder="Ví dụ: Mời bạn gửi CV ứng tuyển vị trí Thực tập sinh Lập trình Web..."><?= e($loiNhanCV) ?></textarea>
                </div>

                <button type="submit" class="nut-gui-cv">
                    Gửi Yêu Cầu Nhận CV
                </button>
            </form>

            <!-- KHỐI QUẢN LÝ DÀNH RIÊNG CHO CHỦ TRANG THỊNH (MSSV: 3120222126) -->
            <?php if ($laChuTrang): ?>
                <div class="khu-vuc-quan-ly-cv">
                    <div class="tieu-de-quan-ly">
                        <h3>
                            Quản lý Yêu cầu CV
                            <span class="huy-hieu-chu-trang">Chủ trang</span>
                        </h3>
                        <span class="thong-ke-cv-badge">Trang <?= $trangHienTaiCV ?> / <?= $tongSoTrangCV ?></span>
                    </div>

                    <?php if (empty($dsYeuCauCV)): ?>
                        <p class="thong-bao-danh-gia">Chưa có yêu cầu nhận CV nào gửi đến.</p>
                    <?php else: ?>
                        <div class="khung-cuon-bang-ca-nhan">
                            <table class="bang-quan-ly-cv">
                                <caption class="can-giua-chu">Danh sách yêu cầu nhận CV gửi tới Nguyễn Tiến Thịnh</caption>
                                <thead>
                                    <tr>
                                        <th scope="col" class="can-giua">#</th>
                                        <th scope="col">Email người gửi</th>
                                        <th scope="col">Lời nhắn</th>
                                        <th scope="col" class="can-giua">Địa chỉ IP</th>
                                        <th scope="col" class="can-giua">Thời gian gửi</th>
                                        <th scope="col" class="can-giua">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($dsYeuCauCV as $yc): ?>
                                        <tr>
                                            <td class="can-giua"><?= (int)$yc['id'] ?></td>
                                            <td class="email-gui"><?= e($yc['email']) ?></td>
                                            <td><?= e($yc['loi_nhan'] ?: '—') ?></td>
                                            <td class="ip-gui"><?= e($yc['ip_gui'] ?: '—') ?></td>
                                            <td class="thoi-gian"><?= e($yc['ngay_tao']) ?></td>
                                            <td class="can-giua">
                                                <form method="POST" action="gioithieu.php#khu-vuc-cv" data-confirm="Bạn có chắc chắn muốn xóa yêu cầu CV này?">
                                                    <input type="hidden" name="action" value="xoa_cv">
                                                    <input type="hidden" name="id_cv" value="<?= (int)$yc['id'] ?>">
                                                    <?= csrfField() ?>
                                                    <button type="submit" class="nut-xoa-cv">
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
                            <nav class="phan-trang-cv" aria-label="Phân trang danh sách yêu cầu CV">
                                <?php if ($trangHienTaiCV > 1): ?>
                                    <a href="gioithieu.php?trang_cv=<?= $trangHienTaiCV - 1 ?>#khu-vuc-cv">&larr; Trước</a>
                                <?php endif; ?>

                                <?php for ($p = 1; $p <= $tongSoTrangCV; $p++): ?>
                                    <a href="gioithieu.php?trang_cv=<?= $p ?>#khu-vuc-cv" class="<?= $p === $trangHienTaiCV ? 'active' : '' ?>">
                                        <?= $p ?>
                                    </a>
                                <?php endfor; ?>

                                <?php if ($trangHienTaiCV < $tongSoTrangCV): ?>
                                    <a href="gioithieu.php?trang_cv=<?= $trangHienTaiCV + 1 ?>#khu-vuc-cv">Sau &rarr;</a>
                                <?php endif; ?>
                            </nav>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="hop-thong-bao-khoa">
                    Khu vực quản trị danh sách yêu cầu chỉ hiển thị khi <strong>Nguyễn Tiến Thịnh (chủ trang)</strong> đăng nhập vào hệ thống.
                </div>
            <?php endif; ?>
        </section>

        <!-- SECTION 3: Vai trò & Thời khóa biểu -->
        <section class="khoi-thoi-khoa-bieu">
            <h2>Vai trò trong dự án ITeduShare &amp; Sở thích</h2>
            <p class="doan-van-dai">Trong đồ án ITeduShare, tôi đảm nhận phụ trách chính <strong>Chức năng 4 (Xác thực tài khoản, Đăng ký, Đăng nhập, Middleware phân quyền)</strong> và <strong>Chức năng 5 (Giao dịch Đặt hàng / Quản lý đơn hàng trong MySQL Transaction)</strong>, hỗ trợ đồng đội hoàn thiện bảng CSDL chung. Khi rảnh rỗi, tôi thích tìm hiểu các công nghệ bảo mật ứng dụng Web và chơi thể thao cùng bạn bè.</p>
        </section>

        <section class="khoi-thoi-khoa-bieu">
            <h2>Thời khóa biểu cá nhân</h2>
            <div class="khung-cuon-bang-ca-nhan">
                <table class="bang-thoi-khoa-bieu">
                    <caption>Kế hoạch học tập và làm việc trong tuần</caption>
                    <thead>
                        <tr>
                            <th scope="col">Thứ</th>
                            <th scope="col">Buổi Sáng</th>
                            <th scope="col">Buổi Chiều</th>
                            <th scope="col">Buổi Tối</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th scope="row">Thứ Hai</th>
                            <td>Lập trình Web (PHP &amp; MySQL)</td>
                            <td>Tự học Git / CSS BEM</td>
                            <td>Làm bài tập nhóm ITeduShare</td>
                        </tr>
                        <tr>
                            <th scope="row">Thứ Tư</th>
                            <td>Cơ sở dữ liệu</td>
                            <td>Làm bài tập nhóm ITeduShare</td>
                            <td>Tự học nâng cao</td>
                        </tr>
                        <tr>
                            <th scope="row">Thứ Sáu</th>
                            <td>Mạng máy tính</td>
                            <td>Sinh hoạt CLB Tin học</td>
                            <td>Giải trí cá nhân</td>
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
