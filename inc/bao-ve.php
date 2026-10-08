<?php
/**
 * bao-ve.php — Yêu cầu file này ở đầu các trang cần đăng nhập
 */
if (empty($_SESSION['user'])) {
    // Nếu chưa đăng nhập, chuyển hướng về trang đăng nhập
    $goc = $goc ?? '';
    header('Location: ' . $goc . 'dang-nhap.php');
    exit;
}

