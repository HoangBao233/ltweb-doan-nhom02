<?php
// src/Models/GiaoAn.php — Lớp thực thể Giáo Án
// Nhóm 02 — ITeduShare
namespace App\Models;

class GiaoAn implements \JsonSerializable
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
        public readonly string $ten_danh_muc = '',
    ) {}

    /**
     * Tạo đối tượng GiaoAn từ một dòng mảng dữ liệu CSDL MySQL.
     */
    public static function tuMang(array $d): self
    {
        return new self(
            id:           (int)($d['id']           ?? 0),
            ten:          (string)($d['ten']        ?? ''),
            mo_ta:        (string)($d['mo_ta']      ?? ''),
            cap_hoc:      (string)($d['cap_hoc']    ?? ''),
            gia:          (int)($d['gia']           ?? 0),
            hinh_anh:     (string)($d['hinh_anh']   ?? ''),
            video_url:    (string)($d['video_url']  ?? ''),
            danh_muc_id:  (int)($d['danh_muc_id']  ?? 0),
            ton_kho:      (int)($d['ton_kho']      ?? 0),
            luot_tai:     (int)($d['luot_tai']     ?? 0),
            ten_danh_muc: (string)($d['ten_danh_muc'] ?? ''),
        );
    }

    /**
     * Chỉ xuất các trường công khai khi chuyển sang JSON cho REST API.
     */
    public function jsonSerialize(): array
    {
        $duongDanAnh = function_exists('anhGiaoAn')
            ? anhGiaoAn($this->hinh_anh)
            : ($this->hinh_anh !== '' ? $this->hinh_anh : 'images/banner-trang-chu.jpg');

        return [
            'id'           => $this->id,
            'danh_muc_id'  => $this->danh_muc_id,
            'ten'          => $this->ten,
            'mo_ta'        => $this->mo_ta,
            'cap_hoc'      => $this->cap_hoc,
            'gia'          => $this->gia,
            'ton_kho'      => $this->ton_kho,
            'hinh_anh'     => $duongDanAnh,
            'video_url'    => $this->video_url,
            'luot_tai'     => $this->luot_tai,
            'ten_danh_muc' => $this->ten_danh_muc,
        ];
    }
}
