# ITeduShare — Nền tảng chia sẻ giáo án & học liệu Tin học (Nhóm 02)

> **Học phần:** Thiết kế và Lập trình Web — Chương 6: PHP và cơ sở dữ liệu MySQL  
> **Nhóm thực hiện:** Nhóm 02 (Khoa Toán – Tin, Trường ĐH Sư phạm – ĐH Đà Nẵng)

---

## 👥 Danh sách thành viên và phân công nhiệm vụ (Bảng phân công)

| STT | Họ và tên | Mã sinh viên | Tài khoản GitHub | Vai trò & Nhiệm vụ chính Bài 6 | Tỷ lệ đóng góp |
|:---:|:---|:---:|:---:|:---|:---:|
| 1 | **Lê Hoàng Bảo** | `3120124003` | `HoangBao233` | **Nhóm trưởng**; Khởi tạo CSDL MySQL (`schema.sql`, `seed.sql`, `inc/db.php`); Chức năng 1 (Đọc dữ liệu DB, chống N+1); Đồng phụ trách Chức năng 5; Phần B (`tv_luu_but`); Câu C1 | **25%** |
| 2 | **Nguyễn Cảnh Tấn** | `3120124027` | `beotan19-cpu` | Chức năng 2 (Kho học liệu: Tìm kiếm LIKE, Lọc, Sắp xếp, Phân trang); Chức năng 6 (REST API & Live Search Debounce); Phần B (`tv_luot_xem`, `tv_du_an_tan`); Câu C3 | **25%** |
| 3 | **Lê Thị Xuân Châu** | `3120124006` | `XuanChau-06` | Chức năng 3 (Quản trị CRUD Thực thể chính, Bắt lỗi 1451, Xem liên hệ); Vẽ sơ đồ ERD (`database/erd.png`); Phần B (`tv_du_an`); Câu C2 | **25%** |
| 4 | **Nguyễn Tiến Thịnh** | `3120222126` | `cowt1310` | Chức năng 4 (Đăng ký, Đăng nhập, Đăng xuất, Phân quyền Session); Chức năng 5 (Giao dịch Đặt hàng Transaction & Quản lý đơn); Phần B (`tv_yeu_cau_cv`); Câu C4 | **25%** |

---

## 💻 Yêu cầu môi trường

