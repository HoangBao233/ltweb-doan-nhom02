-- database/seed.sql — Dữ liệu mẫu ban đầu cho dự án ITeduShare (Nhóm 02)
-- Chạy sau database/schema.sql

SET NAMES utf8mb4;
USE ltweb_nhom02;

-- 1. Dữ liệu bảng danh_muc
INSERT INTO danh_muc (id, ten, slug) VALUES
  (1, 'Tin học Tiểu học', 'tieu-hoc'),
  (2, 'Tin học THCS', 'thcs'),
  (3, 'Tin học THPT', 'thpt'),
  (4, 'Chuyên đề STEM & Robotics', 'stem-robotics'),
  (5, 'Lập trình thi Học sinh giỏi', 'lap-trinh-hsg');

-- 2. Dữ liệu bảng nguoi_dung (mật khẩu mặc định: matkhau123)
-- Mã băm: $2y$10$99W6KsBYiFvuy50BKftZ/e0Ttgx9GduPPmAmyhNN9ETOvjR1YpE3y
INSERT INTO nguoi_dung (id, email, ho_ten, mat_khau, vai_tro) VALUES
  (1, 'admin@itedushare.vn', 'Quản trị viên Hệ thống', '$2y$10$99W6KsBYiFvuy50BKftZ/e0Ttgx9GduPPmAmyhNN9ETOvjR1YpE3y', 'quan_tri'),
  (2, 'hocvien@itedushare.vn', 'Học viên Nguyễn Văn An', '$2y$10$99W6KsBYiFvuy50BKftZ/e0Ttgx9GduPPmAmyhNN9ETOvjR1YpE3y', 'khach_hang'),
  (3, 'tan@itedushare.vn', 'Nguyễn Cảnh Tấn', '$2y$10$99W6KsBYiFvuy50BKftZ/e0Ttgx9GduPPmAmyhNN9ETOvjR1YpE3y', 'quan_tri');

-- 3. Dữ liệu bảng giao_an (học liệu mẫu)
INSERT INTO giao_an (id, danh_muc_id, ten, cap_hoc, gia, ton_kho, hinh_anh, video_url, mo_ta, luot_tai) VALUES
  (1, 3, 'Giáo án Python Cơ bản - Cấu trúc rẽ nhánh', 'THPT', 0, 999, 'images/banner-trang-chu.jpg', 'https://www.youtube.com/embed/IRWYkSjMclM', 'Bài giảng chi tiết về if/else trong ngôn ngữ lập trình Python.', 150),
  (2, 2, 'Sơ đồ tư duy Hệ điều hành Windows', 'THCS', 10000, 50, 'images/so-do-cay-thu-muc.png', 'https://www.youtube.com/embed/IRWYkSjMclM', 'Hệ thống hóa kiến thức về hệ điều hành, tập tin và thư mục.', 80),
  (3, 3, 'Đề kiểm tra Giữa kì 1 - Tin học 11', 'THPT', 15000, 30, 'images/banner-trang-chu.jpg', 'https://www.youtube.com/embed/IRWYkSjMclM', 'Bộ đề thi trắc nghiệm và tự luận môn Tin học 11 bám sát GDPT 2018.', 220),
  (4, 2, 'Thực hành Excel: Hàm VLOOKUP', 'THCS', 0, 999, 'images/banner-trang-chu.jpg', 'https://www.youtube.com/embed/IRWYkSjMclM', 'Bài tập thực hành bảng tính điện tử cơ bản và nâng cao.', 95),
  (5, 3, 'Giáo án C++: Vòng lặp For và While', 'THPT', 20000, 25, 'images/banner-trang-chu.jpg', 'https://www.youtube.com/embed/IRWYkSjMclM', 'Tài liệu bồi dưỡng học sinh giỏi môn Tin học phần vòng lặp.', 130),
  (6, 3, 'Bảo mật thông tin trên mạng Internet', 'THPT', 0, 999, 'images/banner-trang-chu.jpg', 'https://www.youtube.com/embed/IRWYkSjMclM', 'Bài giảng lý thuyết về an toàn và bảo mật thông tin trực tuyến.', 65);

-- 4. Dữ liệu bảng don_hang và chi_tiet_don
INSERT INTO don_hang (id, nguoi_dung_id, tong_tien, trang_thai) VALUES
  (1, 2, 25000, 'da_thanh_toan');

