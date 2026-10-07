<?php
/**
 * GiaoAn.php — Lớp thực thể (Model) đại diện cho một giáo án
 */
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
     * Tạo đối tượng GiaoAn từ mảng dữ liệu JSON
     */
    public static function tuMang(array $d): self
    {
        return new self(
            id:        (int)($d['id']        ?? 0),
            ten:       (string)($d['ten']       ?? ''),
            mo_ta:     (string)($d['mo_ta']     ?? ''),
            cap_hoc:   (string)($d['cap_hoc']   ?? ''),
            gia:       (int)($d['gia']        ?? 0),
            hinh_anh:  (string)($d['hinh_anh']  ?? ''),
            video_url: (string)($d['video_url'] ?? ''),
        );
    }
}
