<?php
/**
 * dang-xuat.php — Hủy phiên đăng nhập và chuyển hướng về trang chủ
 * Đồ án Lập trình Web - Nhóm 02
 * Phụ trách: Nguyễn Tiến Thịnh (MSSV: 3120222126) — Chức năng 4
 */

require_once __DIR__ . '/inc/config.php';

// Xóa toàn bộ biến trong Session
$_SESSION = [];

// Xóa Cookie phiên trên trình duyệt nếu có
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// Hủy hoàn toàn phiên làm việc
session_destroy();

// Chuyển hướng về trang chủ
header('Location: index.php');
exit;
