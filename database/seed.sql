-- database/seed.sql — Dữ liệu mẫu kiểm thử cho đồ án ITeduShare (Nhóm 02)
-- Đề tài: Nền tảng chia sẻ giáo án & học liệu Tin học
-- Học phần: Thiết kế và Lập trình Web — Khoa Toán - Tin, Trường ĐH Sư phạm - ĐH Đà Nẵng
-- Lưu ý: Tệp này chạy SAU khi đã nạp schema.sql
-- Mật khẩu cho mọi tài khoản thử nghiệm: matkhau123

SET NAMES utf8mb4;
USE ltweb_nhom02;

-- ============================================================================
-- 1. NẠP DỮ LIỆU BẢNG DANH MỤC (danh_muc)
-- Yêu cầu: Ít nhất 4 danh mục, có ít nhất 1 danh mục TRỐNG không có giáo án (id = 5)
-- ============================================================================
INSERT INTO danh_muc (id, ten, slug) VALUES
  (1, 'Tin học THCS', 'tin-hoc-thcs'),
  (2, 'Tin học THPT', 'tin-hoc-thpt'),
  (3, 'Lập trình & STEM', 'lap-trinh-stem'),
  (4, 'Tin học văn phòng & Đồ họa', 'van-phong-do-hoa'),
  (5, 'Chuyên đề Bồi dưỡng HSG', 'chuyen-de-boi-duong-hsg'); -- Danh mục TRỐNG để kiểm thử

-- ============================================================================
-- 2. NẠP DỮ LIỆU BẢNG NGƯỜI DÙNG (nguoi_dung)
-- Yêu cầu: 1 quan_tri (admin@ued.vn), 2 khach_hang (lan@ued.vn, an@ued.vn)
-- Mật khẩu chung: matkhau123 (đã băm bằng password_hash bcrypt)
-- ============================================================================
INSERT INTO nguoi_dung (id, email, ho_ten, mat_khau, vai_tro, ngay_tao) VALUES
  (1, 'admin@ued.vn', 'Quản trị viên Hệ thống',
   '$2y$12$qHb8qF.xgsQsDEN5nvxo6OsTDLDQmDQmTnjDdlNCp7MqAzd4fkWBG', 'quan_tri', '2026-10-01 08:00:00'),
  (2, 'lan@ued.vn', 'Nguyễn Thị Lan',
   '$2y$12$u.0T5HsnlVJWcPLCbRUsIO9EXccEweLCYOD7R46J.TJ9bH4ju5eGC', 'khach_hang', '2026-10-05 09:30:00'),
  (3, 'an@ued.vn', 'Trần Văn An',
   '$2y$12$3ZoP8Zw46/1SUlmMu/unR.ZIvgTjCuo2NGWs2F9ttQBE.FmFo83bO', 'khach_hang', '2026-10-10 14:15:00'),
  -- Tài khoản liên kết 4 thành viên nhóm (phục vụ Phần B)
  (4, 'bao@ued.vn', 'Lê Hoàng Bảo',
   '$2y$12$qHb8qF.xgsQsDEN5nvxo6OsTDLDQmDQmTnjDdlNCp7MqAzd4fkWBG', 'quan_tri', '2026-10-12 10:00:00'),
  (5, 'tan@ued.vn', 'Nguyễn Cảnh Tấn',
   '$2y$12$qHb8qF.xgsQsDEN5nvxo6OsTDLDQmDQmTnjDdlNCp7MqAzd4fkWBG', 'khach_hang', '2026-10-12 10:05:00'),
  (6, 'chau@ued.vn', 'Lê Thị Xuân Châu',
   '$2y$12$qHb8qF.xgsQsDEN5nvxo6OsTDLDQmDQmTnjDdlNCp7MqAzd4fkWBG', 'khach_hang', '2026-10-12 10:10:00'),
  (7, 'thinh@ued.vn', 'Nguyễn Tiến Thịnh',
   '$2y$12$qHb8qF.xgsQsDEN5nvxo6OsTDLDQmDQmTnjDdlNCp7MqAzd4fkWBG', 'khach_hang', '2026-10-12 10:15:00');

