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

    // Thêm một giáo án vào danh sách lưu
    public function them(int $id): void
    {
        // Lưu id làm key, giá trị 1 để đánh dấu là đã lưu (giống số lượng trong giỏ hàng)
        $_SESSION['danh_sach_luu'][$id] = 1;
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

