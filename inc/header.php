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

    <header class="trang__dau can-giua-chu" style="display:flex; align-items:center; justify-content:space-between; padding:0.5rem 1rem;">
        <p class="trang__khieu-giao" style="margin:0;">ITeduShare - Cùng giáo viên Tin học kiến tạo tương lai</p>
        <div class="dang-nhap-goc" style="display:flex; align-items:center; gap:0.4rem; white-space:nowrap;">
            <?php if (isset($_SESSION['user'])): ?>
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                <span style="font-weight:600;"><?= e($_SESSION['user']) ?></span>
                <a href="dang-xuat.php" style="margin-left:0.5rem; font-size:0.85rem;">Đăng xuất</a>
            <?php else: ?>
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                <a href="dang-nhap.php" style="font-weight:600;">Đăng nhập</a>
            <?php endif; ?>
        </div>
    </header>

    <nav class="trang__dieu-huong" aria-label="Menu chính">
        <ul id="menu-chinh" class="menu-chinh menu" style="list-style: none;">
            <li><a href="index.php" class="menu-chinh__lien-ket <?= $trang === 'trang-chu' ? 'menu-chinh__lien-ket--hien-tai' : '' ?>">Trang chủ</a></li>
            <li><a href="kho-hoc-lieu.php" class="menu-chinh__lien-ket <?= $trang === 'kho-hoc-lieu' ? 'menu-chinh__lien-ket--hien-tai' : '' ?>">Kho học liệu</a></li>
            <li><a href="gio-hang.php" class="menu-chinh__lien-ket <?= $trang === 'gio-hang' ? 'menu-chinh__lien-ket--hien-tai' : '' ?>">Giáo án đã lưu</a></li>
            <li><a href="ve-chung-toi.php" class="menu-chinh__lien-ket <?= $trang === 've-chung-toi' ? 'menu-chinh__lien-ket--hien-tai' : '' ?>">Về chúng tôi</a></li>
            <li><a href="dong-gop-tai-lieu.php" class="menu-chinh__lien-ket <?= $trang === 'dong-gop-tai-lieu' ? 'menu-chinh__lien-ket--hien-tai' : '' ?>">Đóng góp tài liệu</a></li>
        </ul>
    </nav>
