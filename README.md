# ITeduShare — Nền tảng chia sẻ giáo án & học liệu Tin học (Nhóm 02)

> **Học phần:** Thiết kế và Lập trình Web — Chương 7: Bảo mật ứng dụng web *(kế thừa Chương 6: PHP & MySQL)*  
> **Nhóm thực hiện:** Nhóm 02 (Khoa Toán – Tin, Trường ĐH Sư phạm – ĐH Đà Nẵng)

---

## 👥 Danh sách thành viên và phân công nhiệm vụ

| STT | Họ và tên | Mã sinh viên | Tài khoản GitHub | Nhiệm vụ chính (Chương 6 & Chương 7) | Tỷ lệ đóng góp |
|:---:|:---|:---:|:---:|:---|:---:|
| 1 | **Lê Hoàng Bảo** | `3120124003` | `HoangBao233` | **Nhóm trưởng**; Chương 6 (CSDL, Chức năng 1, Đồng phụ trách 5); Chương 7 (**Biện pháp 1:** CSRF, `dang-xuat.php` POST-only, `tests/bao-mat.http`, `README.md`, Rà soát Nhóm 5 & 9); Phần B (`tv_luu_but`, `tv_binh_chon`); Câu C1 | **25%** |
| 2 | **Nguyễn Cảnh Tấn** | `3120124027` | `beotan19-cpu` | Chương 6 (Chức năng 2, Chức năng 6 REST API); Chương 7 (**Biện pháp 3:** CSP & Header bảo mật, dọn script/style nội tuyến, hàm `chuoi()`, 4 lệnh grep Bảng 4, Rà soát Nhóm 2 & 3); Phần B (`tv_luot_xem`, `tv_du_an_tan`); Câu C2 | **25%** |
| 3 | **Lê Thị Xuân Châu** | `3120124006` | `XuanChau-06` | Chương 6 (Chức năng 3 Quản trị CRUD, ERD); Chương 7 (**Biện pháp 4 & 5:** `.env`, `.env.example`, `vlucas/phpdotenv`, `APP_DEBUG`, `.htaccess`, `uploads/.htaccess`, Rà soát Nhóm 1, 7 & 8); Phần B (`tv_du_an`); Câu C3 | **25%** |
| 4 | **Nguyễn Tiến Thịnh** | `3120222126` | `cowt1310` | Chương 6 (Chức năng 4 Auth, Chức năng 5 Transaction); Chương 7 (**Biện pháp 2, 6, 7:** Cookie/Session an toàn & timeout 30p, `database/tai-khoan.sql` quyền tối thiểu `nhom02_app`, chống brute-force `dang_nhap_sai`, `ghiLogBaoMat()`, Rà soát Nhóm 4 & 6); Phần B (`tv_yeu_cau_cv`); Câu C4 | **25%** |

---

## 💻 Yêu cầu môi trường

