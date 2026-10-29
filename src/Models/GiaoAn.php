<?php
// src/Models/GiaoAn.php — Lớp thực thể Giáo Án
// Nhóm 02 — ITeduShare
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
        public readonly int    $danh_muc_id = 0,
        public readonly int    $ton_kho = 0,
        public readonly int    $luot_tai = 0,
    ) {}

    /**
     * Tạo đối tượng GiaoAn từ một dòng mảng dữ liệu CSDL MySQL.
     */
    public static function tuMang(array $d): self
    {
        return new self(
            id:          (int)($d['id']          ?? 0),
            ten:         (string)($d['ten']       ?? ''),
            mo_ta:       (string)($d['mo_ta']     ?? ''),
            cap_hoc:     (string)($d['cap_hoc']   ?? ''),
            gia:         (int)($d['gia']          ?? 0),
            hinh_anh:    (string)($d['hinh_anh']  ?? ''),
            video_url:   (string)($d['video_url'] ?? ''),
            danh_muc_id: (int)($d['danh_muc_id'] ?? 0),
            ton_kho:     (int)($d['ton_kho']     ?? 0),
            luot_tai:    (int)($d['luot_tai']    ?? 0),
        );
    }
}