-- ============================================================================
-- 3. NẠP DỮ LIỆU BẢNG THÀNH VIÊN (thanh_vien)
-- ============================================================================
INSERT INTO thanh_vien (mssv, ho_ten, nguoi_dung_id) VALUES
  ('3120124003', 'Lê Hoàng Bảo', 4),
  ('3120124027', 'Nguyễn Cảnh Tấn', 5),
  ('3120124006', 'Lê Thị Xuân Châu', 6),
  ('3120222126', 'Nguyễn Tiến Thịnh', 7);

-- ============================================================================
-- 4. NẠP DỮ LIỆU BẢNG GIÁO ÁN (giao_an)
-- Yêu cầu:
--   - Ít nhất 30 giáo án mẫu (ở đây nạp 33 giáo án)
--   - Có mục chứa dấu nháy đơn ' (Levi's) để kiểm thử Ca 1 Bảng 3
--   - Có mục chứa chuỗi '50%' để kiểm thử Ca 1 Bảng 3
--   - Có mục ton_kho = 0 (hết hàng) để kiểm thử Ca 10 Bảng 3
-- ============================================================================
INSERT INTO giao_an (id, danh_muc_id, ten, gia, ton_kho, hinh_anh, mo_ta, cap_hoc, video_url, ngay_tao) VALUES
  -- 12 giáo án kế thừa từ Bài tập 5
  (1, 3, 'Giáo án Python Cơ bản - Cấu trúc rẽ nhánh', 0, 20, 'images/banner-trang-chu.jpg',
   'Bài giảng chi tiết về mệnh đề if/else trong ngôn ngữ lập trình Python dành cho học sinh THPT.', 'THPT',
   'https://www.youtube.com/embed/IRWYkSjMclM', '2026-09-01 08:00:00'),

  (2, 1, 'Sơ đồ tư duy Hệ điều hành Windows', 10000, 15, 'images/so-do-cay-thu-muc.png',
   'Hệ thống hóa kiến thức cơ bản về hệ điều hành, tập tin và thư mục trong chương trình Tin học THCS.', 'THCS',
   'https://www.youtube.com/embed/IRWYkSjMclM', '2026-09-02 09:30:00'),

  (3, 2, 'Đề kiểm tra Giữa kì 1 - Tin học 11', 15000, 12, 'images/banner-trang-chu.jpg',
   'Bộ đề thi kết hợp trắc nghiệm và tự luận môn Tin học 11 bám sát chương trình GDPT 2018.', 'THPT',
   'https://www.youtube.com/embed/IRWYkSjMclM', '2026-09-03 10:15:00'),

  (4, 4, 'Thực hành Excel: Hàm VLOOKUP', 0, 30, 'images/banner-trang-chu.jpg',
   'Bài tập thực hành bảng tính điện tử cơ bản và nâng cao với các hàm dò tìm dữ liệu.', 'THCS',
   'https://www.youtube.com/embed/IRWYkSjMclM', '2026-09-04 14:00:00'),

  (5, 3, 'Giáo án C++: Vòng lặp For và While', 20000, 8, 'images/banner-trang-chu.jpg',
   'Tài liệu bồi dưỡng học sinh giỏi môn Tin học phần cấu trúc lặp và thuật toán cơ sở.', 'THPT',
   'https://www.youtube.com/embed/IRWYkSjMclM', '2026-09-05 16:45:00'),

  (6, 1, 'Bảo mật thông tin trên mạng Internet', 0, 25, 'images/banner-trang-chu.jpg',
   'Bài giảng lý thuyết về an toàn mạng, phòng tránh lừa đảo và bảo vệ dữ liệu cá nhân trực tuyến.', 'THCS',
   'https://www.youtube.com/embed/IRWYkSjMclM', '2026-09-06 08:30:00'),

  (7, 4, 'Thiết kế đồ họa cơ bản với Photoshop', 50000, 5, 'images/banner-trang-chu.jpg',
   'Chuyên đề hướng dẫn làm quen với các công cụ chỉnh sửa ảnh và tạo banner học tập.', 'THPT',
   'https://www.youtube.com/embed/IRWYkSjMclM', '2026-09-07 11:20:00'),

  (8, 3, 'Giáo án Scratch: Lập trình game Flappy Bird', 10000, 18, 'images/banner-trang-chu.jpg',
   'Hướng dẫn trực quan từng bước kéo thả khối lệnh Scratch để xây dựng trò chơi hoàn chỉnh.', 'THCS',
   'https://www.youtube.com/embed/IRWYkSjMclM', '2026-09-08 13:50:00'),

  (9, 3, 'Cơ sở dữ liệu Quan hệ và SQL', 25000, 10, 'images/banner-trang-chu.jpg',
   'Lý thuyết thiết kế bảng, khóa chính, khóa ngoại và thực hành truy vấn SQL cơ bản.', 'THPT',
   'https://www.youtube.com/embed/IRWYkSjMclM', '2026-09-09 15:10:00'),

  (10, 1, 'Sử dụng an toàn mạng xã hội', 0, 40, 'images/banner-trang-chu.jpg',
   'Giáo án kỹ năng số và văn hóa ứng xử trên không gian mạng dành cho học sinh cấp 2.', 'THCS',
   'https://www.youtube.com/embed/IRWYkSjMclM', '2026-09-10 09:00:00'),

  (11, 3, 'Lập trình Web tĩnh HTML & CSS', 30000, 14, 'images/banner-trang-chu.jpg',
   'Bài thực hành thiết kế giao diện trang web cá nhân chuẩn responsive bằng HTML5 và CSS3.', 'THPT',
   'https://www.youtube.com/embed/IRWYkSjMclM', '2026-09-11 10:40:00'),

  (12, 1, 'Sơ đồ tư duy Mạng máy tính', 5000, 22, 'images/so-do-cay-thu-muc.png',
   'Mô hình Client-Server, giao thức TCP/IP và hệ thống tên miền DNS trực quan bằng Mindmap.', 'THCS',
   'https://www.youtube.com/embed/IRWYkSjMclM', '2026-09-12 14:30:00'),

  -- ==========================================================================
  -- CÁC GIÁO ÁN MỚI BỔ SUNG ĐẶC THÙ ĐỂ PHỤC VỤ ĐỦ TIÊU CHÍ KIỂM THỬ (13 - 33)
  -- ==========================================================================

  -- [ĐIỀU KIỆN 1]: Tên chứa dấu nháy đơn ' (Levi's) để kiểm thử Ca 1 Bảng 3
  (13, 3, 'Giáo án Python: Cấu trúc rẽ nhánh phong cách Levi''s 501', 35000, 15, 'images/banner-trang-chu.jpg',
   'Bài giảng chuyên đề cấu trúc điều khiển trong lập trình Python với ví dụ thực tế Levi''s 501.', 'THPT',
   'https://www.youtube.com/embed/IRWYkSjMclM', '2026-09-13 16:00:00'),

  -- [ĐIỀU KIỆN 2]: Tên chứa chuỗi '50%' để kiểm thử Ca 1 Bảng 3
  (14, 2, 'Đề ôn tập Tin học 11: 50% trắc nghiệm và 50% thực hành', 12000, 20, 'images/banner-trang-chu.jpg',
   'Bộ đề cương chuẩn hóa kiểm tra định kỳ học kỳ 1 môn Tin học lớp 11 với 50% trắc nghiệm.', 'THPT',
   'https://www.youtube.com/embed/IRWYkSjMclM', '2026-09-14 08:20:00'),

  -- [ĐIỀU KIỆN 3]: Mục ton_kho = 0 (hết hàng) để kiểm thử Ca 10 Bảng 3
  (15, 3, 'Chuyên đề STEM: Robot Arduino dò đường tự động', 45000, 0, 'images/banner-trang-chu.jpg',
   'Tài liệu hướng dẫn lắp ráp mạch cảm biến và lập trình vi điều khiển Arduino. (Hiện tạm hết lượt cấp phát).', 'THPT',
   'https://www.youtube.com/embed/IRWYkSjMclM', '2026-09-15 11:00:00'),

  (16, 1, 'Tin học 6: Làm quen với chuột và bàn phím máy tính', 0, 50, 'images/banner-trang-chu.jpg',
   'Giáo án mở đầu cho học sinh lớp 6 làm quen với thiết bị phần cứng máy tính.', 'THCS',
   'https://www.youtube.com/embed/IRWYkSjMclM', '2026-09-16 13:40:00'),

  (17, 1, 'Tin học 7: Định dạng đoạn văn bản trong Microsoft Word', 5000, 30, 'images/banner-trang-chu.jpg',
   'Hướng dẫn các kỹ năng canh lề, thụt dòng, tạo danh sách bullet và chèn hình ảnh.', 'THCS',
   'https://www.youtube.com/embed/IRWYkSjMclM', '2026-09-17 15:20:00'),

  (18, 1, 'Tin học 8: Thuật toán và Sơ đồ khối (Flowchart)', 10000, 16, 'images/banner-trang-chu.jpg',
   'Xây dựng tư duy logic qua biểu diễn thuật toán tính toán bằng sơ đồ khối chuẩn.', 'THCS',
   'https://www.youtube.com/embed/IRWYkSjMclM', '2026-09-18 09:10:00'),

  (19, 1, 'Tin học 9: Đa phương tiện và Tạo bài trình chiếu PowerPoint', 15000, 25, 'images/banner-trang-chu.jpg',
   'Kỹ thuật xây dựng slide thuyết trình sinh động, hiệu ứng chuyển trang và chèn âm thanh.', 'THCS',
   'https://www.youtube.com/embed/IRWYkSjMclM', '2026-09-19 14:00:00'),

  (20, 2, 'Tin học 10: Biểu diễn thông tin trong máy tính (Hệ nhị phân)', 20000, 18, 'images/banner-trang-chu.jpg',
   'Chuyển đổi qua lại giữa hệ thập phân, nhị phân và thập lục phân cùng bảng mã ASCII.', 'THPT',
   'https://www.youtube.com/embed/IRWYkSjMclM', '2026-09-20 10:30:00'),

  (21, 2, 'Tin học 10: Kiến trúc máy tính Von Neumann', 15000, 14, 'images/banner-trang-chu.jpg',
   'Lý thuyết về CPU, bộ nhớ trong (RAM/ROM) và các thiết bị ngoại vi vào ra.', 'THPT',
   'https://www.youtube.com/embed/IRWYkSjMclM', '2026-09-21 11:15:00'),

  (22, 2, 'Tin học 11: Kiểu mảng (Array) và Xử lý chuỗi trong Python', 25000, 12, 'images/banner-trang-chu.jpg',
   'Các thao tác với mảng một chiều (List) và các hàm xử lý chuỗi ký tự String nâng cao.', 'THPT',
   'https://www.youtube.com/embed/IRWYkSjMclM', '2026-09-22 14:50:00'),

  (23, 2, 'Tin học 12: Giới thiệu Trí tuệ Nhân tạo (AI) và Học máy', 40000, 10, 'images/banner-trang-chu.jpg',
   'Chuyên đề ứng dụng công nghệ AI tạo sinh (Generative AI) và đạo đức khi sử dụng AI.', 'THPT',
   'https://www.youtube.com/embed/IRWYkSjMclM', '2026-09-23 16:30:00'),

  (24, 3, 'Lập trình Python Nâng cao: Đệ quy và Quay lui (Backtracking)', 45000, 7, 'images/banner-trang-chu.jpg',
   'Giải quyết các bài toán cổ điển: Tám quân hậu, Mã đi tuần, Tháp Hà Nội bằng đệ quy.', 'THPT',
   'https://www.youtube.com/embed/IRWYkSjMclM', '2026-09-24 08:45:00'),

  (25, 3, 'Thuật toán Quy hoạch động (Dynamic Programming)', 50000, 9, 'images/banner-trang-chu.jpg',
   'Chuyên đề tối ưu bài toán con gối nhau: Dãy con tăng dài nhất, Cái túi (Knapsack).', 'THPT',
   'https://www.youtube.com/embed/IRWYkSjMclM', '2026-09-25 10:00:00'),

  (26, 3, 'Scratch Nâng cao: Thiết kế Game vượt chướng ngại vật 2D', 15000, 20, 'images/banner-trang-chu.jpg',
   'Xử lý va chạm sprite, tính điểm màn chơi và lưu lại điểm số kỷ lục.', 'THCS',
   'https://www.youtube.com/embed/IRWYkSjMclM', '2026-09-26 13:15:00'),

  (27, 4, 'Bảng tính Excel: Phân tích dữ liệu với PivotTable', 20000, 15, 'images/banner-trang-chu.jpg',
   'Tổng hợp báo cáo thống kê đa chiều nhanh chóng và trực quan hóa bằng PivotChart.', 'THPT',
   'https://www.youtube.com/embed/IRWYkSjMclM', '2026-09-27 15:40:00'),

  (28, 4, 'Thiết kế ấn phẩm truyền thông học đường bằng Canva', 0, 35, 'images/banner-trang-chu.jpg',
   'Sử dụng các template Canva tạo poster, infographic tuyên truyền ngày Nhà giáo Việt Nam.', 'THCS',
   'https://www.youtube.com/embed/IRWYkSjMclM', '2026-09-28 09:25:00'),

  (29, 4, 'Kỹ năng soạn thảo văn bản hành chính theo Nghị định 30', 25000, 18, 'images/banner-trang-chu.jpg',
   'Chuẩn thể thức văn bản: font chữ, cỡ chữ, căn lề, số hiệu và chữ ký văn bản.', 'THPT',
   'https://www.youtube.com/embed/IRWYkSjMclM', '2026-09-29 11:35:00'),

  (30, 1, 'Tin học 7: Khái niệm Bản quyền và Sở hữu trí tuệ', 0, 30, 'images/banner-trang-chu.jpg',
   'Nhận biết các loại phần mềm miễn phí, mã nguồn mở và tôn trọng quyền tác giả số.', 'THCS',
   'https://www.youtube.com/embed/IRWYkSjMclM', '2026-09-30 14:10:00'),

  (31, 2, 'Tin học 11: Cấu trúc Dữ liệu Ngăn xếp (Stack) và Hàng đợi (Queue)', 30000, 11, 'images/banner-trang-chu.jpg',
   'Mô hình LIFO, FIFO và ứng dụng trong duyệt đồ thị BFS, DFS.', 'THPT',
   'https://www.youtube.com/embed/IRWYkSjMclM', '2026-10-01 10:20:00'),

  (32, 3, 'Lập trình Vi điều khiển Micro:bit cho trường học', 20000, 16, 'images/banner-trang-chu.jpg',
   'Lập trình cảm biến nhiệt độ, gia tốc và giao tiếp không dây Radio trên Micro:bit.', 'THCS',
   'https://www.youtube.com/embed/IRWYkSjMclM', '2026-10-02 13:50:00'),

  (33, 3, 'Giáo án Tin học: Giới thiệu Ngôn ngữ lập trình C++ hiện đại', 10000, 25, 'images/banner-trang-chu.jpg',
   'Tổng quan về cú pháp C++20, cấu trúc một chương trình và các kiểu dữ liệu cơ bản.', 'THPT',
   'https://www.youtube.com/embed/IRWYkSjMclM', '2026-10-03 15:30:00');

