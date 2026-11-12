-- database/tai-khoan.sql — Chạy MỘT lần bằng tài khoản root (phpMyAdmin hoặc mysql CLI)
-- Học phần: Thiết kế và Lập trình Web — Chương 7: Bảo mật ứng dụng web
-- Dự án: ITeduShare (Nhóm 02)
-- Phụ trách: Nguyễn Tiến Thịnh (MSSV: 3120222126) — Biện pháp 6: Tài khoản CSDL quyền tối thiểu
--
-- Mục đích: Tạo tài khoản ứng dụng 'nhom02_app' chỉ có quyền thao tác dữ liệu
-- (SELECT, INSERT, UPDATE, DELETE) trên CSDL ltweb_nhom02.*, không có quyền DDL (DROP, ALTER, CREATE)
-- hay quyền quản trị hệ thống (GRANT, SHUTDOWN).

CREATE USER IF NOT EXISTS 'nhom02_app'@'localhost'
  IDENTIFIED BY 'thay-bang-mat-khau-dai-ngau-nhien';

GRANT SELECT, INSERT, UPDATE, DELETE
  ON ltweb_nhom02.* TO 'nhom02_app'@'localhost';

FLUSH PRIVILEGES;

