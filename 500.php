<?php
/**
 * 500.php — Trang báo lỗi máy chủ 500 (Internal Server Error)
 * 
 * Đồ án Lập trình Web - Nhóm 02
 * Lưu ý đặc biệt (Đề bài mục 2.1 & Ca 14):
 * Tuyệt đối KHÔNG nạp inc/header.php hoặc gọi hàm nguoiDung() / truy vấn CSDL tại đây.
 * Khi CSDL gặp sự cố (máy chủ MySQL dừng hoặc cấu hình sai), trang 500 phải hiển thị độc lập
 * thân thiện cho người dùng, không làm sập trang hay gây màn hình trắng.
 */

if (!headers_sent()) {
    http_response_code(500);
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 — Sự cố máy chủ | ITeduShare</title>
    <link rel="stylesheet" href="css/01-bien.css">
    <link rel="stylesheet" href="css/02-chuan-hoa.css">
    <link rel="stylesheet" href="css/03-bo-cuc.css">
    <link rel="stylesheet" href="css/04-thanh-phan.css">
    <link rel="stylesheet" href="css/05-tien-ich.css">
</head>
<body class="than-trang-500">
    <header class="dau-trang-500">
        <div class="logo-trang-500">
            ITeduShare
        </div>
        <a href="index.php" class="link-ve-trang-chu-500">
            ← Về Trang chủ
        </a>
    </header>

    <main class="trang__chinh hop-loi-500">
        <div class="ma-loi-500">
            500
        </div>
        <h1 class="tieu-de-loi-500">
            Hệ thống đang gặp sự cố tạm thời
        </h1>
        <p class="mo-ta-loi-500">
            Máy chủ hoặc cơ sở dữ liệu hiện không thể xử lý yêu cầu của bạn. 
            Đội ngũ quản trị đã ghi nhận sự cố vào nhật ký hệ thống và đang khắc phục. Vui lòng quay lại sau ít phút.
        </p>
        <div class="nhom-nut-500">
            <a href="index.php" class="nut-thao-tac nut-thao-tac--xem nut-ve-trang-chu-500">
                Quay về Trang chủ
            </a>
            <a href="" class="nut-tai-lai-500">
                Thử tải lại
            </a>
        </div>
    </main>

    <footer class="chan-trang-500">
        &copy; <?= date('Y') ?> ITeduShare — Nền tảng chia sẻ giáo án &amp; học liệu Tin học.
    </footer>
</body>
</html>