-- ============================================================================
-- 5. NẠP DỮ LIỆU ĐƠN HÀNG MẪU (don_hang & chi_tiet_don)
-- Yêu cầu: Mỗi khách hàng (lan@ued.vn và an@ued.vn) có ít nhất 1 đơn hàng
-- ============================================================================

-- Đơn hàng 1 của Nguyễn Thị Lan (nguoi_dung_id = 2)
INSERT INTO don_hang (id, nguoi_dung_id, tong_tien, trang_thai, ghi_chu, ngay_tao) VALUES
  (1, 2, 25000, 'da_giao', 'Đơn hàng mua giáo án Windows và Đề thi học kỳ', '2026-10-06 10:00:00');

-- Chi tiết đơn hàng 1:
-- Mua giáo án id = 2 (giá 10.000) và giáo án id = 3 (giá 15.000)
INSERT INTO chi_tiet_don (don_hang_id, giao_an_id, so_luong, don_gia) VALUES
  (1, 2, 1, 10000),
  (1, 3, 1, 15000);

-- Đơn hàng 2 của Trần Văn An (nguoi_dung_id = 3)
INSERT INTO don_hang (id, nguoi_dung_id, tong_tien, trang_thai, ghi_chu, ngay_tao) VALUES
  (2, 3, 20000, 'cho_duyet', 'Đơn mua giáo án C++ bồi dưỡng học sinh giỏi', '2026-10-11 15:30:00');

