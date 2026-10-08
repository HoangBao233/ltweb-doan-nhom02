<?php
// src/Models/GiaoAn.php — Lớp thực thể Giáo Án, ánh xạ từ data/giao-an.json
// Phụ trách: Tấn (MSSV 3120124027)
// Cách thử: new GiaoAn(1, 'Test', ...) hoặc GiaoAn::tuMang($mangJson)
namespace App\Models;

class GiaoAn
{
    public function __construct(
        public readonly int    $id,
        public readonly string $ten,
        public readonly string $mo_ta,
        public readonly string $cap_hoc,
        public readonly int    $gia,
        public readonly string $hinh_anh,
        public readonly string $video_url,
    ) {}

    /**
     * Tạo đối tượng GiaoAn từ một mục của mảng JSON.
     * Dùng ở KhoGiaoAn khi đọc tệp giao-an.json.
     */
    public static function tuMang(array $d): self
    {
        return new self(
            id:        (int)($d['id']        ?? 0),
            ten:       (string)($d['ten']     ?? ''),
            mo_ta:     (string)($d['mo_ta']   ?? ''),
            cap_hoc:   (string)($d['cap_hoc'] ?? ''),
            gia:       (int)($d['gia']        ?? 0),
            hinh_anh:  (string)($d['hinh_anh'] ?? ''),
            video_url: (string)($d['video_url'] ?? ''),
        );
    }
}

