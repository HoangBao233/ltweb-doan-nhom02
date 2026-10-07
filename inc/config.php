<?php
/**
 * config.php — Cấu hình chung của website
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define('ROOT_DIR', __DIR__ . '/../');
define('DATA_DIR', ROOT_DIR . 'data/');
define('STORAGE_DIR', ROOT_DIR . 'storage/');
define('UPLOADS_DIR', ROOT_DIR . 'uploads/');
