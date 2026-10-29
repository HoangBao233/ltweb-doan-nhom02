<?php
/**
 * src/Data/KhoLienHe.php — Lớp truy xuất dữ liệu phản hồi, liên hệ qua PDO MySQL
 * 
 * Đồ án Lập trình Web - Nhóm 02
 * Phụ trách: Lê Hoàng Bảo (MSSV: 3120124003) — Phần A, Chức năng 1
 */

declare(strict_types=1);

namespace App\Data;

use PDO;

class KhoLienHe
{
    private PDO $db;

    public function __construct(?PDO $db = null)
    {
        $this->db = $db ?? db();
    }

    /**
     * Thêm một liên hệ mới vào bảng lien_he trong CSDL MySQL.
     * 
     * @param array $lh Mảng chứa: ho_ten, email, sdt, noi_dung, anh (tùy chọn)
     * @return int ID của liên hệ vừa được chèn
     */
    public function them(array $lh): int
    {
        $sql = "INSERT INTO lien_he (ho_ten, email, sdt, noi_dung, anh) 
                VALUES (:ho_ten, :email, :sdt, :noi_dung, :anh)";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':ho_ten'   => (string)($lh['ho_ten']   ?? $lh['hoten']   ?? ''),
            ':email'    => (string)($lh['email']    ?? ''),
            ':sdt'      => (string)($lh['sdt']      ?? ''),
            ':noi_dung' => (string)($lh['noi_dung'] ?? $lh['noidung'] ?? ''),
            ':anh'      => !empty($lh['anh']) ? (string)$lh['anh'] : null,
        ]);

        return (int)$this->db->lastInsertId();
    }

    /**
     * Lấy toàn bộ danh sách liên hệ, xếp liên hệ mới nhất lên đầu.
     * 
     * @param int $gioiHan Số dòng tối đa cần lấy (mặc định 100)
     * @return array Danh sách các mảng liên hệ
     */
    public function tatCa(int $gioiHan = 100): array
    {
        $sql = "SELECT id, ho_ten, email, sdt, noi_dung, anh, ngay_tao 
                FROM lien_he 
                ORDER BY ngay_tao DESC, id DESC 
                LIMIT ?";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(1, $gioiHan, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Đếm tổng số lượng phản hồi liên hệ hiện có trong CSDL.
     * 
     * @return int Tổng số dòng
     */
    public function dem(): int
    {
        $stmt = $this->db->query("SELECT COUNT(*) FROM lien_he");
        return (int)$stmt->fetchColumn();
    }
}
