<?php
/**
 * bao-ve.php — Bảo vệ trang chỉ cho admin đã đăng nhập
 * Nạp file này ở đầu các trang cần bảo vệ (quan-tri.php...)
 */
require_once __DIR__ . '/config.php';

if (empty($_SESSION['admin'])) {
    dat_flash('loi', 'Bạn cần đăng nhập để truy cập trang này.');
    chuyen_trang('../dang-nhap.php');
}
