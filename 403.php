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
    <main class="trang__chinh hop-loi-403">
        <h1 class="ma-loi-403">403</h1>
        <h2 class="tieu-de-loi-403">Truy cập bị từ chối (Forbidden)</h2>
        <p class="mo-ta-loi-403">
            Bạn không có quyền truy cập vào tài nguyên hoặc chức năng này. Vui lòng đăng nhập bằng tài khoản có thẩm quyền.
        </p>
        <div class="nhom-nut-403">
            <a href="index.php" class="nut-thao-tac nut-thao-tac--xem link-khong-gach">Về Trang chủ</a>
            <a href="dang-nhap.php" class="nut-thao-tac nut-dang-nhap-403">Đăng nhập</a>
        </div>
    </main>
</body>
</html>

