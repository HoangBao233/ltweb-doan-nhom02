<?php
/**
 * inc/xac-thuc.php — Quản lý xác thực và phân quyền người dùng
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

    // Hỗ trợ kiểm thử hoặc tương thích phiên cũ
    if (!$uid && !empty($_SESSION['user'])) {
        if ($_SESSION['user'] === 'admin') {
            $uid = 1;
        } elseif ($_SESSION['user'] === 'tan' || $_SESSION['user'] === '3120124027') {
            $uid = 3;
        }
    }

    if (!$uid) {
        return null;
    }

    try {
        $st = db()->prepare('SELECT id, email, ho_ten, vai_tro FROM nguoi_dung WHERE id = ? LIMIT 1');
        $st->execute([$uid]);
        $u = $st->fetch();
        $nguoiDung = $u ?: null;
        return $nguoiDung;
    } catch (Throwable $e) {
        return null;
    }
}

function yeuCauQuyen(string $vaiTro): void {
    $u = nguoiDung();
    if (!$u || ($u['vai_tro'] ?? '') !== $vaiTro) {
        http_response_code(403);
        $duongDan403 = __DIR__ . '/../403.php';
        if (file_exists($duongDan403)) {
            require $duongDan403;
        } else {
            echo '403 Forbidden - Bạn không có quyền truy cập.';
        }
        exit;
    }
}

