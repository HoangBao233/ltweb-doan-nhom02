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
<body style="background: #f8fafc; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; margin: 0; padding: 0; color: #1e293b;">
    <header style="background: #ffffff; border-bottom: 1px solid #e2e8f0; padding: 1rem 1.5rem; display: flex; align-items: center; justify-content: space-between;">
        <div style="font-weight: 700; font-size: 1.25rem; color: #004085;">
            📚 ITeduShare
        </div>
        <a href="index.php" style="color: #0284c7; text-decoration: none; font-size: 0.95rem; font-weight: 500;">
            ← Về Trang chủ
        </a>
    </header>

    <main class="trang__chinh" style="max-width: 600px; margin: 4rem auto; text-align: center; padding: 2.5rem 1.5rem; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; box-shadow: 0 4px 16px rgba(0,0,0,0.06);">
        <div style="font-size: 4rem; line-height: 1; color: #e11d48; font-weight: 800; margin-bottom: 0.75rem;">
            500
        </div>
        <h1 style="font-size: 1.5rem; color: #0f172a; margin: 0 0 1rem; font-weight: 700;">
            Hệ thống đang gặp sự cố tạm thời
        </h1>
        <p style="color: #64748b; line-height: 1.6; margin: 0 0 2rem; font-size: 1rem;">
            Máy chủ hoặc cơ sở dữ liệu hiện không thể xử lý yêu cầu của bạn. 
            Đội ngũ quản trị đã ghi nhận sự cố vào nhật ký hệ thống và đang khắc phục. Vui lòng quay lại sau ít phút.
        </p>
        <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
            <a href="index.php" class="nut-thao-tac nut-thao-tac--xem" style="display: inline-block; padding: 0.65rem 1.5rem; background: #0284c7; color: #ffffff; text-decoration: none; border-radius: 6px; font-weight: 600; font-size: 0.95rem;">
                Quay về Trang chủ
            </a>
            <button type="button" onclick="window.location.reload();" style="padding: 0.65rem 1.5rem; background: #f1f5f9; color: #334155; border: 1px solid #cbd5e1; border-radius: 6px; font-weight: 600; font-size: 0.95rem; cursor: pointer;">
                🔄 Thử tải lại
            </button>
        </div>
    </main>

    <footer style="text-align: center; color: #94a3b8; font-size: 0.85rem; padding: 2rem 1rem;">
        &copy; <?= date('Y') ?> ITeduShare — Nền tảng chia sẻ giáo án &amp; học liệu Tin học.
    </footer>
</body>
</html>
