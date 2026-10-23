<?php
// 403.php — Trang báo lỗi 403 Forbidden (Truy cập bị từ chối)
http_response_code(403);
$tieuDe = '403 — Từ chối truy cập | ITeduShare';
$trang  = '';
$goc    = '';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($tieuDe, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></title>
    <link rel="stylesheet" href="css/01-bien.css">
    <link rel="stylesheet" href="css/02-chuan-hoa.css">
    <link rel="stylesheet" href="css/03-bo-cuc.css">
    <link rel="stylesheet" href="css/04-thanh-phan.css">
    <link rel="stylesheet" href="css/05-tien-ich.css">
</head>
<body>
    <main class="trang__chinh" style="max-width: 600px; margin: 4rem auto; text-align: center; padding: 2rem 1rem;">
        <h1 style="font-size: 4rem; color: #e53e3e; margin-bottom: 1rem;">403</h1>
        <h2 style="margin-bottom: 1rem;">Truy cập bị từ chối (Forbidden)</h2>
        <p style="color: #4a5568; margin-bottom: 2rem;">
            Bạn không có quyền truy cập vào tài nguyên hoặc chức năng này. Vui lòng đăng nhập bằng tài khoản có thẩm quyền.
        </p>
        <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
            <a href="index.php" class="nut-thao-tac nut-thao-tac--xem" style="text-decoration: none;">Về Trang chủ</a>
            <a href="dang-nhap.php" class="nut-thao-tac" style="text-decoration: none; background: #4a5568; color: #fff;">Đăng nhập</a>
        </div>
    </main>
</body>
</html>

