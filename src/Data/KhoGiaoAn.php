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

    /**
     * L?y danh s�ch gi�o �n m?i nh?t (ph?c v? Trang ch?).
     * Ph? tr�ch: B?o (Ch?c nang 1)
     * 
     * @param int $gioiHan S? b�i vi?t m?i nh?t c?n l?y (m?c d?nh 8)
     * @return GiaoAn[]
     */
    public function moiNhat(int $gioiHan = 8): array
    {
        $st = $this->db->prepare("SELECT * FROM giao_an ORDER BY id DESC LIMIT ?");
        $st->bindValue(1, $gioiHan, PDO::PARAM_INT);
        $st->execute();
        return array_map(fn(array $d) => GiaoAn::tuMang($d), $st->fetchAll());
    }

    /**
     * L?y danh s�ch gi�o �n theo m?ng ID (ph?c v? Gi? h�ng & L?ch s? xem).
     * Gi?i quy?t tri?t d? l?i N+1 truy v?n b?ng 1 c�u l?nh SQL duy nh?t: WHERE id IN (?, ?, ...).
     * Ph? tr�ch: B?o (Ch?c nang 1)
     * 
     * @param int[] $ids M?ng ch?a c�c ID c?n truy v?n
     * @return GiaoAn[]
     */
    public function theoDanhSachId(array $ids): array
    {
        $ids = array_values(array_filter(array_map('intval', $ids), fn($id) => $id > 0));
        if (empty($ids)) {
            return [];
        }

        $dauHoi = implode(',', array_fill(0, count($ids), '?'));
        $st = $this->db->prepare("SELECT * FROM giao_an WHERE id IN ($dauHoi)");
        $st->execute($ids);

        return array_map(fn(array $d) => GiaoAn::tuMang($d), $st->fetchAll());
    }

    /**
     * L?y c�c gi�o �n li�n quan c�ng danh m?c, tr? gi�o �n dang xem (ph?c v? Trang chi ti?t).
     * Ph? tr�ch: B?o (Ch?c nang 1)
     * 
     * @param int $danhMucId ID danh m?c
     * @param int $truId ID gi�o �n c?n lo?i tr? (gi�o �n hi?n t?i)
     * @param int $gioiHan S? b�i li�n quan t?i da (m?c d?nh 4)
     * @return GiaoAn[]
     */
    public function cungDanhMuc(int $danhMucId, int $truId, int $gioiHan = 4): array
    {
        $st = $this->db->prepare("SELECT * FROM giao_an WHERE danh_muc_id = ? AND id != ? ORDER BY id DESC LIMIT ?");
        $st->bindValue(1, $danhMucId, PDO::PARAM_INT);
        $st->bindValue(2, $truId, PDO::PARAM_INT);
        $st->bindValue(3, $gioiHan, PDO::PARAM_INT);
        $st->execute();

        return array_map(fn(array $d) => GiaoAn::tuMang($d), $st->fetchAll());
    }
}