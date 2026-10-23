<?php
/**
 * inc/db.php — Khởi tạo kết nối PDO MySQL cho dự án ITeduShare (Nhóm 02)
 */

function db(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $host    = '127.0.0.1';
        $port    = '3306';
        $dbname  = 'ltweb_nhom02';
        $charset = 'utf8mb4';
        $dsn     = "mysql:host={$host};port={$port};dbname={$dbname};charset={$charset}";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        $pdo = new PDO($dsn, 'root', '', $options);
    }
    return $pdo;
}

