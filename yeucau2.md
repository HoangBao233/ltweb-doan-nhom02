📋 BÁO CÁO KIỂM ĐỊNH TOÀN DIỆN DỰ ÁN (BÀI TẬP 5 - PHP)
I. CÁC LỖI NGHIÊM TRỌNG (CRITICAL BUGS)

Những lỗi này khiến hệ thống hoạt động sai chức năng hoặc trực tiếp làm trượt các ca kiểm thử trong hướng dẫn:

1. Mã băm mật khẩu Admin bị sai — Không thể đăng nhập Quản trị (Ca 10 thất bại)
Vị trí: inc/tai-khoan.php
Hiện trạng:
php
const TAI_KHOAN = [
    'admin' => '$2y$10$wFq3mP.V/M.jU.v9U9C3h.T3uH4y6nF5g.R/R3x2x.Z/T5Q/Q9b9K' 
];
Vấn đề: Chuỗi hash bcrypt trên là chuỗi mẫu giả lập, không khớp với mật khẩu nhom02@ltweb.
Kết quả kiểm tra bằng lệnh: password_verify('nhom02@ltweb', TAI_KHOAN['admin']) trả về false.
Hệ quả: Bất kỳ ai nhập tài khoản admin / mật khẩu nhom02@ltweb tại dang-nhap.php đều bị báo "Sai tên đăng nhập hoặc mật khẩu", hoàn toàn không vào được trang quản trị.
2. Lệch cấu trúc khóa (Key mismatch) giữa Form Liên hệ và Trang Quản trị
Vị trí: ve-chung-toi.php vs. quan-tri.php
Hiện trạng:
Tại ve-chung-toi.php, khi người dùng gửi form, dữ liệu lưu vào file lien-he.jsonl dùng các khóa dạng chữ thường / snake_case:
php
'hoten'    => $du['hoten'],
'email'    => $du['email'],
'sdt'      => $du['sdt'],
'noidung'  => $du['noidung'],
'anh'      => $ten_anh_uploaded,
'ngay_tao' => date('Y-m-d H:i:s')
Nhưng tại quan-tri.php, giao diện lại đọc bằng các khóa dạng camelCase:
php
$lh['thoiGian'] ?? ''   // Sai, trong file là 'ngay_tao'
$lh['hoTen']    ?? ''   // Sai, trong file là 'hoten'
$lh['noiDung']  ?? ''   // Sai, trong file là 'noidung'
Hệ quả: Khi vào trang quản trị xem danh sách liên hệ đã gửi, các cột Thời gian, Họ tên, và Nội dung đều bị TRỐNG (trắng trơn), chỉ hiển thị mỗi Email và Ảnh!
II. CÁC ĐIỂM CHƯA ĐÁP ỨNG ĐÚNG YÊU CẦU ĐỀ BÀI (REQUIREMENT GAPS)
3. Header dùng chung thiếu nhiều tiêu chí bắt buộc (Chức năng 1 - Bảo)
Vị trí: inc/header.php
Đối chiếu yêu cầu mục 1.3 và Chức năng 1:
Thiếu số lượng giáo án đã lưu (Badge): Đề bài yêu cầu "Hiển thị số lượng giáo án đã lưu (từ Session) / Badge trên header". Hiện tại menu Giáo án đã lưu trong header là thẻ tĩnh thuần túy, không hề gọi hàm soMon() của DanhSachLuu để hiện số đếm (0) hay (2).
Thiếu vòng lặp foreach: Đề bài yêu cầu "header.php dùng foreach để in menu". Hiện tại menu được viết cứng 5 thẻ <li> thủ công.
Thiếu thuộc tính Accessibility aria-current="page": Đề bài yêu cầu "kiểm tra $trang === $tep để thêm aria-current="page"". Hiện tại chỉ có class CSS menu-chinh__lien-ket--hien-tai, không có thuộc tính ARIA này (ảnh hưởng điểm Lighthouse a11y).
Lệch tên biến active trang chủ:
Ở index.php: gán $trang_hien_tai = 'index';
Nhưng ở inc/header.php: lại kiểm tra $trang === 'trang-chu'.
Vì vậy, khi người dùng đang ở Trang chủ, nút menu "Trang chủ" hoàn toàn không được tô sáng / active.
4. Thiếu thẻ đóng HTML (</body></html>) và Script trên nhiều trang chính
Nguyên nhân: File inc/footer.php chỉ chứa duy nhất khối thẻ <footer class="trang__chan">...</footer>, không đóng </body></html>.
Tình trạng: Một số trang tự đóng </body></html> ở cuối file, nhưng các trang sau kết thúc lơ lửng ngay tại dòng gọi footer, dẫn đến lỗi cú pháp HTML (không vượt qua được W3C Validator 0 lỗi):
❌ kho-hoc-lieu.php: Thiếu đóng thẻ </body></html> và thiếu nạp <script type="module" src="js/main.js"></script>.
❌ dang-nhap.php: Thiếu đóng thẻ </body></html> và thiếu nạp js/main.js.
❌ quan-tri.php: Thiếu đóng thẻ </body></html> và thiếu nạp js/main.js.
5. Chưa dọn dẹp các file JavaScript cũ (Bước 5)
Vị trí: Thư mục js/
Đối chiếu yêu cầu: Hướng dẫn ghi rõ:

Xoá / vô hiệu hoá:

