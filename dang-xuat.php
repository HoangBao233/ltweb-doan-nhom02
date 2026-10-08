<?php
// dang-xuat.php — Huỷ phiên và chuyển về trang chủ
// Phụ trách: Tấn (MSSV 3120124027) + Châu — Chức năng 6 Bảng 1
require __DIR__ . '/inc/config.php';

session_destroy();
header('Location: index.php');
exit;

