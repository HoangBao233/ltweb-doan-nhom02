<?php
/**
 * inc/csrf.php — Tạo và kiểm tra mã chống giả mạo yêu cầu liên trang (CSRF)
 * Phụ trách: Lê Hoàng Bảo (MSSV: 3120124003) — Chương 7, Biện pháp 1
 */

/**
 * Lấy hoặc khởi tạo mã CSRF ngẫu nhiên 64 ký tự hex trong phiên hiện tại.
 */
function csrfToken(): string
{
    if (empty($_SESSION['csrf']) || !is_string($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

/**
 * Tạo thẻ input ẩn chứa mã CSRF để nhúng vào bên trong các <form method="post">.
 */
function csrfField(): string
{
    return '<input type="hidden" name="csrf" value="' . csrfToken() . '">';
}

/**
 * Kiểm tra mã CSRF gửi lên từ biểu mẫu ($_POST['csrf']) hoặc từ fetch (X-CSRF-Token).
 * Chặn luôn trường hợp đầu vào là mảng (ví dụ csrf[]=abc) nhờ is_string($gui).
 */
function csrfHopLe(): bool
{
    $gui = $_POST['csrf'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    return is_string($gui) && hash_equals(csrfToken(), $gui);
}

