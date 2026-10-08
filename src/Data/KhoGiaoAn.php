<?php
// src/Data/KhoGiaoAn.php — Nơi DUY NHẤT đọc tệp data/giao-an.json
// Phụ trách: Tấn (MSSV 3120124027) — Chương 6 chỉ cần viết lại lớp này bằng PDO
// Cách thử: $kho = new KhoGiaoAn(__DIR__ . '/../../data/giao-an.json'); var_dump($kho->tatCa());
namespace App\Data;

use App\Models\GiaoAn;
use RuntimeException;

class KhoGiaoAn
{
    private ?array $ds = null;  // mỗi request chỉ đọc tệp một lần

    public function __construct(private string $tepJson) {}

    /**
     * Trả về toàn bộ mảng GiaoAn từ JSON.
     */
    public function tatCa(): array
    {
        if ($this->ds === null) {
            if (!is_file($this->tepJson)) {
                throw new RuntimeException("Không tìm thấy tệp dữ liệu: {$this->tepJson}");
            }
            $json = file_get_contents($this->tepJson);
            $mang = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
            $this->ds = array_map(fn(array $d) => GiaoAn::tuMang($d), $mang);
        }
        return $this->ds;
    }

    /**
     * Tìm một giáo án theo id. Trả về null nếu không tìm thấy.
     */
    public function timTheoId(int $id): ?GiaoAn
    {
        foreach ($this->tatCa() as $ga) {
            if ($ga->id === $id) return $ga;
        }
        return null;
    }

    /**
     * Tìm kiếm và lọc danh sách giáo án — dùng cho trang kho-hoc-lieu.php.
     *
     * @param string $q       Từ khoá tìm kiếm (tên hoặc mô tả)
     * @param string $capHoc  'Tất cả' | 'THCS' | 'THPT'
     * @param string $sapXep  'ten-az' | 'gia-tang' | 'gia-giam'
     * @return GiaoAn[]
     */
    public function timKiem(string $q, string $capHoc, string $sapXep): array
    {
        // Danh sách giá trị cho phép — chặn dữ liệu không hợp lệ
        $capHocHopLe  = ['Tất cả', 'THCS', 'THPT'];
        $sapXepHopLe  = ['ten-az', 'gia-tang', 'gia-giam'];

        if (!in_array($capHoc, $capHocHopLe, true)) $capHoc = 'Tất cả';
        if (!in_array($sapXep, $sapXepHopLe, true)) $sapXep = 'ten-az';

        $q = mb_strtolower(trim($q), 'UTF-8');

        // Lọc theo từ khoá và cấp học
        $ketQua = array_filter($this->tatCa(), function (GiaoAn $ga) use ($q, $capHoc): bool {
            $khopCapHoc = ($capHoc === 'Tất cả') || ($ga->cap_hoc === $capHoc);
            if (!$khopCapHoc) return false;

            if ($q === '') return true;

            // Tìm trong tên hoặc mô tả (không phân biệt hoa thường)
            return mb_stripos($ga->ten, $q, 0, 'UTF-8') !== false
                || mb_stripos($ga->mo_ta, $q, 0, 'UTF-8') !== false;
        });

        // Sắp xếp
        $mang = array_values($ketQua);
        usort($mang, function (GiaoAn $a, GiaoAn $b) use ($sapXep): int {
            return match ($sapXep) {
                'ten-az'    => mb_strtolower($a->ten, 'UTF-8') <=> mb_strtolower($b->ten, 'UTF-8'),
                'gia-tang'  => $a->gia <=> $b->gia,
                'gia-giam'  => $b->gia <=> $a->gia,
                default     => 0,
            };
        });

        return $mang;
    }
}

