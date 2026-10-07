<?php
/**
 * GiaoAn.php — Lớp thực thể (Model) đại diện cho một giáo án
 * Tương thích PHP 7.4 trở lên và PHP 8.x
 */
class GiaoAn
{
    public int $id;
    public string $ten;
    public string $mo_ta;
    public string $cap_hoc;
    public int $gia;
    public string $hinh_anh;
    public string $video_url;

    public function __construct(
        int $id = 0,
        string $ten = '',
        string $mo_ta = '',
        string $cap_hoc = '',
        int $gia = 0,
        string $hinh_anh = '',
        string $video_url = ''
    ) {
        $this->id        = $id;
        $this->ten       = $ten;
        $this->mo_ta     = $mo_ta;
        $this->cap_hoc   = $cap_hoc;
        $this->gia       = $gia;
        $this->hinh_anh  = $hinh_anh;
        $this->video_url = $video_url;
    }

    /**
     * Tạo đối tượng GiaoAn từ mảng dữ liệu JSON
     */
    public static function tuMang(array $d): self
    {
        return new self(
            (int)($d['id']        ?? 0),
            (string)($d['ten']       ?? ''),
            (string)($d['mo_ta']     ?? ''),
            (string)($d['cap_hoc']   ?? ''),
            (int)($d['gia']        ?? 0),
            (string)($d['hinh_anh']  ?? ''),
            (string)($d['video_url'] ?? '')
        );
    }
}
