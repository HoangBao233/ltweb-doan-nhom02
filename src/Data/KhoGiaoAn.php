<?php
/**
 * KhoGiaoAn.php — Lớp truy cập dữ liệu giáo án từ giao-an.json
 */
require_once dirname(__DIR__) . '/Models/GiaoAn.php';

class KhoGiaoAn
{
    private string $duong_dan_file;
    /** @var GiaoAn[] */
    private ?array $cache = null;

    public function __construct(string $duong_dan_file = '')
    {
        $this->duong_dan_file = $duong_dan_file ?: DATA_DIR . 'giao-an.json';
    }

    /**
     * Đọc toàn bộ giáo án từ JSON, cache lại trong request
     * @return GiaoAn[]
     */
    public function tatCa(): array
    {
        if ($this->cache !== null) return $this->cache;

        $noi_dung = file_get_contents($this->duong_dan_file);
        if ($noi_dung === false) return $this->cache = [];

        $mang    = json_decode($noi_dung, true);
        if (!is_array($mang)) return $this->cache = [];

        $this->cache = array_map([GiaoAn::class, 'tuMang'], $mang);
        return $this->cache;
    }

    /**
     * Tìm một giáo án theo id, trả null nếu không có
     */
    public function timTheoId(int $id): ?GiaoAn
    {
        foreach ($this->tatCa() as $ga) {
            if ($ga->id === $id) return $ga;
        }
        return null;
    }

    /**
     * Lọc và sắp xếp danh sách giáo án
     *
     * @param string $capHoc   '' | 'THCS' | 'THPT'
     * @param string $sapXep   'macdinh' | 'ten-az' | 'gia-tang' | 'gia-giam'
     * @param string $timKiem  chuỗi tìm kiếm (tên)
     * @return GiaoAn[]
     */
    public function locVaSapXep(
        string $capHoc  = '',
        string $sapXep  = 'macdinh',
        string $timKiem = ''
    ): array {
        $ds = $this->tatCa();

        // Lọc cấp học
        if ($capHoc !== '' && $capHoc !== 'Tất cả') {
            $ds = array_filter($ds, fn($ga) => $ga->cap_hoc === $capHoc);
        }

        // Tìm kiếm theo tên
        if ($timKiem !== '') {
            $kw = mb_strtolower($timKiem, 'UTF-8');
            $ds = array_filter($ds, fn($ga) =>
                str_contains(mb_strtolower($ga->ten, 'UTF-8'), $kw) ||
                str_contains(mb_strtolower($ga->mo_ta, 'UTF-8'), $kw)
            );
        }

        // Sắp xếp
        $ds = array_values($ds);
        match ($sapXep) {
            'ten-az'    => usort($ds, fn($a, $b) => strcmp($a->ten, $b->ten)),
            'gia-tang'  => usort($ds, fn($a, $b) => $a->gia <=> $b->gia),
            'gia-giam'  => usort($ds, fn($a, $b) => $b->gia <=> $a->gia),
            default     => null, // giữ thứ tự gốc
        };

        return $ds;
    }

    /**
     * Trả về danh sách giáo án tương ứng với mảng id
     * @param int[] $ids
     * @return GiaoAn[]
     */
    public function layNhieuTheoId(array $ids): array
    {
        $tat_ca = $this->tatCa();
        return array_values(array_filter($tat_ca, fn($ga) => in_array($ga->id, $ids, true)));
    }
}
