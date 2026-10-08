<?php
// vendor/autoload.php — autoloader thủ công (thay thế Composer khi chưa cài)
// Khi Composer được cài: chạy "composer install" để thay file này bằng bản chuẩn
spl_autoload_register(function (string $class): void {
    // App\Models\GiaoAn  →  src/Models/GiaoAn.php
    $path = __DIR__ . '/../' . str_replace(['App\\', '\\'], ['src/', '/'], $class) . '.php';
    if (is_file($path)) {
        require_once $path;
    }
});

