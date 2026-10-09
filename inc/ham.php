<?php
/**
 * ham.php — Các hàm tiện ích đơn giản
 */

// Escape chống XSS
function e(mixed $str): string {
    return htmlspecialchars((string)$str, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

// Định dạng tiền VNĐ
function vnd($gia): string {
    $gia = (float)$gia;
    if ($gia === 0.0) {
        return 'Miễn phí';
    }
    return number_format($gia, 0, ',', '.') . ' ₫';
}

// Chuyển hướng trang
function chuyen_trang(string $url): void {
    header('Location: ' . $url);
    exit;
}
