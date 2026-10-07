<?php
/**
 * header.php — Header + Menu điều hướng dùng chung
 */
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/ham.php';

$trang = $trang_hien_tai ?? '';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= e($meta_mo_ta ?? 'ITeduShare - Nền tảng chia sẻ giáo án Tin học') ?>">
    <title><?= e(($tieu_de_trang ?? 'Trang chủ') . ' | ITeduShare') ?></title>
    
    <!-- CSS dùng chung của nhóm -->
    <link rel="stylesheet" href="css/01-bien.css">
    <link rel="stylesheet" href="css/02-chuan-hoa.css">
    <link rel="stylesheet" href="css/03-bo-cuc.css">
    <link rel="stylesheet" href="css/04-thanh-phan.css">
    <link rel="stylesheet" href="css/05-tien-ich.css">
</head>
<body class="trang">

    <header class="trang__dau can-giua-chu">
        <p class="trang__khieu-giao">ITeduShare - Cùng giáo viên Tin học kiến tạo tương lai</p>
    </header>

    <nav class="trang__dieu-huong" aria-label="Menu chính">
        <button type="button" class="nut-menu" aria-expanded="false" aria-controls="menu-chinh">☰ Menu</button>
        <ul id="menu-chinh" class="menu-chinh menu">
            <li><a href="index.php" class="menu-chinh__lien-ket <?= $trang === 'trang-chu' ? 'menu-chinh__lien-ket--hien-tai' : '' ?>">Trang chủ</a></li>
            <li><a href="kho-hoc-lieu.php" class="menu-chinh__lien-ket <?= $trang === 'kho-hoc-lieu' ? 'menu-chinh__lien-ket--hien-tai' : '' ?>">Kho học liệu</a></li>
            <li><a href="gio-hang.php" class="menu-chinh__lien-ket <?= $trang === 'gio-hang' ? 'menu-chinh__lien-ket--hien-tai' : '' ?>">Giáo án đã lưu</a></li>
            <li><a href="ve-chung-toi.php" class="menu-chinh__lien-ket <?= $trang === 've-chung-toi' ? 'menu-chinh__lien-ket--hien-tai' : '' ?>">Về chúng tôi</a></li>
            <li><a href="dong-gop-tai-lieu.php" class="menu-chinh__lien-ket <?= $trang === 'dong-gop-tai-lieu' ? 'menu-chinh__lien-ket--hien-tai' : '' ?>">Đóng góp tài liệu</a></li>
            <li><a href="dang-nhap.php" class="menu-chinh__lien-ket <?= $trang === 'dang-nhap' ? 'menu-chinh__lien-ket--hien-tai' : '' ?>">Đăng nhập</a></li>
        </ul>
    </nav>