-- Chi tiết đơn hàng 2:
-- Mua giáo án id = 5 (giá 20.000)
INSERT INTO chi_tiet_don (don_hang_id, giao_an_id, so_luong, don_gia) VALUES
  (2, 5, 1, 20000);

-- ============================================================================
-- 6. NẠP DỮ LIỆU BẢNG LIÊN HỆ (lien_he)
-- Mẫu liên hệ đã nhận từ người dùng để hiển thị trong quan-tri.php
-- ============================================================================
INSERT INTO lien_he (id, ho_ten, email, sdt, noi_dung, anh, ngay_tao) VALUES
  (1, 'Thầy Nguyễn Văn Cường', 'cuong.nguyen@thptdanang.edu.vn', '0905123456',
   'Nhóm cho mình xin thêm tài liệu chuyên đề Python bồi dưỡng học sinh giỏi nhé!', NULL, '2026-10-08 08:30:00'),
  (2, 'Cô Lê Thị Mai', 'maile@thcshue.edu.vn', '0914987654',
   'Website rất tiện lợi, giáo án Scratch thiết kế rất sinh động và dễ hiểu.', NULL, '2026-10-10 11:20:00'),
  (3, 'Phạm Hoàng Nam', 'namph@gmail.com', '0988112233',
   'Cho mình hỏi về cách thanh toán và tải file Word có lời giải chi tiết.', NULL, '2026-10-15 14:45:00');