- **PHP:** Phiên bản `≥ 8.1` (bật extension `pdo_mysql`, `fileinfo`, `mbstring`, `session`).
- **Hệ quản trị CSDL:** MariaDB `≥ 10.4` hoặc MySQL `≥ 8.0` (mặc định của XAMPP, Laragon).
- **Composer:** Quản lý Autoloading PSR-4 (`App\` ánh xạ vào `src/`) và thư viện `vlucas/phpdotenv`.
- **Trình duyệt & Công cụ kiểm thử:** Google Chrome / Firefox (DevTools), REST Client extension cho VS Code (để chạy `api.http` và `tests/bao-mat.http`), `curl.exe`.

---

## 🚀 Cài đặt và chạy (Dành cho Giảng viên chấm bài)

### Bước 1: Cài đặt thư viện phụ thuộc (Composer)
Mở Terminal / Command Prompt tại thư mục gốc của dự án và chạy:
```bash
composer install
```

### Bước 2: Tạo và nạp Cơ sở dữ liệu `ltweb_nhom02`
**Cách 1: Dùng dòng lệnh (Khuyên dùng)**
```bash
mysql -u root < database/schema.sql
mysql -u root ltweb_nhom02 < database/seed.sql
```

**Cách 2: Dùng giao diện phpMyAdmin**
1. Mở `http://localhost/phpmyadmin/`.
2. Vào thẻ **Import** → chọn tệp `database/schema.sql` → nhấn **Import**.
3. Chọn cơ sở dữ liệu `ltweb_nhom02` vừa tạo → vào thẻ **Import** → chọn tệp `database/seed.sql` → nhấn **Import**.

### Bước 3: Sao chép cấu hình môi trường (`.env`)
```bash
cp .env.example .env    # Windows: copy .env.example .env
```
> **Lưu ý:** Nếu giá trị mật khẩu trong `.env` có dấu cách hoặc dấu `#`, phải đặt trong nháy kép (ví dụ `DB_PASSWORD="ab#12"`). Không bao giờ đưa `.env` lên Git.

### Bước 4: *(Tùy chọn bật quyền tối thiểu cho MySQL)*
Để chạy website bằng tài khoản CSDL giới hạn đúng 4 quyền `SELECT, INSERT, UPDATE, DELETE`:
1. Chạy tệp `database/tai-khoan.sql` bằng quyền `root` (trong **phpMyAdmin → SQL** hoặc `mysql -u root < database/tai-khoan.sql`).
2. Mở `.env` và đổi sang tài khoản vừa tạo:
   ```env
   DB_USERNAME=nhom02_app
   DB_PASSWORD=...
   ```
   *(Thay `...` bằng đúng mật khẩu đã đặt trong `database/tai-khoan.sql`.)*

### Bước 5: Khởi chạy website
- **Cách 1 — PHP built-in server:**
  ```bash
  php -S localhost:8000
  ```
  Truy cập **`http://localhost:8000`**. *(Lưu ý: `php -S` bỏ qua các tệp `.htaccess`.)*

- **Cách 2 — Apache của XAMPP:**
  Đặt thư mục dự án trong `C:\xampp\htdocs\ltweb-doan-nhom02` và truy cập **`http://localhost/ltweb-doan-nhom02/`**.
  *(Các quy tắc chặn tệp nội bộ `.htaccess` gốc và `uploads/.htaccess` chỉ có hiệu lực khi chạy trên Apache.)*

---

## 🔐 Tài khoản thử nghiệm (Mật khẩu chung: `matkhau123`)

Mọi mật khẩu đều được băm bằng Bcrypt (`password_hash`) trong bảng `nguoi_dung`:

| Email đăng nhập | Mật khẩu | Vai trò | Quyền hạn và Mô tả |
|:---|:---:|:---:|:---|
| **`admin@ued.vn`** | `matkhau123` | `quan_tri` | Quản trị viên hệ thống: Truy cập `quan-tri.php`, thêm/sửa/xóa giáo án, xem liên hệ, xem và cập nhật trạng thái đơn hàng. |
| **`lan@ued.vn`** | `matkhau123` | `khach_hang` | Học viên / Khách hàng: Có sẵn đơn hàng mẫu để kiểm thử chi tiết đơn và phân quyền. |
| **`an@ued.vn`** | `matkhau123` | `khach_hang` | Học viên / Khách hàng: Có sẵn đơn hàng mẫu để kiểm thử chặn xem trộm đơn hàng người khác. |
| **`bao@ued.vn`** | `matkhau123` | `quan_tri` | Tài khoản thành viên Lê Hoàng Bảo (`3120124003` — liên kết `mssv` trang cá nhân & quyền quản trị). |
| **`tan@ued.vn`** | `matkhau123` | `khach_hang` | Tài khoản thành viên Nguyễn Cảnh Tấn (`3120124027` — liên kết `mssv` trang cá nhân). |
| **`chau@ued.vn`** | `matkhau123` | `khach_hang` | Tài khoản thành viên Lê Thị Xuân Châu (`3120124006` — liên kết `mssv` trang cá nhân). |
| **`thinh@ued.vn`** | `matkhau123` | `khach_hang` | Tài khoản thành viên Nguyễn Tiến Thịnh (`3120222126` — liên kết `mssv` trang cá nhân). |

---

## 🛡️ Bảng Biện pháp – Tệp – Slide (Chương 7: Bảo mật ứng dụng web)

| TT | Biện pháp bảo mật | Tệp chính | Tài liệu / Slide tham chiếu | Người phụ trách |
|:---:|:---|:---|:---|:---|
| **1** | **Mã CSRF cho mọi form POST và `fetch`** (`csrfToken()`, `csrfField()`, `csrfHopLe()`, chặn POST tập trung, API trả `403` JSON, `X-CSRF-Token`, `dang-xuat.php` chỉ nhận `POST`) | `inc/csrf.php`, `inc/config.php`, `inc/header.php`, `js/gio-hang.js`, `dang-xuat.php`, mọi form POST | Chương 7 — **Slide 23, 24** *(Gợi ý 3)* | **Lê Hoàng Bảo** |
| **2** | **Cookie phiên và phiên an toàn** (`session.use_strict_mode = 1`, `HttpOnly`, `SameSite=Lax`, `Secure` khi HTTPS, timeout 30 phút, `dangNhap()` đổi ID phiên, `dangXuat()` xóa phiên & cookie) | `inc/config.php`, `inc/xac-thuc.php`, cookie `da_xem` / `theme` | Chương 7 — **Slide 25** *(Gợi ý 1)* | **Nguyễn Tiến Thịnh** |
| **3** | **Header bảo mật và Content Security Policy (CSP)** (`Content-Security-Policy` không `'unsafe-inline'`, `X-Content-Type-Options: nosniff`, `Referrer-Policy`, bỏ `X-Powered-By`, dọn script/style/on* nội tuyến, `chuoi()`) | `inc/config.php`, `inc/ham.php`, `css/*.css`, `js/*.js` | Chương 7 — **Slide 22, 26, 27** | **Nguyễn Cảnh Tấn** |
| **4** | **Cấu hình và bí mật trong tệp `.env`** (`vlucas/phpdotenv`, `APP_DEBUG`, `DB_HOST`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`, chặn `.env` khỏi Git) | `.env.example`, `.gitignore`, `composer.json`, `inc/config.php`, `inc/db.php` | Chương 7 — **Slide 29** *(Gợi ý 2)* | **Lê Thị Xuân Châu** |
| **5** | **Chặn tệp nội bộ và thực thi script trên Apache** (cấm liệt kê thư mục, trả `404` cho `.env`, `.git/`, `inc/`, `src/`, `vendor/`, `database/`, `logs/`, trả `403` cho `.sql`, `.log`, `composer.json`, cấm chạy PHP trong `uploads/`) | `.htaccess`, `uploads/.htaccess` | Chương 7 — **Slide 28, 30** *(Gợi ý 4)* | **Lê Thị Xuân Châu** |
| **6** | **Tài khoản CSDL quyền tối thiểu** (tài khoản `nhom02_app` chỉ có `SELECT, INSERT, UPDATE, DELETE` trên `ltweb_nhom02.*`, không có `DROP`/`CREATE`/`GRANT`) | `database/tai-khoan.sql`, `.env` | Chương 7 — **Slide 20** *(Gợi ý 4)* | **Nguyễn Tiến Thịnh** |
| **7** | **Chống đoán mật khẩu (Brute-force) và ghi log bảo mật** (bảng `dang_nhap_sai`, khóa tạm 15 phút sau 5 lần sai → HTTP `429`, `ghiLogBaoMat()` ghi `csrf_sai`, `cam_truy_cap`, `dang_nhap_sai`) | `database/schema.sql`, `inc/xac-thuc.php`, `inc/ham.php`, `dang-nhap.php`, `logs/bao-mat.log` | Chương 7 — **Slide 31, 32** *(Gợi ý 5, 6)* | **Nguyễn Tiến Thịnh** |

---

## 🗺️ Sáu chức năng cốt lõi (Chương 6 — giữ tương thích ngược)

| STT | Trang | Chức năng | Tệp PHP chính | Người phụ trách |
|:---:|:---|:---|:---|:---|
| **1** | `index.php`, `chi-tiet-giao-an.php`, `gio-hang.php`, `ve-chung-toi.php` | Đọc dữ liệu từ MySQL, chống N+1 bằng `WHERE id IN (...)`, cookie `da_xem`, liên hệ & upload `finfo` an toàn | `index.php`, `chi-tiet-giao-an.php`, `gio-hang.php`, `ve-chung-toi.php`, `src/Data/KhoLienHe.php` | **Lê Hoàng Bảo** |
| **2** | `kho-hoc-lieu.php` | Tìm kiếm `LIKE + addcslashes`, lọc giá & danh mục, sắp xếp Whitelist, phân trang `LIMIT/OFFSET` | `kho-hoc-lieu.php`, `src/Data/KhoGiaoAn.php` | **Nguyễn Cảnh Tấn** |
| **3** | `quan-tri.php`, `them-giao-an.php`, `sua-giao-an.php`, `xoa-giao-an.php` | Quản trị CRUD Giáo án (`yeuCauQuyen('quan_tri')`), bắt lỗi khóa ngoại MySQL `1451`, PRG + Flash | `quan-tri.php`, `them-giao-an.php`, `sua-giao-an.php`, `xoa-giao-an.php`, `inc/kiem-tra.php` | **Lê Thị Xuân Châu** |
| **4** | `dang-ky.php`, `dang-nhap.php`, `dang-xuat.php` | Đăng ký, đăng nhập (`password_hash`/`verify`), đăng xuất POST-only (`405` với GET), bảo vệ phiên | `dang-ky.php`, `dang-nhap.php`, `dang-xuat.php`, `inc/xac-thuc.php` | **Nguyễn Tiến Thịnh** |
| **5** | `dat-hang.php`, `don-hang.php` | Đặt hàng Transaction (`beginTransaction`/`commit`/`rollBack`), trừ kho nguyên tử, "Đơn hàng của tôi" | `inc/don-hang.php`, `dat-hang.php`, `don-hang.php` | **Nguyễn Tiến Thịnh & Lê Hoàng Bảo** |
| **6** | `api/giao-an.php`, `api/gio-hang.php`, `js/tim-kiem.js`, `js/gio-hang.js` | REST API trả JSON (`200/400/403/404/405/422/500`), Debounce live search, AJAX thêm giỏ hàng kèm `X-CSRF-Token` | `api/giao-an.php`, `api/gio-hang.php`, `js/tim-kiem.js`, `js/gio-hang.js`, `api.http` | **Nguyễn Cảnh Tấn & Lê Thị Xuân Châu** |

---

## 🧪 Bộ kiểm thử hồi quy (`api.http` & `tests/bao-mat.http`)

Dùng VS Code extension **REST Client** (chạy mục `### 0` để lấy `PHPSESSID` và `<meta name="csrf-token">` trước khi gửi các request `POST`):
- **`api.http`:** Kiểm thử đầy đủ các endpoint REST API của Chương 6 kèm xác thực CSRF `X-CSRF-Token` (các endpoint `GET /api/giao-an.php`, `GET/POST /api/gio-hang.php` trả `200`, `400`, `403`, `404`, `405`, `422`).
- **`tests/bao-mat.http`:** Bộ kiểm thử hồi quy 14 ca phủ định bảo mật Chương 7 (Header/Cookie, POST thiếu CSRF / `csrf[]=abc`, API thiếu `X-CSRF-Token`, GET `dang-xuat.php` / `xoa-giao-an.php` trả `405`, khách POST xóa trả `403`, Session Fixation, timeout phiên, XSS & CSP, tham số mảng `[]`, `.env` & `APP_DEBUG=false`, `.htaccess` trên Apache, `nhom02_app`, brute-force `429`, và các ca riêng đề tài).

---

## 👤 Danh sách trang cá nhân thành viên (Phần B)

| Thành viên | MSSV | Đường dẫn trang cá nhân | Bảng MySQL riêng (`tv_...`) |
|:---|:---:|:---|:---|
| **Lê Hoàng Bảo** | `3120124003` | `/thanhvien/3120124003_bao/gioithieu.php` | `tv_luu_but`, `tv_binh_chon` (Sổ lưu bút phân trang & xóa theo quyền chủ trang; Bình chọn công nghệ `ON DUPLICATE KEY UPDATE`) |
| **Nguyễn Cảnh Tấn** | `3120124027` | `/thanhvien/3120124027_tan/gioithieu.php` | `tv_luot_xem`, `tv_du_an_tan` (Bộ đếm theo ngày `ON DUPLICATE KEY UPDATE`, tra cứu dự án) |
| **Lê Thị Xuân Châu** | `3120124006` | `/thanhvien/3120124006_chau/gioithieu.php` | `tv_du_an` (Danh sách dự án, chủ trang thêm/xóa) |
| **Nguyễn Tiến Thịnh** | `3120222126` | `/thanhvien/3120222126_thinh/gioithieu.php` | `tv_yeu_cau_cv` (Form gửi yêu cầu nhận CV lưu DB, phân trang danh sách) |

---

## 🤖 Ghi chú sử dụng công cụ AI có trách nhiệm

Nhóm có sử dụng công cụ AI (LLM hỗ trợ lập trình) để tra cứu cú pháp PDO/SQL, cấu hình CSP/`.htaccess`, gợi ý ca kiểm thử phủ định và hoàn thiện tài liệu; toàn bộ cấu trúc CSDL, mã nguồn PHP/JS và các ca kiểm thử bảo mật đều được từng thành viên tự rà soát chéo, chạy thử trực tiếp trên máy cá nhân với website/CSDL của nhóm và làm chủ nội dung.
