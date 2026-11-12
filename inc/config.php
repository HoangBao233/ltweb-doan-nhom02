<?php
/**
 * config.php — Cấu hình chung của website
 */
// Nạp autoload của Composer (để dùng được các lớp trong src/)
require_once __DIR__ . '/../vendor/autoload.php';

// Nạp các hàm tiện ích
require_once __DIR__ . '/ham.php';

// Nạp kết nối CSDL (PDO Singleton)
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/xac-thuc.php';

// Các hằng số thư mục (đã có)
define('ROOT_DIR', __DIR__ . '/../');
define('DATA_DIR', ROOT_DIR . 'data/');
define('STORAGE_DIR', ROOT_DIR . 'storage/');
define('UPLOADS_DIR', ROOT_DIR . 'uploads/');

// Môi trường ('dev' để hiện lỗi, đổi thành 'prod' khi nộp bài)
// Nop bai voi MOI_TRUONG = 'dev' theo dung yeu cau de bai BTN6
const MOI_TRUONG = 'dev';

// Bật tắt báo lỗi theo môi trường
error_reporting(E_ALL);
ini_set('display_errors', MOI_TRUONG === 'dev' ? '1' : '0');
ini_set('log_errors', '1');
ini_set('error_log', __DIR__ . '/../logs/php-error.log');

// Cấu hình phiên an toàn trước khi khởi tạo phiên (Biện pháp 2)
ini_set('session.use_strict_mode', '1');
session_set_cookie_params([
    'lifetime' => 0,
    'path'     => '/',
    'secure'   => !empty($_SERVER['HTTPS']),
    'httponly' => true,
    'samesite' => 'Lax',
]);

// Khởi tạo phiên làm việc
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Hết hạn sau 30 phút không hoạt động (Biện pháp 2)
if (time() - ($_SESSION['hoat_dong'] ?? time()) > 30 * 60) {
    $_SESSION = [];
    session_regenerate_id(true);
}
$_SESSION['hoat_dong'] = time();

// Bắt lỗi toàn cục và hiện trang 500 khi ở môi trường prod
set_exception_handler(function (Throwable $e) {
    error_log($e->getMessage()); // Luôn ghi log
    if (MOI_TRUONG === 'prod') {
        http_response_code(500);
        require __DIR__ . '/../500.php';
        exit;
    }
    // Nếu đang dev, ném lỗi ra màn hình để dễ sửa
    throw $e;
});

