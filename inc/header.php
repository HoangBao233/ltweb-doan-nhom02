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

// Thông tin người dùng hiện tại
$uHienTai = function_exists('nguoiDung') ? nguoiDung() : null;
$daDangNhap = ($uHienTai !== null);

// Danh sách menu điều hướng
$menu = [
    'index.php'             => 'Trang chủ',
    'kho-hoc-lieu.php'      => 'Kho học liệu',
    'gio-hang.php'          => 'Giáo án đã lưu',
    've-chung-toi.php'      => 'Về chúng tôi',
    'dong-gop-tai-lieu.php' => 'Đóng góp tài liệu',
];

// Phân biệt menu theo vai trò người dùng (Chức năng 4)
if ($daDangNhap) {
    if (($uHienTai['vai_tro'] ?? '') === 'quan_tri') {
        $menu['quan-tri.php'] = 'Quản trị';
        $menu['don-hang.php'] = 'Đơn hàng';
    } else {
        $menu['don-hang.php'] = 'Đơn hàng';
    }
}

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
    <meta name="csrf-token" content="<?= csrfToken() ?>">

    <!-- CSS dùng chung của nhóm -->
    <link rel="stylesheet" href="<?= $goc ?>css/01-bien.css">
    <link rel="stylesheet" href="<?= $goc ?>css/02-chuan-hoa.css">
    <link rel="stylesheet" href="<?= $goc ?>css/03-bo-cuc.css">
    <link rel="stylesheet" href="<?= $goc ?>css/04-thanh-phan.css">
    <link rel="stylesheet" href="<?= $goc ?>css/05-tien-ich.css">
    <?php if ($trangHienTai === 'gio-hang' || $trangHienTai === 'gio-hang.php'): ?>
        <link rel="stylesheet" href="<?= $goc ?>css/gio-hang.css">
    <?php endif; ?>
</head>
<body class="trang">

    <header class="trang__dau can-giua-chu thanh-header-tren">
        <p class="trang__khieu-giao">ITeduShare - Cùng giáo viên Tin học kiến tạo tương lai</p>
        
        <div class="dang-nhap-goc">
            <?php if ($daDangNhap): ?>
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon-tai-khoan"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                <span class="chu-tai-khoan"><?= (($uHienTai['vai_tro'] ?? '') === 'quan_tri') ? 'Quản trị viên' : e($uHienTai['ho_ten'] ?? '') ?></span>
                <span class="vach-ngan-tai-khoan">|</span>
                <form method="post" action="<?= $goc ?>dang-xuat.php" class="form-dang-xuat">
                    <?= csrfField() ?>
                    <button type="submit" class="nut-dang-xuat-header">Đăng xuất</button>
                </form>
            <?php else: ?>
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon-tai-khoan"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                <a href="<?= $goc ?>dang-nhap.php" class="link-tai-khoan">Đăng nhập</a>
                <span class="vach-ngan-tai-khoan">|</span>
                <a href="<?= $goc ?>dang-ky.php" class="link-tai-khoan">Đăng ký</a>
            <?php endif; ?>
        </div>
    </header>

    <nav class="trang__dieu-huong" aria-label="Menu chính">
        <!-- Nút Menu hiển thị trên Mobile (Mặc định ẩn trên Desktop) -->
        <button type="button" class="nut-menu" aria-expanded="false" aria-controls="menu-chinh">☰ Menu</button>

        <ul id="menu-chinh" class="menu-chinh menu">
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
                            <span class="huy-hieu-so-luong"><?= $soLuongLuu ?></span>
                        <?php endif; ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </nav>
