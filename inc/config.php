<?php
/**
 * config.php — Cấu hình toàn cục, nạp đầu tiên ở mọi trang
 */

// Đường dẫn gốc của dự án (thư mục chứa index.php)
define('ROOT', dirname(__DIR__));
define('DS',   DIRECTORY_SEPARATOR);

// Thư mục lưu trữ nội bộ
define('STORAGE_DIR', ROOT . DS . 'storage' . DS);
define('LOGS_DIR',    ROOT . DS . 'logs'    . DS);
define('UPLOADS_DIR', ROOT . DS . 'uploads' . DS);
define('DATA_DIR',    ROOT . DS . 'data'    . DS);

// Cấu hình hiển thị lỗi (tắt trên production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Ghi log lỗi vào file
ini_set('log_errors', 1);
ini_set('error_log', LOGS_DIR . 'php-error.log');

// Khởi động Session (chỉ 1 lần)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Nạp các hàm tiện ích
require_once __DIR__ . DS . 'ham.php';