Toàn bộ js/trang-danh-sach.js — PHP đã thay thế
Toàn bộ js/trang-chi-tiet.js — PHP đã thay thế
Hiện trạng: Cả 2 file vật lý js/trang-danh-sach.js (174 dòng) và js/trang-chi-tiet.js (96 dòng) vẫn còn nằm nguyên trong thư mục js/ (chưa bị xóa bỏ).
6. Tệp README.md hoàn toàn chưa được tạo (Mục 9)
Hiện trạng: Tệp README.md hiện không tồn tại tại thư mục gốc dự án.
Yêu cầu cần có:
Hướng dẫn cài đặt Composer (composer install) và khởi chạy (php -S localhost:8000).
Yêu cầu môi trường PHP >= 8.1.
Cung cấp tài khoản thử quản trị (admin / nhom02@ltweb).
Bảng ánh xạ 7 đường dẫn URL chính sang các file .php.
7. Định nghĩa hàm e() trong inc/ham.php có nguy cơ gây lỗi Type Error
Vị trí: inc/ham.php
Hiện trạng:
php
function e(string $s): string {
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}
Vấn đề: Type hint nhận string $s. Nếu trong code truyền vào số nguyên (như e($ga->id)), số thực hoặc null, trên PHP 8.1+ sẽ lập tức văng Fatal TypeError.
Yêu cầu chuẩn của đề bài: Phải là function e(mixed $str): string kèm cờ ENT_QUOTES | ENT_SUBSTITUTE và ép kiểu (string)$str.
III. TÌNH TRẠNG CÁC TRANG CÁ NHÂN (PHẦN B)
Thành viên	Trạng thái đồng bộ Header/Footer	2 Chức năng động phía máy chủ	Đóng thẻ HTML & JS
Lê Hoàng Bảo (3120124003)	🟢 Đã đồng bộ	1. Sổ lưu bút (JSONL)
2. Khảo sát công nghệ (JSON)	🟢 Đầy đủ
Nguyễn Cảnh Tấn (3120124027)	🟢 Đã đồng bộ	1. Đếm lượt xem (luotxem.txt)
2. Lọc kỹ năng qua GET URL	🔴 Thiếu </body></html>
Lê Thị Xuân Châu (3120124006)	🔴 CHƯA ĐỒNG BỘ (vẫn dùng HTML tĩnh riêng, chưa require header.php)	1. Giao diện Sáng/Tối (Cookie)
2. Tải ảnh đại diện an toàn	🟢 Tự đóng trong HTML riêng
Nguyễn Tiến Thịnh (3120222126)	🟢 Đã đồng bộ	1. Máy tính điểm GPA (20-30-50%)
2. Yêu cầu nhận CV (JSONL)	🔴 Thiếu </body></html>

NOTE

Về chức năng của Bảo: Trong bảng gợi ý ban đầu của đề bài, Bảo được phân công "Máy tính điểm học phần (20-30-50%)". Tuy nhiên bạn Thịnh đã làm tính năng Máy tính điểm GPA này rồi, nên việc Bảo chuyển sang làm "Khảo sát / Bình chọn công nghệ" là hoàn toàn hợp lý (đảm bảo nguyên tắc 4 thành viên không trùng lặp chức năng).

IV. BẢNG ĐỐI CHIẾU 12 CA KIỂM THỬ (BẢNG 3 TRONG HƯỚNG DẪN)
Ca	Nội dung kiểm thử	Kết quả hiện tại	Đánh giá
Ca 1	Form liên hệ bật novalidate, để trống các ô và submit	Server chặn lại và báo lỗi từng ô trong ve-chung-toi.php	🟢 PASS
Ca 2	Nhập email sai dạng lan@ued, SĐT abc	Server bắt được lỗi định dạng	🟢 PASS
Ca 3	File text đổi tên gia-mao.jpg đính kèm form liên hệ	Kiểm tra MIME thật bằng finfo_file bắt được file giả	🟢 PASS
Ca 4	Đính kèm ảnh thật nhưng dung lượng > 2MB	Báo lỗi vượt quá 2MB	🟢 PASS
Ca 5	Gửi form liên hệ hợp lệ rồi nhấn F5 xem có bị trùng lặp	Áp dụng PRG (header Location), không bị ghi đúp	🟢 PASS
Ca 6	Nhập chuỗi XSS "><script>alert(1)</script> vào ô tìm kiếm	Ô tìm kiếm có bọc e($q), chống XSS an toàn	🟢 PASS
Ca 7	Truy cập chi-tiet-giao-an.php?id=abc, ?id=999999, ?id=	Bắt được ID không tồn tại và trả về trang 404.php	🟢 PASS
Ca 8	Sửa ID / số lượng thành 0, -3, 1000 rồi submit lưu	Chỉ thêm ID tồn tại trong KhoGiaoAn vào session	🟢 PASS
Ca 9	Thêm 2 giáo án vào giỏ, xóa 1, kiểm tra số đếm badge trên header	Giỏ hàng chạy đúng, nhưng header KHÔNG CÓ badge hiển thị	🔴 FAIL
Ca 10	Mở quan-tri.php khi chưa đăng nhập, đăng nhập sai, đăng nhập đúng	Chưa đăng nhập chuyển hướng tốt, nhưng đăng nhập đúng BỊ LỖI (hash sai); dữ liệu liên hệ bị trống tên/nội dung	🔴 FAIL
Ca 11	Sửa cookie da_xem thành abc,-1,999999 rồi tải lại trang chủ	index.php lọc sạch ID sai và chỉ hiện ID hợp lệ	🟢 PASS
Ca 12	Chuyển MOI_TRUONG = 'prod', đổi tên giao-an.json, mở kho học liệu	Đã có set_exception_handler và trang 500.php	🟢 PASS
V. CÁC ĐIỀU KIỆN TRƯỚC KHI NỘP BÀI (CHECKLIST CUỐI)
Cấu hình môi trường: Tại inc/config.php, hằng số MOI_TRUONG hiện vẫn đang để là 'dev'. Trước khi nộp bài cần đổi sang 'prod'.