<?php
/**
 * xoa-giao-an.php — Xử lý xóa giáo án an toàn và bắt lỗi khóa ngoại MySQL 1451
 * 
 * Phụ trách: Lê Thị Xuân Châu (MSSV: 3120124006) — Phần A, Chức năng 3
 * Yêu cầu kỹ thuật:
 *  - yeuCauQuyen('quan_tri')
 *  - CHỈ nhận phương thức POST (từ chối GET bằng HTTP 405 Method Not Allowed)
 *  - Bắt lỗi ràng buộc khóa ngoại MySQL 1451 (khi giáo án đã có trong đơn hàng)
 *  - PRG chuyển hướng về quan-tri.php kèm Flash message
 */

require_once __DIR__ . '/inc/config.php';

// 1. Phân quyền: Chỉ quản trị viên mới được thao tác xóa
yeuCauQuyen('quan_tri');

// 2. Chặn truy cập bằng phương thức GET (Chỉ nhận POST theo chuẩn bảo mật đề bài)
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    ?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>405 — Thao tác không được phép | ITeduShare</title>
    <link rel="stylesheet" href="css/01-bien.css">
    <link rel="stylesheet" href="css/02-chuan-hoa.css">
    <link rel="stylesheet" href="css/03-bo-cuc.css">
    <link rel="stylesheet" href="css/04-thanh-phan.css">
    <link rel="stylesheet" href="css/05-tien-ich.css">
</head>
<body style="background: #f8fafc; font-family: inherit;">
    <main class="trang__chinh" style="max-width: 550px; margin: 4rem auto; text-align: center; padding: 2.5rem 1.5rem; background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.05);">
        <h1 style="font-size: 3.5rem; color: #e11d48; margin: 0 0 0.5rem; font-weight: 800;">405</h1>
        <h2 style="margin: 0 0 1rem; color: #1e293b; font-size: 1.35rem;">Thao tác không được phép</h2>
        <p style="color: #64748b; margin-bottom: 2rem; line-height: 1.6; font-size: 0.95rem;">
            Thao tác xóa chỉ được thực hiện từ nút bấm trong hệ thống, không hỗ trợ mở trực tiếp bằng thanh địa chỉ trình duyệt.
        </p>
        <a href="quan-tri.php" style="display: inline-block; padding: 0.65rem 1.5rem; background: #0284c7; color: #fff; text-decoration: none; border-radius: 6px; font-weight: 600; font-size: 0.95rem;">
            ← Quay lại Trang Quản trị
        </a>
    </main>
</body>
</html>
    <?php
    exit;
}

// 3. Lấy và kiểm tra ID giáo án cần xóa
$id = filter_var($_POST['id'] ?? '', FILTER_VALIDATE_INT);
if (!$id || $id <= 0) {
    $_SESSION['flash_error'] = 'Mã giáo án không hợp lệ.';
    header('Location: quan-tri.php');
    exit;
}

// 4. Thực hiện xóa với khối try-catch để bắt lỗi ràng buộc toàn vẹn khóa ngoại 1451
try {
    $st = db()->prepare('DELETE FROM giao_an WHERE id = ?');
    $st->execute([$id]);

    if ($st->rowCount() > 0) {
        $_SESSION['flash'] = "Đã xóa thành công giáo án #$id khỏi hệ thống!";
    } else {
        $_SESSION['flash_error'] = "Không tìm thấy giáo án #$id để xóa (có thể đã bị xóa trước đó).";
    }
} catch (PDOException $e) {
    // Kiểm tra mã lỗi driver MySQL 1451 (ER_ROW_IS_REFERENCED_2)
    $maLoiDriver = (int)($e->errorInfo[1] ?? 0);

    if ($maLoiDriver === 1451) {
        // Thông báo thân thiện khi vi phạm khóa ngoại thay vì để sập trang
        $_SESSION['flash_error'] = "Không thể xóa giáo án #$id vì giáo án này đã có trong lịch sử đơn hàng của học viên.";
    } else {
        error_log("Lỗi xóa giáo án #$id: " . $e->getMessage());
        $_SESSION['flash_error'] = "Đã xảy ra lỗi trong quá trình xóa dữ liệu (Mã lỗi: " . ($maLoiDriver ?: 'SQL') . ").";
    }
}

// 5. Chuyển hướng PRG về trang quản trị
header('Location: quan-tri.php');
exit;

