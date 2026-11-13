<?php
/**
 * dang-xuat.php — Hủy phiên đăng nhập và chuyển hướng về trang chủ (chỉ nhận POST)
 * Đồ án Lập trình Web - Nhóm 02
 * Phụ trách: Nguyễn Tiến Thịnh & Lê Hoàng Bảo — Chức năng 4 & Biện pháp 1, 2 (Chương 7)
 */

require_once __DIR__ . '/inc/config.php';

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    exit('Phương thức không được hỗ trợ');
}

dangXuat();

// Chuyển hướng về trang chủ
header('Location: index.php');
exit;
