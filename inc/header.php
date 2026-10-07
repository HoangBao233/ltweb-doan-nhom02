<?php
/**
 * header.php — Header + Nav dùng chung cho mọi trang
 *
 * Biến nhận từ trang gọi:
 *   $tieu_de_trang   : string — tiêu đề trang (dùng trong <title>)
 *   $meta_mo_ta      : string — meta description
 *   $trang_hien_tai  : string — tên file (vd: 'index', 'kho-hoc-lieu')
 */
$tieu_de_trang  = $tieu_de_trang  ?? 'ITeduShare';
$meta_mo_ta     = $meta_mo_ta     ?? 'Nền tảng chia sẻ học liệu và giáo án môn Tin học chuẩn GDPT 2018.';
$trang_hien_tai = $trang_hien_tai ?? '';

// Danh sách menu
$menu = [
    'index'              => ['href' => 'index.php',              'nhan' => 'Trang chủ'],
    'kho-hoc-lieu'       => ['href' => 'kho-hoc-lieu.php',       'nhan' => 'Kho học liệu'],
    've-chung-toi'       => ['href' => 've-chung-toi.php',       'nhan' => 'Về chúng tôi'],
    'dong-gop-tai-lieu'  => ['href' => 'dong-gop-tai-lieu.php',  'nhan' => 'Đóng góp tài liệu'],
];
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= e($meta_mo_ta) ?>">
    <title><?= e($tieu_de_trang) ?> | ITeduShare</title>

    <!-- Nạp 5 file CSS theo đúng thứ tự ưu tiên -->
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
            <?php foreach ($menu as $key => $item): ?>
                <?php $kich_hoat = ($trang_hien_tai === $key) ? ' menu-chinh__lien-ket--kich-hoat' : ''; ?>
                <li>
                    <a href="<?= e($item['href']) ?>"
                       class="menu-chinh__lien-ket<?= $kich_hoat ?>"
                       <?= ($trang_hien_tai === $key) ? 'aria-current="page"' : '' ?>>
                        <?= e($item['nhan']) ?>
                    </a>
                </li>
            <?php endforeach; ?>

            <!-- Nút giỏ lưu -->
            <li>
                <a href="gio-hang.php" class="menu-chinh__lien-ket<?= ($trang_hien_tai === 'gio-hang') ? ' menu-chinh__lien-ket--kich-hoat' : '' ?>"
                   title="Giáo án đã lưu" style="font-weight: bold; color: var(--mau-nhan, #ff5722);">
                    🔖 Đã lưu: <span id="dem-yeu-thich" aria-live="polite"><?= so_luong_gio() ?></span>
                </a>
            </li>

            <!-- Đăng nhập / Đăng xuất -->
            <?php if (!empty($_SESSION['admin'])): ?>
            <li>
                <a href="quan-tri.php" class="menu-chinh__lien-ket">⚙ Quản trị</a>
            </li>
            <li>
                <a href="dang-xuat.php" class="menu-chinh__lien-ket">Đăng xuất</a>
            </li>
            <?php else: ?>
            <li>
                <a href="dang-nhap.php" class="menu-chinh__lien-ket<?= ($trang_hien_tai === 'dang-nhap') ? ' menu-chinh__lien-ket--kich-hoat' : '' ?>">Đăng nhập</a>
            </li>
            <?php endif; ?>
        </ul>
    </nav>