INSERT INTO chi_tiet_don (don_hang_id, giao_an_id, so_luong, don_gia) VALUES
  (1, 2, 1, 10000),
  (1, 3, 1, 15000);

-- 5. Dữ liệu bảng lien_he
INSERT INTO lien_he (id, ho_ten, email, chu_de, noi_dung) VALUES
  (1, 'Trần Minh Đức', 'duc.tran@gmail.com', 'Góp ý học liệu Python', 'Tài liệu rất hay và trực quan, mong nhóm ra thêm phần mảng và chuỗi.'),
  (2, 'Phạm Quỳnh Anh', 'quynhanh.edu@gmail.com', 'Đăng ký đóng góp giáo án', 'Tôi là giáo viên Tin học THCS, muốn chia sẻ thêm bộ bài giảng Canva.');

-- ============================================================================
-- PHẦN B: DỮ LIỆU TRANG CÁ NHÂN CỦA NGUYỄN CẢNH TẤN (MSSV: 3120124027)
-- ============================================================================

-- Bảng thanh_vien: thông tin thành viên (Tấn liên kết với tài khoản id=3 trong nguoi_dung)
INSERT INTO thanh_vien (mssv, ho_ten, nguoi_dung_id) VALUES
  ('3120124027', 'Nguyễn Cảnh Tấn', 3),
  ('3120124003', 'Lê Hoàng Bảo', 1),
  ('3120124006', 'Lê Thị Xuân Châu', NULL),
  ('3120222126', 'Nguyễn Tiến Thịnh', NULL);

-- Bảng tv_luu_but: 10 lời nhắn mẫu gửi cho Nguyễn Cảnh Tấn (MSSV 3120124027)
INSERT INTO tv_luu_but (id, mssv, ten_khach, loi_nhan, thoi_gian) VALUES
  (1, '3120124027', 'Lê Viết Chung (GV)', 'Thầy rất ấn tượng với dự án mô phỏng thuật toán của em. Cố gắng phát huy nhé!', '2026-10-12 08:30:00'),
  (2, '3120124027', 'Lê Hoàng Bảo', 'Giao diện trang cá nhân và chức năng tra cứu dự án mượt mà lắm bạn Tấn ơi!', '2026-10-13 10:15:00'),
  (3, '3120124027', 'Lê Thị Xuân Châu', 'Lời nhắn và thiết kế CSDL của Tấn rất chuẩn chỉ, hỗ trợ nhóm rất nhiệt tình!', '2026-10-14 14:20:00'),
  (4, '3120124027', 'Nguyễn Tiến Thịnh', 'Học liệu Python trực quan, mình áp dụng vào bài thuyết trình đạt điểm rất cao.', '2026-10-15 09:45:00'),
  (5, '3120124027', 'Trần Thị Thu Hà (K20)', 'Giáo án STEM môn Tin học của anh soạn rất công phu và sáng tạo, cảm ơn anh!', '2026-10-16 16:00:00'),
  (6, '3120124027', 'Phan Văn Hùng', 'Bộ đề thi trắc nghiệm khách quan rất bám sát chương trình GDPT 2018.', '2026-10-17 11:10:00'),
  (7, '3120124027', 'Đoàn Duy Bình (GV)', 'Em nắm vững kiến thức hệ quản trị cơ sở dữ liệu và truy vấn chuẩn mực.', '2026-10-18 15:30:00'),
  (8, '3120124027', 'Võ Hoàng Nam', 'Các ứng dụng Pygame của bạn truyền cảm hứng lập trình rất tốt cho học sinh.', '2026-10-19 20:05:00'),
  (9, '3120124027', 'Nguyễn Mai Lan', 'Chúc bạn Tấn hoàn thành xuất sắc đồ án môn Thiết kế và Lập trình Web!', '2026-10-20 18:25:00'),
  (10, '3120124027', 'Bùi Quốc Tuấn', 'Trang web portfolio rất chuyên nghiệp, cấu trúc rõ ràng và dễ tiếp cận.', '2026-10-21 21:40:00');

