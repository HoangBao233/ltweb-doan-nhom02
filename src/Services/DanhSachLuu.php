<?php
// src/Services/DanhSachLuu.php — Xử lý danh sách giáo án yêu thích lưu trong Session
// Phụ trách: Bảo + Thịnh (Chức năng 5)
namespace App\Services;

class DanhSachLuu
{
    public function __construct()
    {
        // Khởi tạo mảng trống nếu chưa có session
        if (!isset($_SESSION['danh_sach_luu'])) {
            $_SESSION['danh_sach_luu'] = [];
        }
    }

    // Thêm một giáo án vào danh sách lưu (cộng dồn số lượng)
    public function them(int $id, int $sl = 1): void
    {
        $hienTai = (int)($_SESSION['danh_sach_luu'][$id] ?? 0);
        $_SESSION['danh_sach_luu'][$id] = $hienTai + max(1, $sl);
    }

    // Cập nhật số lượng của một giáo án trong danh sách lưu
    public function capNhat(int $id, int $sl): void
    {
        if ($sl <= 0) {
            unset($_SESSION['danh_sach_luu'][$id]);
        } else {
            $_SESSION['danh_sach_luu'][$id] = $sl;
        }
    }

    // Lấy số lượng của một giáo án trong giỏ
    public function soLuong(int $id): int
    {
        return (int)($_SESSION['danh_sach_luu'][$id] ?? 0);
    }

    // Xoá một giáo án khỏi danh sách
    public function xoa(int $id): void
    {
        unset($_SESSION['danh_sach_luu'][$id]);
    }

    // Xoá toàn bộ danh sách
    public function xoaHet(): void
    {
        $_SESSION['danh_sach_luu'] = [];
    }

    // Đếm số lượng giáo án đang lưu để hiển thị trên Header
    public function soMon(): int
    {
        return count($_SESSION['danh_sach_luu']);
    }

    // Trả về mảng các ID đã lưu
    public function tatCa(): array
    {
        return array_keys($_SESSION['danh_sach_luu']);
    }

    // Kiểm tra xem một ID đã được lưu hay chưa
    public function daLuu(int $id): bool
    {
        return isset($_SESSION['danh_sach_luu'][$id]);
    }
}

