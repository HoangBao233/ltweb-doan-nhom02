<?php
/**
 * inc/xac-thuc.php — Quản lý xác thực và phân quyền người dùng
 * Đồ án Lập trình Web - Nhóm 02
 * Phụ trách: Nguyễn Tiến Thịnh (MSSV: 3120222126) — Chức năng 4 & Bảo mật Chương 7
 */

declare(strict_types=1);

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/ham.php';

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
 * - Nếu đã đăng nhập nhưng sai vai trò: ghi log 'cam_truy_cap', trả mã HTTP 403 và nạp trang 403.php.
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
        ghiLogBaoMat('cam_truy_cap', [
            'trang' => $_SERVER['REQUEST_URI'] ?? '-',
            'vai_tro_can' => $vaiTro,
            'vai_tro_hien_tai' => $u['vai_tro'] ?? 'khong_ro'
        ]);
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

/**
 * Chuẩn hóa đăng nhập người dùng (Chương 7, Biện pháp 2)
 * Gọi session_regenerate_id(true) chống Session Fixation, lưu uid và timestamp hoạt động.
 */
function dangNhap(int $uid): void
{
    session_regenerate_id(true);
    $_SESSION['uid'] = $uid;
    $_SESSION['hoat_dong'] = time();
}

/**
 * Chuẩn hóa đăng xuất an toàn (Chương 7, Biện pháp 2)
 * Xóa mảng session, hủy cookie phiên trên trình duyệt và hủy session phía máy chủ.
 */
function dangXuat(): void
{
    $_SESSION = [];
    setcookie(session_name(), '', [
        'expires'  => time() - 3600,
        'path'     => '/',
        'secure'   => !empty($_SERVER['HTTPS']),
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_destroy();
    }
}

/**
 * Đếm số lần đăng nhập sai của email trong vòng 15 phút gần nhất (Chương 7, Biện pháp 7)
 */
function soLanSai(string $email): int
{
    $st = db()->prepare(
        'SELECT COUNT(*) FROM dang_nhap_sai WHERE email = ? AND thoi_gian > NOW() - INTERVAL 15 MINUTE'
    );
    $st->execute([$email]);
    return (int)$st->fetchColumn();
}

/**
 * Ghi nhận một lần đăng nhập sai vào CSDL và nhật ký bảo mật (Chương 7, Biện pháp 7)
 */
function ghiLanSai(string $email): void
{
    $ip = $_SERVER['REMOTE_ADDR'] ?? '-';
    $st = db()->prepare('INSERT INTO dang_nhap_sai (email, ip) VALUES (?, ?)');
    $st->execute([$email, $ip]);
    ghiLogBaoMat('dang_nhap_sai', ['email' => $email]);
}
