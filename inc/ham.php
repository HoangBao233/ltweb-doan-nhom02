<?php
/**
 * ham.php — Các hàm tiện ích dùng chung toàn dự án
 */

/**
 * Escape HTML để chống XSS
 */
function e(string $s): string {
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}

/**
 * Định dạng giá tiền VNĐ
 */
function vnd(int|float $gia): string {
    if ($gia === 0 || $gia === 0.0) {
        return 'Miễn phí';
    }
    return number_format($gia, 0, ',', '.') . ' ₫';
}

/**
 * Tạo URL có query string từ trang và tham số
 */
function url(string $trang, array $params = []): string {
    $q = !empty($params) ? '?' . http_build_query($params) : '';
    return e($trang . $q);
}

/**
 * Đặt flash message vào session
 */
function dat_flash(string $loai, string $noi_dung): void {
    $_SESSION['flash'] = ['loai' => $loai, 'noi_dung' => $noi_dung];
}

/**
 * Lấy và xoá flash message khỏi session
 */
function lay_flash(): ?array {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

/**
 * Redirect đến trang khác
 */
function chuyen_trang(string $url): void {
    header('Location: ' . $url);
    exit;
}

/**
 * Lấy số giáo án đang có trong giỏ lưu (session)
 */
function so_luong_gio(): int {
    return count($_SESSION['gio'] ?? []);
}