-- Bảng tv_du_an_tan: 10 dự án phần mềm và học liệu tiêu biểu của Tấn
INSERT INTO tv_du_an_tan (id, mssv, ten_du_an, cong_nghe, nam_thuc_hien, mo_ta, luot_tai) VALUES
  (1, '3120124027', 'Phần mềm mô phỏng thuật toán sắp xếp', 'Python', 2024, 'Mô phỏng trực quan các thuật toán Bubble Sort, Quick Sort, Merge Sort với Pygame phục vụ giảng dạy Tin học 11.', 145),
  (2, '3120124027', 'Hệ thống thi trắc nghiệm trực tuyến Edutest', 'PHP & MySQL', 2024, 'Nền tảng thi và chấm trắc nghiệm trực tuyến tự động, phân quyền giáo viên - học sinh, thống kê phổ điểm chi tiết.', 230),
  (3, '3120124027', 'Trợ lý ảo hỗ trợ học tập EduBot', 'Python', 2025, 'Ứng dụng chatbot tra cứu tài liệu học tập và giải đáp câu hỏi cú pháp lập trình tự động bằng xử lý ngôn ngữ tự nhiên.', 89),
  (4, '3120124027', 'Ứng dụng nhận diện cử chỉ tay trình chiếu', 'Python', 2023, 'Sử dụng OpenCV và MediaPipe nhận diện cử chỉ ngón tay để lật trang bài giảng PowerPoint từ xa không cần chuột.', 112),
  (5, '3120124027', 'Kho học liệu số giáo dục ITeduShare', 'PHP & MySQL', 2025, 'Website chia sẻ giáo án điện tử, đề thi và video học liệu tin học bám sát chương trình GDPT 2018 cho giáo viên.', 420),
  (6, '3120124027', 'Bộ bài giảng STEM Lập trình Robotics', 'C++', 2023, 'Chuỗi bài giảng và tài liệu hướng dẫn học sinh THCS lập trình điều khiển xe robot dò đường bằng Arduino.', 78),
  (7, '3120124027', 'Game giáo dục: Cuộc phiêu lưu vào xứ sở Binary', 'Python', 2024, 'Trò chơi hóa bài học chuyển đổi hệ nhị phân sang thập phân trên nền tảng Pygame, kích thích hứng thú của học sinh.', 165),
  (8, '3120124027', 'Hệ thống quản lý thư viện trường học LibManage', 'C# & SQL Server', 2022, 'Phần mềm desktop quản lý đầu sách, thẻ độc giả, mượn trả và in phiếu nhắc hẹn tự động.', 95),
  (9, '3120124027', 'Website portfolio cá nhân sư phạm tin học', 'HTML/CSS/JS', 2023, 'Trang web giới thiệu hồ sơ năng lực cá nhân, sản phẩm giảng dạy và thành tích học tập với thiết kế chuẩn responsive.', 310),
  (10, '3120124027', 'Hệ thống tự động chấm bài tập lập trình CodeJudge', 'Python', 2025, 'Hệ thống sandbox thực thi và chấm điểm tự động các bài nộp thuật toán của học sinh theo các test case ẩn.', 180);

-- Bảng tv_luot_xem: 10 dòng dữ liệu lượt xem 10 ngày gần nhất của Tấn (sử dụng ngày động quanh CURDATE)
INSERT INTO tv_luot_xem (mssv, ngay, so_luot) VALUES
  ('3120124027', DATE_SUB(CURDATE(), INTERVAL 9 DAY), 18),
  ('3120124027', DATE_SUB(CURDATE(), INTERVAL 8 DAY), 25),
  ('3120124027', DATE_SUB(CURDATE(), INTERVAL 7 DAY), 32),
  ('3120124027', DATE_SUB(CURDATE(), INTERVAL 6 DAY), 45),
  ('3120124027', DATE_SUB(CURDATE(), INTERVAL 5 DAY), 38),
  ('3120124027', DATE_SUB(CURDATE(), INTERVAL 4 DAY), 52),
  ('3120124027', DATE_SUB(CURDATE(), INTERVAL 3 DAY), 60),
  ('3120124027', DATE_SUB(CURDATE(), INTERVAL 2 DAY), 74),
  ('3120124027', DATE_SUB(CURDATE(), INTERVAL 1 DAY), 85),
  ('3120124027', CURDATE(), 20)
ON DUPLICATE KEY UPDATE so_luot = VALUES(so_luot);

