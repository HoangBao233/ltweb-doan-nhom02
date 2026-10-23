<?php
/**
 * inc/db.php — Khởi tạo kết nối CSDL MySQL qua PDO (Mẫu thiết kế Singleton)
 * 
 * Đồ án Lập trình Web - Nhóm 02
 * Phụ trách: Lê Hoàng Bảo (MSSV: 3120124003)
 */

declare(strict_types=1);

/**
 * Trả về đối tượng PDO duy nhất (Singleton) trong suốt vòng đời của một request.
 * 
 * - Kết nối CSDL: ltweb_nhom02
 * - Tài khoản: root, Mật khẩu: rỗng (chuẩn XAMPP)
 * - ERRMODE: ERRMODE_EXCEPTION (Bật ngoại lệ khi truy vấn lỗi)
 * - DEFAULT_FETCH_MODE: FETCH_ASSOC (Trả về mảng kết hợp theo tên cột)
 * - EMULATE_PREPARES: false (Sử dụng Prepared Statement thực sự của MySQL)
 * 
 * @return PDO
 * @throws PDOException Nếu kết nối CSDL thất bại
 */
function db(): PDO {
    static $pdo = null;

    if ($pdo === null) {
        $host    = 'localhost';
        $port    = '3306';
        $dbname  = 'ltweb_nhom02';
        $charset = 'utf8mb4';
        $user    = 'root';
        $pass    = '';

        $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset={$charset}";

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        $pdo = new PDO($dsn, $user, $pass, $options);
    }

    return $pdo;
}