- **PHP:** Phiên bản `≥ 8.1` (bật extension `pdo_mysql`, `fileinfo`, `mbstring`, `session`).
- **Hệ quản trị CSDL:** MariaDB `≥ 10.4` hoặc MySQL `≥ 8.0` (mặc định của XAMPP, Laragon).
- **Composer:** Quản lý Autoloading PSR-4 (`App\` ánh xạ vào `src/`).
- **Trình duyệt & Công cụ kiểm thử:** Google Chrome, REST Client extension cho VS Code (để chạy `api.http`), `curl.exe`.

---

## 🚀 Hướng dẫn cài đặt và khởi chạy (Dành cho Giảng viên chấm bài)

### Bước 1: Cài đặt thư viện phụ thuộc (Composer)
Mở Terminal / Command Prompt tại thư mục gốc của dự án và chạy:
```bash
composer install
```

### Bước 2: Tạo và nạp Cơ sở dữ liệu `ltweb_nhom02`
CSDL sử dụng tài khoản mặc định của XAMPP: user `root`, mật khẩu rỗng (password: `""`).

**Cách 1: Dùng dòng lệnh (Khuyên dùng)**
```bash
# Trên Windows cmd/PowerShell (với mysql của XAMPP ở C:\xampp\mysql\bin):
mysql -u root < database/schema.sql
mysql -u root ltweb_nhom02 < database/seed.sql
```

**Cách 2: Dùng giao diện phpMyAdmin**
1. Mở `http://localhost/phpmyadmin/`.
2. Vào thẻ **Import** $\rightarrow$ chọn tệp `database/schema.sql` $\rightarrow$ nhấn **Import**.
3. Chọn cơ sở dữ liệu `ltweb_nhom02` vừa tạo $\rightarrow$ vào thẻ **Import** $\rightarrow$ chọn tệp `database/seed.sql` $\rightarrow$ nhấn **Import**.

---

### Bước 3: Khởi chạy website

**Cách 1: Dùng máy chủ tích hợp của PHP (Theo đúng hướng dẫn đề bài)**
```bash
php -S localhost:8000
```
Sau đó mở trình duyệt và truy cập: **`http://localhost:8000`**

**Cách 2: Dùng Apache của XAMPP**
Đặt thư mục dự án trong `C:\xampp\htdocs\ltweb-doan-nhom02` và mở: **`http://localhost/ltweb-doan-nhom02/`**

---

## 🔐 Tài khoản thử nghiệm (Mật khẩu chung: `matkhau123`)

Mọi mật khẩu đều được băm bằng thuật toán an toàn Bcrypt (`password_hash`) trong bảng `nguoi_dung`:

| Email đăng nhập | Mật khẩu | Vai trò | Quyền hạn và Mô tả |
|:---|:---:|:---:|:---|
| **`admin@ued.vn`** | `matkhau123` | `quan_tri` | Quản trị viên hệ thống: Truy cập `quan-tri.php`, thêm/sửa/xóa giáo án, xem liên hệ, xem toàn bộ đơn hàng và cập nhật trạng thái đơn. |
| **`lan@ued.vn`** | `matkhau123` | `khach_hang` | Học viên / Khách hàng: Đã có sẵn 1 đơn hàng mẫu trong CSDL để kiểm thử xem chi tiết đơn và phân quyền. |
| **`an@ued.vn`** | `matkhau123` | `khach_hang` | Học viên / Khách hàng: Đã có sẵn 1 đơn hàng mẫu trong CSDL để kiểm thử ca chặn xem trộm đơn hàng người khác (Ca 8). |

---

## 🗺️ Bảng 1: Sáu chức năng bắt buộc (Chức năng – URL – Tệp PHP)

| STT | Trang | Chức năng | Kỹ thuật bắt buộc | Tệp PHP | Người phụ trách |
|:---:|:---|:---|:---|:---|:---|
| **1** | `index.php`<br>`chi-tiet-giao-an.php`<br>`gio-hang.php`<br>`ve-chung-toi.php` | Đọc dữ liệu từ MySQL: trang chủ (mục mới nhất, đã xem gần đây qua Cookie); chi tiết kèm tên danh mục (JOIN) và 4 mục cùng loại; giỏ hàng (chống N+1 bằng `WHERE id IN (...)`); liên hệ lưu vào bảng `lien_he`. | `db()` Singleton; lớp truy cập nhận PDO; JOIN; `WHERE id IN (...)`; id sai trả 404; không truy vấn lặp; upload ảnh an toàn qua `finfo`. | `index.php`<br>`chi-tiet-giao-an.php`<br>`gio-hang.php`<br>`ve-chung-toi.php`<br>`src/Data/KhoLienHe.php` | **Lê Hoàng Bảo** |
| **2** | `kho-hoc-lieu.php` | Tìm theo từ khóa (có dấu, không dấu, chữ hoa/thường); lọc theo danh mục và giá tối đa; sắp xếp; phân trang; giữ bộ lọc khi đổi trang; báo khi không có kết quả. | `LIKE` + `addcslashes`; `WHERE` dựng từ mảng điều kiện & tham số; `ORDER BY` theo Whitelist an toàn; `COUNT(*)` + `LIMIT ? OFFSET ?`; `http_build_query`. | `kho-hoc-lieu.php`<br>`src/Data/KhoGiaoAn.php` | **Nguyễn Cảnh Tấn** |
| **3** | `quan-tri.php`<br>`them-giao-an.php`<br>`sua-giao-an.php`<br>`xoa-giao-an.php` | Quản trị thực thể chính (Giáo án): danh sách có phân trang; thêm; sửa (điền sẵn dữ liệu cũ); xóa có hộp thoại xác nhận; xem các liên hệ đã nhận (mới nhất trước). | `yeuCauQuyen('quan_tri')`; hàm `kiemTraGiaoAn()` dùng chung; `INSERT` + `lastInsertId`, `UPDATE`, `DELETE` chỉ nhận POST; bắt lỗi khóa ngoại MySQL 1451; upload an toàn; PRG + Flash. | `quan-tri.php`<br>`them-giao-an.php`<br>`sua-giao-an.php`<br>`xoa-giao-an.php`<br>`inc/kiem-tra.php` | **Lê Thị Xuân Châu** |
| **4** | `dang-ky.php`<br>`dang-nhap.php`<br>`dang-xuat.php` | Đăng ký, đăng nhập, đăng xuất với bảng `nguoi_dung`; header hiện tên người dùng và menu Quản trị đúng vai trò; bảo vệ chống tấn công leo thang quyền và Session Fixation. | `password_hash`, `password_verify`, `password_needs_rehash`; email UNIQUE + bắt lỗi 1062; không nhận `vai_tro` từ form; một thông báo lỗi chung; `session_regenerate_id(true)`; Session chỉ giữ `uid`. | `dang-ky.php`<br>`dang-nhap.php`<br>`dang-xuat.php`<br>`inc/xac-thuc.php` | **Nguyễn Tiến Thịnh** |
| **5** | `dat-hang.php`<br>`don-hang.php` | Đặt hàng từ giỏ trong Giao dịch (MySQL Transaction); "Đơn hàng của tôi": danh sách đơn kèm tổng tiền, xem chi tiết; quản trị viên xem mọi đơn và đổi trạng thái. | `beginTransaction`, `commit`, `rollBack`; `UPDATE ... WHERE ton_kho >= ?` và `rowCount()`; `chi_tiet_don` lưu đơn giá lúc đặt; JOIN + GROUP BY; chỉ đơn của mình (`AND nguoi_dung_id = ?`); Whitelist trạng thái. | `inc/don-hang.php`<br>`dat-hang.php`<br>`don-hang.php` | **Nguyễn Tiến Thịnh & Lê Hoàng Bảo** |
| **6** | `api/giao-an.php`<br>`api/gio-hang.php`<br>`js/tim-kiem.js`<br>`js/gio-hang.js` | REST API trả JSON: tìm kiếm, xem chi tiết, xem và thêm vào giỏ; tìm kiếm tức thời live search ở `kho-hoc-lieu.php`; thêm vào giỏ không tải lại trang, cập nhật badge ngay. | `Content-Type: application/json`; mã 200, 400, 404, 405, 422, 500; `php://input`; `fetch` + Debounce 300ms; `textContent`; tắt JavaScript form vẫn hoạt động bình thường. | `api/giao-an.php`<br>`api/gio-hang.php`<br>`js/tim-kiem.js`<br>`js/gio-hang.js`<br>`api.http` | **Nguyễn Cảnh Tấn & Lê Thị Xuân Châu** |

---

## 📡 Bảng các Endpoint REST API (`api/`)

Kiểm thử toàn bộ các request bằng tệp `api.http` (dùng VS Code extension REST Client):

| Phương thức | Endpoint | Tham số / Body | Mã HTTP | Mô tả phản hồi |
|:---:|:---|:---|:---:|:---|
| **GET** | `/api/giao-an.php?q=python` | Query `q`: Từ khóa tìm kiếm | `200 OK` | Trả về JSON danh sách các giáo án phù hợp: `{"du_lieu": [...]}` |
| **GET** | `/api/giao-an.php?id=1` | Query `id`: ID giáo án | `200 OK` | Trả về JSON chi tiết giáo án (chỉ các trường công khai) |
| **GET** | `/api/giao-an.php?id=999999` | Query `id`: Không tồn tại | `404 Not Found` | `{"loi": "Không tìm thấy giáo án"}` |
| **GET** | `/api/giao-an.php?id=abc` | Query `id`: Sai định dạng số | `400 Bad Request` | `{"loi": "ID giáo án không hợp lệ"}` |
| **GET** | `/api/gio-hang.php` | *(Không)* | `200 OK` | Trả về số món và danh sách giáo án trong session: `{"so_mon": ..., "gio_hang": ...}` |
| **POST** | `/api/gio-hang.php` | JSON: `{"id": 1, "sl": 1}` | `200 OK` | Thêm vào giỏ thành công: `{"thanh_cong": true, "so_mon": ...}` |
| **POST** | `/api/gio-hang.php` | Body không phải JSON (vd: `{id: 1}`) | `400 Bad Request` | `{"loi": "Dữ liệu yêu cầu phải là JSON hợp lệ"}` |
| **POST** | `/api/gio-hang.php` | JSON: `{"id": 1, "sl": -3}` | `422 Unprocessable` | `{"loi": "Số lượng phải là số nguyên dương lớn hơn 0"}` |
| **POST** | `/api/gio-hang.php` | JSON: `{"id": 999999, "sl": 1}` | `404 Not Found` | `{"loi": "Giáo án không tồn tại"}` |
| **POST** | `/api/gio-hang.php` | JSON: `{"id": 2, "sl": 99999}` | `422 Unprocessable` | `{"loi": "Số lượng yêu cầu vượt quá tồn kho hiện có..."}` |
| **DELETE** | `/api/gio-hang.php` | *(Phương thức không hỗ trợ)* | `405 Method Not Allowed` | Header `Allow: GET, POST`; `{"loi": "Phương thức không được hỗ trợ"}` |

---

## 👤 Danh sách trang cá nhân thành viên (Phần B)

| Thành viên | MSSV | Đường dẫn trang cá nhân | Bảng MySQL riêng (`tv_...`) |
|:---|:---:|:---|:---:|
| **Lê Hoàng Bảo** | `3120124003` | `/thanhvien/3120124003_bao/gioithieu.php` | `tv_luu_but` (Sổ lưu bút phân trang, chỉ chủ trang xóa) |
| **Nguyễn Cảnh Tấn** | `3120124027` | `/thanhvien/3120124027_tan/gioithieu.php` | `tv_luot_xem`, `tv_du_an_tan` (Bộ đếm theo ngày `ON DUPLICATE KEY UPDATE`, tra cứu dự án) |
| **Lê Thị Xuân Châu** | `3120124006` | `/thanhvien/3120124006_chau/gioithieu.php` | `tv_du_an` (Danh sách dự án, chủ trang thêm/xóa) |
| **Nguyễn Tiến Thịnh** | `3120222126` | `/thanhvien/3120222126_thinh/gioithieu.php` | `tv_yeu_cau_cv` (Form gửi yêu cầu nhận CV lưu DB, phân trang danh sách) |
