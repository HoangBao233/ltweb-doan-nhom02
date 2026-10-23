-- database/schema.sql — Lược đồ CSDL cho dự án ITeduShare (Nhóm 02)
-- Chạy lại được nhiều lần mà không bị lỗi trùng lặp

SET NAMES utf8mb4;
DROP DATABASE IF EXISTS ltweb_nhom02;
CREATE DATABASE ltweb_nhom02 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE ltweb_nhom02;

-- 1. Bảng danh_muc: các cấp học và phân loại học liệu
CREATE TABLE danh_muc (
  id   INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  ten  VARCHAR(100) NOT NULL,
  slug VARCHAR(100) NOT NULL UNIQUE
);

-- 2. Bảng nguoi_dung: tài khoản người dùng và quản trị viên
CREATE TABLE nguoi_dung (
  id        INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  email     VARCHAR(100) NOT NULL UNIQUE,
  ho_ten    VARCHAR(100) NOT NULL,
  mat_khau  VARCHAR(255) NOT NULL,
  vai_tro   ENUM('quan_tri', 'khach_hang') NOT NULL DEFAULT 'khach_hang',
  ngay_tao  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- 3. Bảng giao_an (thực thể chính): giáo án và học liệu chia sẻ
CREATE TABLE giao_an (
  id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  danh_muc_id INT UNSIGNED NOT NULL,
  ten         VARCHAR(200) NOT NULL,
  cap_hoc     VARCHAR(50) NOT NULL DEFAULT 'THPT',
  gia         INT UNSIGNED NOT NULL DEFAULT 0,
  ton_kho     INT UNSIGNED NOT NULL DEFAULT 99,
  hinh_anh    VARCHAR(255) NULL,
  video_url   VARCHAR(255) NULL,
  mo_ta       TEXT NULL,
  luot_tai    INT UNSIGNED NOT NULL DEFAULT 0,
  ngay_tao    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (danh_muc_id) REFERENCES danh_muc(id)
);

-- 4. Bảng don_hang: đơn tải/mua tài liệu
CREATE TABLE don_hang (
  id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nguoi_dung_id INT UNSIGNED NOT NULL,
  tong_tien     INT UNSIGNED NOT NULL DEFAULT 0,
  trang_thai    ENUM('cho_xu_ly', 'da_thanh_toan', 'huy') NOT NULL DEFAULT 'cho_xu_ly',
  ngay_tao      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (nguoi_dung_id) REFERENCES nguoi_dung(id) ON DELETE CASCADE
);

-- 5. Bảng chi_tiet_don: chi tiết từng tài liệu trong đơn (quan hệ n - n, khóa chính ghép)
CREATE TABLE chi_tiet_don (
  don_hang_id INT UNSIGNED NOT NULL,
  giao_an_id  INT UNSIGNED NOT NULL,
  so_luong    SMALLINT UNSIGNED NOT NULL DEFAULT 1,
  don_gia     INT UNSIGNED NOT NULL,
  PRIMARY KEY (don_hang_id, giao_an_id),
  FOREIGN KEY (don_hang_id) REFERENCES don_hang(id) ON DELETE CASCADE,
  FOREIGN KEY (giao_an_id) REFERENCES giao_an(id)
);

-- 6. Bảng lien_he: thông tin phản hồi từ người dùng
CREATE TABLE lien_he (
  id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  ho_ten       VARCHAR(100) NOT NULL,
  email        VARCHAR(100) NOT NULL,
  chu_de       VARCHAR(200) NOT NULL,
  noi_dung     TEXT NOT NULL,
  tep_dinh_kem VARCHAR(255) NULL,
  ngay_gui     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- ============================================================================
-- PHẦN B: BẢNG DÀNH CHO THÀNH VIÊN NHÓM
-- ============================================================================

-- Bảng thanh_vien: thông tin từng thành viên trong Nhóm 02
CREATE TABLE thanh_vien (
  mssv          CHAR(10) PRIMARY KEY,
  ho_ten        VARCHAR(100) NOT NULL,
  nguoi_dung_id INT UNSIGNED NULL UNIQUE,
  FOREIGN KEY (nguoi_dung_id) REFERENCES nguoi_dung(id) ON DELETE SET NULL
);

-- Bảng tv_luu_but: sổ lưu bút / nhận xét của khách gửi cho thành viên
CREATE TABLE tv_luu_but (
  id        INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  mssv      CHAR(10) NOT NULL,
  ten_khach VARCHAR(50) NOT NULL,
  loi_nhan  VARCHAR(500) NOT NULL,
  thoi_gian DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (mssv) REFERENCES thanh_vien(mssv) ON DELETE CASCADE
);

-- Bảng tv_du_an_tan: danh sách dự án phần mềm và học liệu tiêu biểu của Tấn
CREATE TABLE tv_du_an_tan (
  id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  mssv          CHAR(10) NOT NULL,
  ten_du_an     VARCHAR(150) NOT NULL,
  cong_nghe     VARCHAR(100) NOT NULL,
  nam_thuc_hien SMALLINT UNSIGNED NOT NULL,
  mo_ta         TEXT NULL,
  luot_tai      INT UNSIGNED NOT NULL DEFAULT 0,
  FOREIGN KEY (mssv) REFERENCES thanh_vien(mssv) ON DELETE CASCADE
);

-- Bảng tv_luot_xem: đếm lượt xem trang cá nhân theo ngày (khóa chính ghép mssv + ngay)
CREATE TABLE tv_luot_xem (
  mssv     CHAR(10) NOT NULL,
  ngay     DATE NOT NULL,
  so_luot  INT UNSIGNED NOT NULL DEFAULT 1,
  PRIMARY KEY (mssv, ngay),
  FOREIGN KEY (mssv) REFERENCES thanh_vien(mssv) ON DELETE CASCADE
);

