-- database/schema.sql — Tạo lại CSDL từ đầu cho đồ án ITeduShare (Nhóm 02)
-- Đề tài: Nền tảng chia sẻ giáo án & học liệu Tin học
-- Học phần: Thiết kế và Lập trình Web — Khoa Toán - Tin, Trường ĐH Sư phạm - ĐH Đà Nẵng

SET NAMES utf8mb4;
DROP DATABASE IF EXISTS ltweb_nhom02;
CREATE DATABASE ltweb_nhom02
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;
USE ltweb_nhom02;

-- ============================================================================
-- 1. BẢNG DANH MỤC (danh_muc)
-- Phục vụ: Phân loại giáo án theo danh mục (THCS, THPT, Lập trình, Chuyên đề)
-- ============================================================================
CREATE TABLE danh_muc (
  id        INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  ten       VARCHAR(100) NOT NULL,
  slug      VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 2. BẢNG GIÁO ÁN (giao_an) — THỰC THỂ CHÍNH CỦA ĐỒ ÁN
-- Lưu trữ thông tin giáo án/học liệu điện tử.
-- Ràng buộc:
--   - Khóa ngoại danh_muc_id trỏ về danh_muc(id) (Cấm xóa danh mục còn giáo án -> lỗi 1451)
--   - CHECK (gia >= 0): Giá không được âm
--   - CHECK (ton_kho >= 0): Tồn kho (số lượt cấp phát/tải) không được âm
-- Chỉ mục:
--   - INDEX(danh_muc_id, gia): Tối ưu truy vấn lọc theo danh mục và sắp xếp theo giá
--   - INDEX(ten): Tối ưu truy vấn tìm kiếm theo tên
-- ============================================================================
CREATE TABLE giao_an (
  id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  danh_muc_id INT UNSIGNED NOT NULL,
  ten         VARCHAR(200) NOT NULL,
  gia         INT UNSIGNED NOT NULL DEFAULT 0 CHECK (gia >= 0),
  ton_kho     INT UNSIGNED NOT NULL DEFAULT 10 CHECK (ton_kho >= 0),
  hinh_anh    VARCHAR(255) NULL,
  mo_ta       TEXT NULL,
  cap_hoc     VARCHAR(50) NOT NULL DEFAULT 'THCS',
  video_url   VARCHAR(255) NULL,
  luot_tai    INT UNSIGNED NOT NULL DEFAULT 0,
  ngay_tao    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (danh_muc_id) REFERENCES danh_muc(id),
  INDEX idx_giao_an_dm_gia (danh_muc_id, gia),
  INDEX idx_giao_an_ten (ten)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 3. BẢNG NGƯỜI DÙNG (nguoi_dung)
-- Lưu trữ tài khoản đăng nhập, mật khẩu băm, vai trò phân quyền.
-- ============================================================================
CREATE TABLE nguoi_dung (
  id        INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  email     VARCHAR(191) NOT NULL UNIQUE,
  ho_ten    VARCHAR(100) NOT NULL,
  mat_khau  VARCHAR(255) NOT NULL,
  vai_tro   ENUM('quan_tri', 'khach_hang') NOT NULL DEFAULT 'khach_hang',
  ngay_tao  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 4. BẢNG ĐƠN HÀNG (don_hang)
-- Quản lý các giao dịch đặt mua/tải tài liệu của người dùng.
-- ============================================================================
CREATE TABLE don_hang (
  id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nguoi_dung_id INT UNSIGNED NOT NULL,
  tong_tien     INT UNSIGNED NOT NULL DEFAULT 0,
  trang_thai    ENUM('cho_duyet', 'da_giao', 'da_huy') NOT NULL DEFAULT 'cho_duyet',
  ghi_chu       VARCHAR(500) NULL,
  ngay_tao      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (nguoi_dung_id) REFERENCES nguoi_dung(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 5. BẢNG CHI TIẾT ĐƠN HÀNG (chi_tiet_don)
-- Bảng trung gian quan hệ nhiều - nhiều giữa don_hang và giao_an.
-- Khóa chính ghép: (don_hang_id, giao_an_id)
-- Ràng buộc:
--   - don_hang_id: ON DELETE CASCADE (xóa đơn hàng thì xóa chi tiết đơn)
--   - giao_an_id: KHÔNG có ON DELETE CASCADE (chặn xóa giáo án đã có trong đơn -> lỗi 1451)
--   - don_gia: Lưu đơn giá lúc đặt (phi chuẩn hóa có chủ đích để bảo toàn hóa đơn lịch sử)
-- ============================================================================
CREATE TABLE chi_tiet_don (
  don_hang_id INT UNSIGNED NOT NULL,
  giao_an_id  INT UNSIGNED NOT NULL,
  so_luong    SMALLINT UNSIGNED NOT NULL DEFAULT 1 CHECK (so_luong > 0),
  don_gia     INT UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (don_hang_id, giao_an_id),
  FOREIGN KEY (don_hang_id) REFERENCES don_hang(id) ON DELETE CASCADE,
  FOREIGN KEY (giao_an_id) REFERENCES giao_an(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 6. BẢNG LIÊN HỆ (lien_he)
-- Lưu trữ các phản hồi, liên hệ từ form ve-chung-toi.php (thay thế tệp JSONL cũ).
-- ============================================================================
CREATE TABLE lien_he (
  id       INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  ho_ten   VARCHAR(100) NOT NULL,
  email    VARCHAR(191) NOT NULL,
  sdt      VARCHAR(20) NOT NULL,
  noi_dung TEXT NOT NULL,
  anh      VARCHAR(255) NULL,
  ngay_tao DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 7. BẢNG THÀNH VIÊN (thanh_vien)
-- Lưu danh sách 4 thành viên nhóm 02, liên kết tài khoản nguoi_dung_id để phân quyền
-- ============================================================================
CREATE TABLE thanh_vien (
  mssv          CHAR(10) PRIMARY KEY,
  ho_ten        VARCHAR(100) NOT NULL,
  nguoi_dung_id INT UNSIGNED NULL UNIQUE,
  FOREIGN KEY (nguoi_dung_id) REFERENCES nguoi_dung(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 8. CÁC BẢNG RIÊNG CHO TRANG CÁ NHÂN PHẦN B (Tên bắt đầu bằng tv_)
-- Mỗi thành viên một bảng riêng, có khóa ngoại mssv trỏ về thanh_vien(mssv)
-- ============================================================================

-- Bảng riêng của Lê Hoàng Bảo (3120124003): Sổ lưu bút có phân trang, chủ trang được xóa
CREATE TABLE tv_luu_but (
  id        INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  mssv      CHAR(10) NOT NULL,
  ten_khach VARCHAR(50) NOT NULL,
  loi_nhan  VARCHAR(500) NOT NULL,
  thoi_gian DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (mssv) REFERENCES thanh_vien(mssv) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng riêng của Nguyễn Cảnh Tấn (3120124027): Bộ đếm lượt xem theo ngày (ON DUPLICATE KEY UPDATE)
CREATE TABLE tv_luot_xem (
  id        INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  mssv      CHAR(10) NOT NULL,
  ngay      DATE NOT NULL,
  so_luot   INT UNSIGNED NOT NULL DEFAULT 1,
  UNIQUE KEY uk_tv_luot_xem (mssv, ngay),
  FOREIGN KEY (mssv) REFERENCES thanh_vien(mssv) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng riêng của Lê Thị Xuân Châu (3120124006): Quản lý danh sách dự án cá nhân
-- Bảng riêng của Nguyễn Cảnh Tấn (3120124027): Dự án cá nhân và học liệu tiêu biểu bi?u
CREATE TABLE tv_du_an_tan (
  id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  mssv          CHAR(10) NOT NULL,
  ten_du_an     VARCHAR(150) NOT NULL,
  cong_nghe     VARCHAR(100) NOT NULL,
  nam_thuc_hien SMALLINT UNSIGNED NOT NULL,
  mo_ta         TEXT NULL,
  luot_tai      INT UNSIGNED NOT NULL DEFAULT 0,
  FOREIGN KEY (mssv) REFERENCES thanh_vien(mssv) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE tv_du_an (
  id        INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  mssv      CHAR(10) NOT NULL,
  ten_du_an VARCHAR(200) NOT NULL,
  mo_ta     VARCHAR(500) NULL,
  lien_ket  VARCHAR(255) NULL,
  ngay_tao  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (mssv) REFERENCES thanh_vien(mssv) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng riêng của Nguyễn Tiến Thịnh (3120222126): Quản lý yêu cầu nhận CV qua email
CREATE TABLE tv_yeu_cau_cv (
  id        INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  mssv      CHAR(10) NOT NULL,
  email     VARCHAR(191) NOT NULL,
  loi_nhan  VARCHAR(500) NULL,
  ip_gui    VARCHAR(45) NULL,
  ngay_tao  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (mssv) REFERENCES thanh_vien(mssv) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

