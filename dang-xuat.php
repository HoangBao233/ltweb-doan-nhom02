<?php
/**
 * dang-xuat.php — Huỷ session và đăng xuất
 */
require_once 'inc/config.php';

// Xoá toàn bộ session
$_SESSION = [];
if (ini_get('session.use_cookies')) {
    $p = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $p['path'], $p['domain'], $p['secure'], $p['httponly']);
}
session_destroy();

// Redirect về trang chủ
header('Location: index.php');
exit;