-- ============================================================================
-- 7. NẠP DỮ LIỆU MẪU CHO PHẦN B (CÁC BẢNG tv_...)
-- ============================================================================

-- Bảng tv_luu_but (Trang Bảo - 3120124003)
INSERT INTO tv_luu_but (id, mssv, ten_khach, loi_nhan, thoi_gian) VALUES
  (1, '3120124003', 'Nguyễn Cảnh Tấn', 'Website thiết kế giao diện rất sáng và đẹp mắt!', '2026-10-16 08:00:00'),
  (2, '3120124003', 'Lê Thị Xuân Châu', 'Hệ thống chạy mượt, chúc dự án đạt điểm cao nhé Bảo.', '2026-10-16 09:30:00'),
  (3, '3120124003', 'Nguyễn Tiến Thịnh', 'Các chức năng phân quyền và giỏ hàng rất ổn định.', '2026-10-16 10:15:00'),
  (4, '3120124003', 'Thầy H.', 'Nhóm triển khai tốt các yêu cầu của Bài tập 6.', '2026-10-16 11:00:00'),
  (5, '3120124003', 'Bạn học cùng lớp', 'Chúc mừng nhóm trưởng đã hoàn thành đúng tiến độ!', '2026-10-16 14:00:00'),
  (6, '3120124003', 'Trần Thị Thu Hà', 'Giao diện trang cá nhân của bạn Bảo rất sáng sủa và dễ dùng!', '2026-10-17 08:30:00'),
  (7, '3120124003', 'Phan Văn Hùng', 'Chúc Bảo và nhóm hoàn thành xuất sắc đồ án Thiết kế Web!', '2026-10-18 10:00:00'),
  (8, '3120124003', 'Võ Hoàng Nam', 'Slide bài giảng và học liệu chia sẻ rất bổ ích cho sinh viên.', '2026-10-19 15:45:00');

