<?php
// api/gio-hang.php — REST API quản lý giỏ hàng / danh sách lưu giáo án
// Phụ trách: Tấn & Châu (Chức năng 6 Bảng 1)
require_once __DIR__ . '/../inc/config.php';

// Đăng ký bộ xử lý ngoại lệ trả về JSON chuẩn HTTP 500 khi máy chủ gặp lỗi
set_exception_handler(function (Throwable $e) {
    if (!headers_sent()) {
        http_response_code(500);
        header('Content-Type: application/json; charset=utf-8');
    }
    echo json_encode(['loi' => 'Lỗi máy chủ'], JSON_UNESCAPED_UNICODE);
    exit;
});

// Thiết lập header trả về JSON chuẩn UTF-8
header('Content-Type: application/json; charset=utf-8');

// Chỉ cho phép phương thức GET và POST
if (!in_array($_SERVER['REQUEST_METHOD'], ['GET', 'POST'], true)) {
    http_response_code(405);
    header('Allow: GET, POST');
    echo json_encode(['loi' => 'Phương thức không được hỗ trợ'], JSON_UNESCAPED_UNICODE);
    exit;
}

// Khởi tạo mảng giỏ hàng trong Session nếu chưa có
if (!isset($_SESSION['danh_sach_luu']) || !is_array($_SESSION['danh_sach_luu'])) {
    $_SESSION['danh_sach_luu'] = [];
}

// 1. Phương thức GET: Xem số món và nội dung giỏ hàng hiện tại
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    http_response_code(200);
    echo json_encode([
        'so_mon'   => count($_SESSION['danh_sach_luu']),
        'gio_hang' => $_SESSION['danh_sach_luu'],
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// 2. Phương thức POST: Thêm giáo án vào giỏ hàng qua thân JSON
$raw = file_get_contents('php://input');
$data = json_decode($raw, true);

// Kiểm tra định dạng thân request có phải JSON hợp lệ không
if (!is_array($data)) {
    http_response_code(400);
    echo json_encode(['loi' => 'Dữ liệu yêu cầu phải là JSON hợp lệ'], JSON_UNESCAPED_UNICODE);
    exit;
}

// Kiểm tra mã giáo án ID
if (!isset($data['id'])) {
    http_response_code(404);
    echo json_encode(['loi' => 'Thiếu ID giáo án'], JSON_UNESCAPED_UNICODE);
    exit;
}

$id = filter_var($data['id'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
if ($id === false) {
    http_response_code(404);
    echo json_encode(['loi' => 'ID giáo án không hợp lệ'], JSON_UNESCAPED_UNICODE);
    exit;
}

// Kiểm tra giáo án có trong CSDL không
$st = db()->prepare('SELECT id, ten, ton_kho FROM giao_an WHERE id = ?');
$st->execute([$id]);
$ga = $st->fetch();

if (!$ga) {
    http_response_code(404);
    echo json_encode(['loi' => 'Giáo án không tồn tại'], JSON_UNESCAPED_UNICODE);
    exit;
}

// Kiểm tra số lượng sl (phải là số nguyên dương)
if (!isset($data['sl'])) {
    $sl = 1;
} else {
    $sl = filter_var($data['sl'], FILTER_VALIDATE_INT);
    if ($sl === false || $sl <= 0) {
        http_response_code(422);
        echo json_encode(['loi' => 'Số lượng phải là số nguyên dương lớn hơn 0'], JSON_UNESCAPED_UNICODE);
        exit;
    }
}

// Kiểm tra tồn kho
$soLuongHienTai = $_SESSION['danh_sach_luu'][$id] ?? 0;
if ($soLuongHienTai + $sl > $ga['ton_kho']) {
    http_response_code(422);
    echo json_encode([
        'loi' => 'Số lượng yêu cầu vượt quá tồn kho hiện có (' . $ga['ton_kho'] . ')'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// Cập nhật giỏ hàng trong session
$_SESSION['danh_sach_luu'][$id] = $soLuongHienTai + $sl;
$tongSoMon = count($_SESSION['danh_sach_luu']);

http_response_code(200);
echo json_encode([
    'thanh_cong' => true,
    'so_mon'     => $tongSoMon,
    'thong_bao'  => 'Đã thêm giáo án vào danh sách lưu thành công'
], JSON_UNESCAPED_UNICODE);

