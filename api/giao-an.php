<?php
// api/giao-an.php — REST API tìm kiếm và xem chi tiết giáo án (trả về JSON)
// Phụ trách: Tấn (MSSV 3120124027) — Chức năng 6 Bảng 1
require_once __DIR__ . '/../inc/config.php';
use App\Models\GiaoAn;

// Đăng ký bộ xử lý ngoại lệ trả về JSON chuẩn HTTP 500 khi máy chủ gặp lỗi
set_exception_handler(function (Throwable $e) {
    error_log('Lỗi API giao-an: ' . $e->getMessage());
    traJson(['loi' => 'Lỗi máy chủ'], 500);
});

// Chỉ chấp nhận phương thức GET
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    header('Allow: GET');
    traJson(['loi' => 'Phương thức không được hỗ trợ'], 405);
}

// Trường hợp 1: Xem chi tiết một giáo án theo ?id=
if (isset($_GET['id'])) {
    $id = filter_var($_GET['id'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    if ($id === false) {
        traJson(['loi' => 'ID giáo án không hợp lệ'], 400);
    }

    $st = db()->prepare('SELECT g.id, g.danh_muc_id, g.ten, g.cap_hoc, g.gia, g.ton_kho, g.hinh_anh, g.video_url, g.mo_ta, g.luot_tai, d.ten AS ten_danh_muc FROM giao_an g LEFT JOIN danh_muc d ON g.danh_muc_id = d.id WHERE g.id = ?');
    $st->execute([$id]);
    $row = $st->fetch();

    if (!$row) {
        traJson(['loi' => 'Không tìm thấy giáo án'], 404);
    }

    traJson(['du_lieu' => GiaoAn::tuMang($row)], 200);
}

// Trường hợp 2: Tìm kiếm theo từ khóa ?q= hoặc lấy danh sách mới nhất
$q = trim($_GET['q'] ?? '');

if ($q !== '') {
    $st = db()->prepare('SELECT id, danh_muc_id, ten, cap_hoc, gia, ton_kho, hinh_anh, video_url, mo_ta, luot_tai FROM giao_an WHERE ten LIKE ? ORDER BY id DESC LIMIT 20');
    $st->execute(['%' . addcslashes($q, '%_\\') . '%']);
} else {
    $st = db()->query('SELECT id, danh_muc_id, ten, cap_hoc, gia, ton_kho, hinh_anh, video_url, mo_ta, luot_tai FROM giao_an ORDER BY id DESC LIMIT 20');
}

$ds = array_map(fn(array $row) => GiaoAn::tuMang($row), $st->fetchAll());

traJson(['du_lieu' => $ds], 200);

