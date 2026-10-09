<?php
/**
 * header.php — Header + Menu điều hướng dùng chung
 */
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/ham.php';

$trang = $trang ?? $trang_hien_tai ?? '';
$goc   = $goc ?? '';
$tieuDeTrang = $tieuDe ?? $tieu_de_trang ?? 'Trang chủ';
$metaMoTa = $meta_mo_ta ?? 'ITeduShare - Nền tảng chia sẻ giáo án Tin học';

// Lấy số lượng giáo án đã lưu từ Session
$soLuongLuu = 0;
if (class_exists('App\Services\DanhSachLuu')) {
    $dsLuuHeader = new \App\Services\DanhSachLuu();
    $soLuongLuu = $dsLuuHeader->soMon();
} elseif (isset($_SESSION['danh_sach_luu']) && is_array($_SESSION['danh_sach_luu'])) {
    $soLuongLuu = count($_SESSION['danh_sach_luu']);
}

// Danh sách menu điều hướng
$menu = [
    'index.php'             => 'Trang chủ',
    'kho-hoc-lieu.php'      => 'Kho học liệu',
    'gio-hang.php'          => 'Giáo án đã lưu',
    've-chung-toi.php'      => 'Về chúng tôi',
    'dong-gop-tai-lieu.php' => 'Đóng góp tài liệu',
];

// Chuẩn hóa tên trang hiện tại để đánh dấu menu đang xem
$trangHienTai = $trang;
if ($trangHienTai === 'index' || $trangHienTai === 'trang-chu') {
    $trangHienTai = 'index.php';
} elseif ($trangHienTai === 'chi-tiet-giao-an') {
    $trangHienTai = 'kho-hoc-lieu.php';
}
?>
<!DOCTYPE html>
<html lang="vi" class="js">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= e($metaMoTa) ?>">
    <title><?= e($tieuDeTrang) ?> | ITeduShare</title>
    
    <!-- Đánh dấu hỗ trợ JS ngay từ đầu để kích hoạt Menu Mobile mượt mà -->
    <script>document.documentElement.classList.add('js');</script>

    <!-- CSS dùng chung của nhóm -->
    <link rel="stylesheet" href="<?= $goc ?>css/01-bien.css">
    <link rel="stylesheet" href="<?= $goc ?>css/02-chuan-hoa.css">
    <link rel="stylesheet" href="<?= $goc ?>css/03-bo-cuc.css">
    <link rel="stylesheet" href="<?= $goc ?>css/04-thanh-phan.css">
    <link rel="stylesheet" href="<?= $goc ?>css/05-tien-ich.css">
</head>
<body class="trang">

    <header class="trang__dau can-giua-chu" style="display:flex; align-items:center; justify-content:space-between; padding:0.5rem 1rem;">
        <p class="trang__khieu-giao" style="margin:0;">ITeduShare - Cùng giáo viên Tin học kiến tạo tương lai</p>
        
        <div class="dang-nhap-goc" style="display:flex; align-items:center; gap:0.4rem; white-space:nowrap;">
            <?php if (isset($_SESSION['user'])): ?>
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                <span style="font-weight:600;"><?= e($_SESSION['user']) ?></span>
                <a href="<?= $goc ?>dang-xuat.php" style="margin-left:0.5rem; font-size:0.85rem;">Đăng xuất</a>
            <?php else: ?>
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                <a href="<?= $goc ?>dang-nhap.php" style="font-weight:600;">Đăng nhập</a>
            <?php endif; ?>
        </div>
    </header>

    <nav class="trang__dieu-huong" aria-label="Menu chính">
        <!-- Nút Menu hiển thị trên Mobile (Mặc định ẩn trên Desktop) -->
        <button type="button" class="nut-menu" aria-expanded="false" aria-controls="menu-chinh">☰ Menu</button>

        <ul id="menu-chinh" class="menu-chinh menu" style="list-style: none;">
            <?php foreach ($menu as $tep => $ten): 
                $tepKhongDuoi = str_replace('.php', '', $tep);
                $isCurrent = ($trangHienTai === $tep || $trangHienTai === $tepKhongDuoi);
            ?>
                <li>
                    <a href="<?= $goc ?><?= $tep ?>" 
                       class="menu-chinh__lien-ket <?= $isCurrent ? 'menu-chinh__lien-ket--kich-hoat' : '' ?>"
                       <?= $isCurrent ? 'aria-current="page"' : '' ?>>
                        <?= e($ten) ?>
                        <?php if ($tep === 'gio-hang.php'): ?>
                            <span class="huy-hieu-so-luong" style="background: var(--mau-nhan, #e74c3c); color: #fff; border-radius: 999px; padding: 0.1rem 0.5rem; font-size: 0.75rem; font-weight: bold; margin-left: 0.35rem; display: inline-block; vertical-align: middle;"><?= $soLuongLuu ?></span>
                        <?php endif; ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </nav>
    <script>
    (function() {
        var nutMenu = document.querySelector('.nut-menu');
        var menuChinh = document.querySelector('.menu');
        if (nutMenu && menuChinh && !nutMenu.dataset.daGan) {
            nutMenu.dataset.daGan = 'true';
            nutMenu.addEventListener('click', function() {
                var dangMo = menuChinh.classList.toggle('mo');
                nutMenu.setAttribute('aria-expanded', dangMo ? 'true' : 'false');
                nutMenu.textContent = dangMo ? '✕ Đóng' : '☰ Menu';
            });
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && menuChinh.classList.contains('mo')) {
                    menuChinh.classList.remove('mo');
                    nutMenu.setAttribute('aria-expanded', 'false');
                    nutMenu.textContent = '☰ Menu';
                    nutMenu.focus();
                }
            });
        }
    })();
    </script>
