<?php
// api/gio-hang.php — REST API quản lý giỏ hàng / danh sách lưu giáo án
// Phụ trách: Tấn & Châu (Chức năng 6 Bảng 1)
require_once __DIR__ . '/../inc/config.php';

// Đăng ký bộ xử lý ngoại lệ trả về JSON chuẩn HTTP 500 khi máy chủ gặp lỗi
set_exception_handler(function (Throwable $e) {
    error_log('Lỗi API gio-hang: ' . $e->getMessage());
    traJson(['loi' => 'Lỗi máy chủ'], 500);
});

// Chỉ cho phép phương thức GET và POST
if (!in_array($_SERVER['REQUEST_METHOD'], ['GET', 'POST'], true)) {
    header('Allow: GET, POST');
    traJson(['loi' => 'Phương thức không được hỗ trợ'], 405);
}

// Khởi tạo mảng giỏ hàng trong Session nếu chưa có
if (!isset($_SESSION['danh_sach_luu']) || !is_array($_SESSION['danh_sach_luu'])) {
    $_SESSION['danh_sach_luu'] = [];
}

// 1. Phương thức GET: Xem số món và nội dung giỏ hàng hiện tại
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    traJson([
        'so_mon'   => count($_SESSION['danh_sach_luu']),
        'gio_hang' => $_SESSION['danh_sach_luu'],
    ], 200);
}

// 2. Phương thức POST: Thêm giáo án vào giỏ hàng qua thân JSON
$raw = file_get_contents('php://input');
$data = json_decode($raw, true);

// Kiểm tra định dạng thân request có phải JSON hợp lệ không
if (!is_array($data)) {
    traJson(['loi' => 'Dữ liệu yêu cầu phải là JSON hợp lệ'], 400);
}

// Kiểm tra mã giáo án ID
if (!isset($data['id'])) {
    traJson(['loi' => 'Thiếu ID giáo án'], 404);
}

$id = (!is_array($data['id']) && !is_object($data['id']))
    ? filter_var($data['id'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]])
    : false;
if ($id === false) {
    traJson(['loi' => 'ID giáo án không hợp lệ'], 404);
}

// Kiểm tra giáo án có trong CSDL không
$st = db()->prepare('SELECT id, ten, ton_kho FROM giao_an WHERE id = ?');
$st->execute([$id]);
$ga = $st->fetch();

if (!$ga) {
    traJson(['loi' => 'Giáo án không tồn tại'], 404);
}

// Kiểm tra số lượng sl (phải là số nguyên dương)
if (!isset($data['sl'])) {
    $sl = 1;
} else {
    $sl = (!is_array($data['sl']) && !is_object($data['sl']))
        ? filter_var($data['sl'], FILTER_VALIDATE_INT)
        : false;
    if ($sl === false || $sl <= 0) {
        traJson(['loi' => 'Số lượng phải là số nguyên dương lớn hơn 0'], 422);
    }
}

// Kiểm tra tồn kho
$soLuongHienTai = $_SESSION['danh_sach_luu'][$id] ?? 0;
if ($soLuongHienTai + $sl > $ga['ton_kho']) {
    traJson([
        'loi' => 'Số lượng yêu cầu vượt quá tồn kho hiện có (' . $ga['ton_kho'] . ')'
    ], 422);
}

// Cập nhật giỏ hàng trong session
$_SESSION['danh_sach_luu'][$id] = $soLuongHienTai + $sl;
$tongSoMon = count($_SESSION['danh_sach_luu']);

traJson([
    'thanh_cong' => true,
    'so_mon'     => $tongSoMon,
    'thong_bao'  => 'Đã thêm giáo án vào danh sách lưu thành công'
], 200);

