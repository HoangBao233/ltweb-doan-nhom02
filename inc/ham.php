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

// Chuẩn hóa đường dẫn ảnh bìa giáo án / tệp tải lên để hiển thị đồng bộ ở cả trang công khai và quản trị
function anhGiaoAn(?string $hinhAnh, string $goc = ''): string {
    $duongDan = trim((string)$hinhAnh);
    if ($duongDan === '') {
        return $goc . 'images/banner-trang-chu.jpg';
    }

    if (str_starts_with($duongDan, 'http://') || str_starts_with($duongDan, 'https://')) {
        return $duongDan;
    }

    $duongDan = ltrim($duongDan, '/\\');
    if (str_starts_with($duongDan, './')) {
        $duongDan = substr($duongDan, 2);
    }

    if (str_starts_with($duongDan, 'images/') || str_starts_with($duongDan, 'uploads/')) {
        return $goc . $duongDan;
    }

    // Tương thích các bản ghi cũ chỉ lưu tên tệp trong uploads/
    return $goc . 'uploads/' . $duongDan;
}

// Trả phản hồi JSON chuẩn UTF-8 và mã HTTP tương ứng cho REST API
function traJson(mixed $duLieu, int $maHttp = 200): never {
    if (!headers_sent()) {
        http_response_code($maHttp);
        header('Content-Type: application/json; charset=utf-8');
    }
    echo json_encode($duLieu, JSON_UNESCAPED_UNICODE);
    exit;
}
