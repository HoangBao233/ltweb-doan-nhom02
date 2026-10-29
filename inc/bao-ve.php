<?php
/**
 * bao-ve.php — Middleware bảo vệ các trang quản trị hệ thống
 * Đồ án Lập trình Web - Nhóm 02
 * Phụ trách: Nguyễn Tiến Thịnh (MSSV: 3120222126) — Chức năng 4
 */

require_once __DIR__ . '/xac-thuc.php';

// Yêu cầu quyền 'quan_tri':
// - Chưa đăng nhập -> Chuyển hướng sang dang-nhap.php (Ca 8)
// - Khách hàng đăng nhập -> Trả mã HTTP 403 Forbidden và hiển thị 403.php (Ca 8)
yeuCauQuyen('quan_tri');
