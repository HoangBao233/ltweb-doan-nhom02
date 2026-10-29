<?php
/**
 * inc/xac-thuc.php — Quản lý xác thực và phân quyền người dùng
 * Đồ án Lập trình Web - Nhóm 02
 * Phụ trách: Nguyễn Tiến Thịnh (MSSV: 3120222126) — Chức năng 4
 */

declare(strict_types=1);

require_once __DIR__ . '/db.php';

/**
 * Trả về thông tin người dùng đang đăng nhập từ CSDL MySQL (qua PDO).
 * Trong Session CHỈ LƯU $_SESSION['uid'] (ngăn ngừa Privilege Escalation và đảm bảo cập nhật quyền từ CSDL).
 *
 * @return array{id: int, email: string, ho_ten: string, vai_tro: string, ngay_tao: string}|null
 */
function nguoiDung(): ?array {
    static $nguoiDung = null;
    if ($nguoiDung !== null) {
        return $nguoiDung;
    }

    $uid = $_SESSION['uid'] ?? null;
    if (!$uid && !empty($_SESSION['user_id'])) {
        $uid = (int)$_SESSION['user_id'];
    }

    // Hỗ trợ tương thích ngược nếu còn phiên làm việc cũ
    if (!$uid && !empty($_SESSION['user'])) {
        if ($_SESSION['user'] === 'admin') {
            $uid = 1;
        } elseif ($_SESSION['user'] === 'lan') {
            $uid = 2;
        } elseif ($_SESSION['user'] === 'an') {
            $uid = 3;
        } elseif ($_SESSION['user'] === 'bao' || $_SESSION['user'] === '3120124003') {
            $uid = 4;
        } elseif ($_SESSION['user'] === 'tan' || $_SESSION['user'] === '3120124027') {
            $uid = 5;
        } elseif ($_SESSION['user'] === 'chau' || $_SESSION['user'] === '3120124006') {
            $uid = 6;
        } elseif ($_SESSION['user'] === 'thinh' || $_SESSION['user'] === '3120222126') {
            $uid = 7;
        }
    }

    if (!$uid) {
        return null;
    }

    try {
        $st = db()->prepare('SELECT id, email, ho_ten, vai_tro, ngay_tao FROM nguoi_dung WHERE id = ? LIMIT 1');
        $st->execute([(int)$uid]);
        $u = $st->fetch();
        $nguoiDung = $u ?: null;
        return $nguoiDung;
    } catch (Throwable $e) {
        error_log('Lỗi nguoiDung(): ' . $e->getMessage());
        return null;
    }
}

/**
 * Kiểm tra xem người dùng hiện tại đã đăng nhập hay chưa.
 */
function daDangNhap(): bool {
    return nguoiDung() !== null;
}

/**
 * Bắt buộc phải đăng nhập. Nếu chưa đăng nhập, chuyển hướng sang trang đăng nhập.
 */
function yeuCauDangNhap(): void {
    if (!daDangNhap()) {
        global $goc;
        $duongDanGoc = $goc ?? '';
        header('Location: ' . $duongDanGoc . 'dang-nhap.php');
        exit;
    }
}

/**
 * Yêu cầu quyền truy cập cụ thể (ví dụ: 'quan_tri').
 * - Nếu chưa đăng nhập: chuyển hướng sang trang đăng nhập.
 * - Nếu đã đăng nhập nhưng sai vai trò: trả mã HTTP 403 và nạp trang 403.php.
 */
function yeuCauQuyen(string $vaiTro): void {
    if (!daDangNhap()) {
        global $goc;
        $duongDanGoc = $goc ?? '';
        header('Location: ' . $duongDanGoc . 'dang-nhap.php');
        exit;
    }

    $u = nguoiDung();
    if (($u['vai_tro'] ?? '') !== $vaiTro) {
        http_response_code(403);
        $duongDan403 = __DIR__ . '/../403.php';
        if (file_exists($duongDan403)) {
            require $duongDan403;
        } else {
            echo '403 Forbidden - Bạn không có quyền truy cập chức năng này.';
        }
        exit;
    }
}
