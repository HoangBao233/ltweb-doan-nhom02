<?php
// src/Data/KhoGiaoAn.php — Nơi DUY NHẤT truy vấn bảng giao_an qua PDO
// Phụ trách: Tấn (MSSV 3120124027) — Bước 2 & Chức năng 2 Bảng 1
namespace App\Data;

use App\Models\GiaoAn;
use PDO;

class KhoGiaoAn
{
    public function __construct(private PDO|string|null $db = null)
    {
        if (!($this->db instanceof PDO)) {
            $this->db = db();
        }
    }

    /**
     * Trả về toàn bộ danh sách GiaoAn từ CSDL MySQL.
     * @return GiaoAn[]
     */
    public function tatCa(): array
    {
        $st = $this->db->query("SELECT * FROM giao_an ORDER BY id DESC");
        return array_map(fn(array $d) => GiaoAn::tuMang($d), $st->fetchAll());
    }

    /**
     * Tìm một giáo án theo id. Trả về null nếu không tìm thấy.
     */
    public function timTheoId(int $id): ?GiaoAn
    {
        $st = $this->db->prepare("SELECT * FROM giao_an WHERE id = ?");
        $st->execute([$id]);
        $d = $st->fetch();
        return $d ? GiaoAn::tuMang($d) : null;
    }

    /**
     * Bộ lọc của trang danh sách → [$where, $ts]. dem() và trang() cùng
     * gọi hàm này nên hai câu SQL luôn có đúng một điều kiện lọc.
     */
    private function dieuKien(array $loc): array
    {
        $dk = [];
        $ts = [];

        // Tìm theo từ khóa (tên giáo án): dùng addcslashes để escape ký tự đặc biệt % và _
        if (isset($loc['q']) && $loc['q'] !== '') {
            $dk[] = 'ten LIKE ?';
            $ts[] = '%' . addcslashes($loc['q'], '%_\\') . '%';
        }

        // Lọc theo danh mục
        if (!empty($loc['dm'])) {
            $dk[] = 'danh_muc_id = ?';
            $ts[] = (int) $loc['dm'];
        }

        // Lọc theo cấp học (nếu có lựa chọn khác 'Tất cả')
        if (!empty($loc['cap_hoc']) && $loc['cap_hoc'] !== 'Tất cả') {
            $dk[] = 'cap_hoc = ?';
            $ts[] = $loc['cap_hoc'];
        }

        // Lọc theo giá tối đa
        if (!empty($loc['giaToiDa'])) {
            $dk[] = 'gia <= ?';
            $ts[] = (int) $loc['giaToiDa'];
        }

        $where = $dk ? 'WHERE ' . implode(' AND ', $dk) : '';
        return [$where, $ts];
    }

    /**
     * Đếm tổng số dòng khớp với bộ lọc.
     */
    public function dem(array $loc): int
    {
        [$where, $ts] = $this->dieuKien($loc);
        $st = $this->db->prepare("SELECT COUNT(*) FROM giao_an $where");
        $st->execute($ts);
        return (int) $st->fetchColumn();
    }

    /**
     * Một trang danh sách GiaoAn theo bộ lọc, sắp xếp và phân trang.
     * @return GiaoAn[]
     */
    public function trang(array $loc, string $sx, int $soDong, int $boQua): array
    {
        [$where, $ts] = $this->dieuKien($loc);

        // Danh sách whitelist sắp xếp an toàn chống SQL Injection
        $orderBy = match ($sx) {
            'gia-tang' => 'gia ASC, id',
            'gia-giam' => 'gia DESC, id',
            'ten'      => 'ten ASC, id',
            default    => 'id DESC', // 'moi' hoặc giá trị lạ
        };

        $st = $this->db->prepare("SELECT * FROM giao_an $where ORDER BY $orderBy LIMIT ? OFFSET ?");
        $idx = 1;
        foreach ($ts as $v) {
            $st->bindValue($idx++, $v);
        }
        $st->bindValue($idx++, (int) $soDong, PDO::PARAM_INT);
        $st->bindValue($idx++, (int) $boQua, PDO::PARAM_INT);
        $st->execute();

        return array_map(fn(array $d) => GiaoAn::tuMang($d), $st->fetchAll());
    }

    /**
     * Giữ lại hàm timKiem để tương thích ngược nếu có tệp cũ gọi.
     * @return GiaoAn[]
     */
    public function timKiem(string $q, string $capHoc, string $sapXep): array
    {
        $loc = [
            'q'       => $q,
            'cap_hoc' => $capHoc,
        ];
        return $this->trang($loc, $sapXep, 100, 0);
    }
}
