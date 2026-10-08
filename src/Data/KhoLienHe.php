<?php
// src/Data/KhoLienHe.php — Tệp xử lý lưu trữ liên hệ (Chương 6: thay bằng bảng lien_he MySQL)
// Phụ trách: Thịnh (Chức năng 4)
namespace App\Data;

class KhoLienHe
{
    public function __construct(private string $tep) {}

    // Thêm một liên hệ mới vào tệp .jsonl
    public function them(array $lh): void
    {
        $dong = json_encode($lh, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
        // Khóa tệp (LOCK_EX) khi ghi để tránh lỗi ghi đè nếu có nhiều người gửi cùng lúc
        file_put_contents($this->tep, $dong . PHP_EOL, FILE_APPEND | LOCK_EX);
    }

    // Đọc tất cả liên hệ, xếp mới nhất đứng trước
    public function tatCa(): array
    {
        if (!is_file($this->tep)) return [];
        $dong = file($this->tep, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $ds = array_map(fn($d) => json_decode($d, true), $dong);
        // array_filter('is_array') để loại bỏ những dòng không parse được thành mảng
        return array_reverse(array_filter($ds, 'is_array'));
    }
}

