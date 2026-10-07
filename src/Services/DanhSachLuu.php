<?php
/**
 * DanhSachLuu.php — Quản lý danh sách giáo án đã lưu (tương đương giỏ hàng)
 * Dữ liệu lưu trong $_SESSION['gio'] là mảng các id giáo án
 */
class DanhSachLuu
{
    private const KHOA = 'gio';

    private static function &layGio(): array
    {
        if (!isset($_SESSION[self::KHOA]) || !is_array($_SESSION[self::KHOA])) {
            $_SESSION[self::KHOA] = [];
        }
        return $_SESSION[self::KHOA];
    }

    /**
     * Thêm giáo án vào danh sách lưu
     */
    public static function them(int $id): void
    {
        $gio = &self::layGio();
        if (!in_array($id, $gio, true)) {
            $gio[] = $id;
        }
    }

    /**
     * Xoá một giáo án khỏi danh sách lưu
     */
    public static function xoa(int $id): void
    {
        $gio = &self::layGio();
        $gio = array_values(array_filter($gio, fn($i) => $i !== $id));
        $_SESSION[self::KHOA] = $gio;
    }

    /**
     * Xoá toàn bộ danh sách
     */
    public static function xoaToanBo(): void
    {
        $_SESSION[self::KHOA] = [];
    }

    /**
     * Trả về mảng id đã lưu
     * @return int[]
     */
    public static function tatCa(): array
    {
        return self::layGio();
    }

    /**
     * Số lượng giáo án đã lưu
     */
    public static function soLuong(): int
    {
        return count(self::layGio());
    }

    /**
     * Kiểm tra giáo án có trong danh sách chưa
     */
    public static function daLuu(int $id): bool
    {
        return in_array($id, self::layGio(), true);
    }
}