-- Bảng tv_luot_xem (Trang Tấn - 3120124027): Thống kê 7 ngày gần nhất
INSERT INTO tv_luot_xem (mssv, ngay, so_luot) VALUES
  ('3120124027', '2026-10-17', 12),
  ('3120124027', '2026-10-18', 19),
  ('3120124027', '2026-10-19', 15),
  ('3120124027', '2026-10-20', 28),
  ('3120124027', '2026-10-21', 34),
  ('3120124027', '2026-10-22', 42),
  ('3120124027', '2026-10-23', 50);

-- Bảng tv_du_an (Trang Châu - 3120124006)
INSERT INTO tv_du_an (mssv, ten_du_an, mo_ta, lien_ket, ngay_tao) VALUES
  ('3120124006', 'Hệ thống Quản lý Thư viện số', 'Ứng dụng Web tra cứu và mượn sách trực tuyến bằng PHP & MySQL.', 'https://github.com', '2026-09-15 08:00:00'),
  ('3120124006', 'Website ITeduShare', 'Phụ trách xây dựng phân hệ Quản trị CRUD và thiết kế CSDL đồ án.', 'https://github.com', '2026-10-01 10:00:00');

-- Bảng tv_yeu_cau_cv (Trang Thịnh - 3120222126)
INSERT INTO tv_yeu_cau_cv (mssv, email, loi_nhan, ip_gui, ngay_tao) VALUES
  ('3120222126', 'hr@fpt.com', 'Vui lòng gửi CV vị trí Web Developer thực tập.', '127.0.0.1', '2026-10-18 09:00:00'),
  ('3120222126', 'tuyendung@viettel.vn', 'Mời bạn nộp hồ sơ xét tuyển kỹ sư phần mềm.', '127.0.0.1', '2026-10-20 14:30:00');


-- Dữ liệu mẫu bảng tv_du_an_tan của Tấn (3120124027)
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
