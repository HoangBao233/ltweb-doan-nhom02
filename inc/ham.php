<?php
/**
 * ham.php — Các hàm tiện ích đơn giản
 */

// Làm sạch tham số chuỗi — tránh lỗi TypeError khi người dùng gửi mảng ?q[]=... (Chương 7)
function chuoi(mixed $v): string
{
    return is_string($v) ? $v : '';
}

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

/**
 * Ghi sự kiện bảo mật vào logs/bao-mat.log (Chương 7, Biện pháp 7)
 * Phụ trách: Nguyễn Tiến Thịnh (MSSV: 3120222126)
 * Mỗi sự kiện được ghi trên một dòng kèm timestamp và JSON ngữ cảnh.
 * TUYỆT ĐỐI KHÔNG ghi mật khẩu, PHPSESSID hoặc mã CSRF vào log!
 */
function ghiLogBaoMat(string $suKien, array $chiTiet = []): void
{
    $chiTiet += [
        'ip'  => $_SERVER['REMOTE_ADDR'] ?? '-',
        'uid' => $_SESSION['uid'] ?? null,
    ];
    $json = json_encode($chiTiet, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    $dong = date('Y-m-d H:i:s') . " {$suKien} {$json}";
    $thuMucLog = __DIR__ . '/../logs';
    if (!is_dir($thuMucLog)) {
        mkdir($thuMucLog, 0755, true);
    }
    error_log($dong . PHP_EOL, 3, $thuMucLog . '/bao-mat.log');
}
