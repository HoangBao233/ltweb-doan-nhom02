<?php
// api/giao-an.php — REST API tìm kiếm và xem chi tiết giáo án (trả về JSON)
// Phụ trách: Tấn (MSSV 3120124027) — Chức năng 6 Bảng 1
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

// Chỉ chấp nhận phương thức GET
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    header('Allow: GET');
    echo json_encode(['loi' => 'Phương thức không được hỗ trợ'], JSON_UNESCAPED_UNICODE);
    exit;
}

// Trường hợp 1: Xem chi tiết một giáo án theo ?id=
if (isset($_GET['id'])) {
    $id = filter_var($_GET['id'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    if ($id === false) {
        http_response_code(400);
        echo json_encode(['loi' => 'ID giáo án không hợp lệ'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $st = db()->prepare('SELECT id, ten, cap_hoc, gia, ton_kho, hinh_anh, video_url, mo_ta, luot_tai FROM giao_an WHERE id = ?');
    $st->execute([$id]);
    $ga = $st->fetch();

    if (!$ga) {
        http_response_code(404);
        echo json_encode(['loi' => 'Không tìm thấy giáo án'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    http_response_code(200);
    echo json_encode(['du_lieu' => $ga], JSON_UNESCAPED_UNICODE);
    exit;
}

// Trường hợp 2: Tìm kiếm theo từ khóa ?q= hoặc lấy danh sách mới nhất
$q = trim($_GET['q'] ?? '');

if ($q !== '') {
    $st = db()->prepare('SELECT id, ten, cap_hoc, gia, hinh_anh, mo_ta FROM giao_an WHERE ten LIKE ? ORDER BY id DESC LIMIT 20');
    $st->execute(['%' . addcslashes($q, '%_\\') . '%']);
} else {
    $st = db()->query('SELECT id, ten, cap_hoc, gia, hinh_anh, mo_ta FROM giao_an ORDER BY id DESC LIMIT 20');
}

$ds = $st->fetchAll();

http_response_code(200);
echo json_encode(['du_lieu' => $ds], JSON_UNESCAPED_UNICODE);

