# ITeduShare — Nền tảng chia sẻ giáo án Tin học (Nhóm 02)

> **Nhóm thực hiện:** Nhóm 02  

---

## Danh sách thành viên nhóm

| STT | MSSV | Họ và tên | Vai trò | Chức năng phụ trách chính |
|:---:|:---:|:---|:---|:---|
| 1 | **3120124003** | **Lê Hoàng Bảo** | Nhóm trưởng | Chức năng 1 (Cấu trúc chung, Header, Footer) & Chức năng 5 (Lưu giáo án Session) |
| 2 | **3120124006** | **Lê Thị Xuân Châu** | Thành viên | Chức năng 3 (Chi tiết giáo án + Cookie đã xem gần đây) |
| 3 | **3120124027** | **Nguyễn Thành Tấn** | Thành viên | Chức năng 2 (Kho học liệu, Tìm kiếm, Lọc, Sắp xếp) |
| 4 | **3120222126** | **Lê Đức Thịnh** | Thành viên | Chức năng 4 (Đóng góp tài liệu, Upload file) & Chức năng 5 (Hỗ trợ danh sách lưu) |

---

## 💻 Yêu cầu môi trường

- **PHP:** Phiên bản `≥ 8.1` (Phiên bản khuyến nghị: `PHP 8.2+` | Môi trường kiểm thử hiện tại: `PHP 8.2.12`).
- **Composer:** Quản lý gói thư viện và chuẩn autoloading PSR-4 (Khuyến nghị: `Composer 2.x` | Hiện tại: `Composer 2.10.3`).
- **Các tiện ích mở rộng (PHP Extensions):**
  - `fileinfo` (bắt buộc để kiểm tra định dạng MIME file an toàn khi upload).
  - `mbstring`, `json`, `session`.
- **Trình duyệt:** Google Chrome, Microsoft Edge, Mozilla Firefox hoặc bất kỳ trình duyệt hiện đại nào hỗ trợ HTML5/CSS3.

---

## 🚀 Hướng dẫn cài đặt và khởi chạy

### Bước 1: Tải mã nguồn về máy
```bash
git clone https://github.com/HoangBao233/ltweb-doan-nhom02.git
cd ltweb-doan-nhom02
```

### Bước 2: Cài đặt các gói phụ thuộc (Composer)
Chạy lệnh sau tại thư mục gốc để nạp chuẩn Autoloading PSR-4 (`App\` ánh xạ vào `src/`):
```bash
composer install
```
*(Nếu cập nhật thêm class hoặc namespace mới, chạy `composer dump-autoload`)*

### Bước 3: Đảm bảo quyền ghi các thư mục dữ liệu
Đảm bảo các thư mục sau có quyền ghi dữ liệu (`write permission`):
- `storage/` — Lưu trữ dữ liệu log, lưu bút, số lượt xem.
- `logs/` — Lưu file nhật ký lỗi `logs/php-error.log`.
- `uploads/` — Lưu ảnh đại diện và giáo án do người dùng đóng góp.

---

### Bước 4: Khởi chạy dự án

Có 2 cách khởi chạy dự án:

#### Cách 1: Dùng máy chủ tích hợp của PHP (PHP Built-in Server - Khuyên dùng khi chấm bài)
Mở Terminal/PowerShell tại thư mục gốc của dự án và chạy lệnh:
```bash
php -S localhost:8000
```
Sau đó mở trình duyệt và truy cập: **`http://localhost:8000`**

#### Cách 2: Dùng XAMPP (Apache)
1. Đặt toàn bộ thư mục dự án `ltweb-doan-nhom02` vào thư mục `htdocs` của XAMPP (`C:\xampp\htdocs\ltweb-doan-nhom02`).
2. Mở ứng dụng **XAMPP Control Panel** và nhấn **Start** tại dịch vụ **Apache**.
3. Mở trình duyệt và truy cập: **`http://localhost/ltweb-doan-nhom02/`**

---

## 🔐 Tài khoản thử nghiệm trang quản trị

Website có phân quyền truy cập trang Quản trị (`quan-tri.php`) thông qua Session và bảo vệ bởi lớp middleware kiểm tra đăng nhập (`inc/bao-ve.php`).

- **Đường dẫn đăng nhập:** `/dang-nhap.php`
- **Tên đăng nhập (Username):** `admin`
- **Mật khẩu (Password):** `nhom02@ltweb`

> **Lưu ý bảo mật:** Mật khẩu được băm (hash) bằng thuật toán chuẩn `password_hash()` trong file `inc/tai-khoan.php`, được xác minh bằng `password_verify()` và tự động tái tạo Session ID (`session_regenerate_id(true)`) sau khi đăng nhập thành công để phòng chống tấn công Session Fixation.

---

## 🗺️ Bảng ánh xạ 7 đường dẫn URL chính sang các file PHP

| STT | Chức năng | Đường dẫn URL | Tệp PHP xử lý | Mô tả nghiệp vụ |
|:---:|:---|:---|:---|:---|
| 1 | **Trang chủ** | `/` hoặc `/index.php` | `index.php` | Hiển thị bài viết nổi bật, danh sách giáo án đã xem gần đây (Cookie) và widget thời tiết |
| 2 | **Kho học liệu** | `/kho-hoc-lieu.php` | `kho-hoc-lieu.php` | Tìm kiếm theo từ khóa, lọc theo cấp học (THCS/THPT), sắp xếp học liệu (SSR) |
| 3 | **Chi tiết giáo án** | `/chi-tiet-giao-an.php?id=1` | `chi-tiet-giao-an.php` | Đọc ID từ GET, ghi nhận cookie `da_xem`, xem chi tiết nội dung và video giáo án |
| 4 | **Liên hệ / Về chúng tôi** | `/ve-chung-toi.php` | `ve-chung-toi.php` | Giới thiệu dự án, xử lý form liên hệ và phản hồi thông tin |
| 5 | **Giáo án đã lưu (Giỏ hàng)**| `/gio-hang.php` | `gio-hang.php` | Quản lý danh sách bài học đã lưu bằng Session, tính tổng số lượng, xóa từng mục hoặc xóa hết |
| 6 | **Đăng nhập** | `/dang-nhap.php` | `dang-nhap.php` | Xác thực người dùng, lưu Session `$_SESSION['user']`, chuyển hướng sau đăng nhập |
| 7 | **Quản trị hệ thống** | `/quan-tri.php` | `quan-tri.php` | Khu vực dành riêng cho Quản trị viên, thống kê số lượng giáo án và tin nhắn liên hệ |

### Các đường dẫn bổ trợ khác:
- **Đóng góp tài liệu:** `/dong-gop-tai-lieu.php` (Xử lý form nộp giáo án, validate dữ liệu, upload file an toàn).
- **Đăng xuất:** `/dang-xuat.php` (Hủy session và quay về trang chủ).
- **Trang thành viên cá nhân:** 
  - Lê Hoàng Bảo: `/thanhvien/3120124003_bao/gioithieu.php`
  - Lê Thị Xuân Châu: `/thanhvien/3120124006_chau/gioithieu.php`
  - Nguyễn Thành Tấn: `/thanhvien/3120124027_tan/gioithieu.php`
  - Lê Đức Thịnh: `/thanhvien/3120222126_thinh/gioithieu.php`
- **Trang xử lý lỗi:** `404.php` (Không tìm thấy trang), `500.php` (Lỗi máy chủ nội bộ).

---

## 📁 Cấu trúc thư mục dự án

```text
ltweb-doan-nhom02/
├── composer.json               # Cấu hình Composer và ánh xạ PSR-4 autoloading
├── composer.lock               # Khóa phiên bản các gói phụ thuộc
├── .gitignore                  # Bỏ qua /vendor/, /logs/*, /storage/*, file tạm
├── README.md                   # Hướng dẫn cài đặt và thông tin dự án
│
├── index.php                   # Trang chủ website
├── kho-hoc-lieu.php            # Trang danh sách kho học liệu
├── chi-tiet-giao-an.php        # Trang chi tiết giáo án
├── ve-chung-toi.php            # Trang giới thiệu và liên hệ
├── dong-gop-tai-lieu.php       # Trang đóng góp tài liệu & upload
├── gio-hang.php                # Trang quản lý giáo án đã lưu
├── dang-nhap.php               # Trang đăng nhập quản trị
├── dang-xuat.php               # Đăng xuất hệ thống
├── quan-tri.php                # Trang bảng điều khiển quản trị
├── 404.php / 500.php           # Các trang thông báo lỗi chuẩn HTTP
│
├── inc/                        # Thư mục chứa các thành phần và hàm dùng chung
│   ├── config.php              # Cấu hình chung, session_start, timezone, bật error log
│   ├── ham.php                 # Hàm tiện ích: e() chống XSS, vnd() định dạng tiền tệ
│   ├── header.php              # Phần đầu trang và thanh Menu điều hướng chung
│   ├── footer.php              # Phần chân trang chung
│   ├── bao-ve.php              # Middleware bảo vệ trang yêu cầu quyền admin
│   └── tai-khoan.php           # Danh sách tài khoản băm mật khẩu
│
├── src/                        # Mã nguồn hướng đối tượng (PSR-4: App\)
│   ├── Models/
│   │   └── GiaoAn.php          # Lớp đối tượng Giáo án
│   ├── Data/
│   │   ├── KhoGiaoAn.php       # Đọc, lọc, sắp xếp, tìm kiếm giáo án từ file JSON
│   │   └── KhoLienHe.php       # Quản lý lưu trữ phản hồi liên hệ
│   └── Services/
│       └── DanhSachLuu.php     # Xử lý thêm, xóa, đếm giáo án đã lưu trong Session
│
├── data/                       # Dữ liệu tĩnh gốc
│   └── giao-an.json            # 12 bản ghi giáo án Tin học mẫu
├── storage/                    # Dữ liệu lưu trữ runtime (Lưu bút, đếm lượt xem)
├── uploads/                    # Tệp tin tải lên (ảnh giáo án, tài liệu người dùng)
├── logs/                       # Tệp ghi log hệ thống (php-error.log)
├── css/                        # Hệ thống CSS tổ chức theo 5 lớp chuẩn BEM & Flex/Grid
├── js/                         # JavaScript bổ trợ trải nghiệm người dùng (UX)
│   ├── main.js                 # Điều khiển Menu Hamburger Mobile
│   ├── yeu-thich.js            # Lưu trữ yêu thích LocalStorage đồng bộ giao diện
│   └── trang-chu.js            # Widget tích hợp API thời tiết
└── thanhvien/                  # Thư mục trang giới thiệu cá nhân của 4 thành viên
```

---

## 🛡️ Các tiêu chuẩn kỹ thuật & thực hành bảo mật đã áp dụng

1. **Phòng chống Cross-Site Scripting (XSS):** Mọi dữ liệu xuất ra màn hình HTML đều được lọc qua hàm `e(mixed $str): string` với cờ `ENT_QUOTES | ENT_SUBSTITUTE` chuẩn UTF-8.
2. **Post-Redirect-Get (PRG Pattern):** Tất cả các thao tác nhận dữ liệu POST (Lưu giáo án, gửi liên hệ, nộp bài, đăng nhập) đều được xử lý và lập tức chuyển hướng (`header("Location: ..."); exit;`), ngăn ngừa tuyệt đối lỗi lặp hành động khi người dùng nhấn F5.
3. **Bảo mật Upload tập tin:** Sử dụng tiện ích `finfo_file()` để xác thực định dạng MIME thực tế của tệp trên máy chủ (không phụ thuộc vào đuôi file hay `$_FILES['type']`), đặt tên file ngẫu nhiên an toàn bằng `uniqid()`.
4. **Kiểm soát Cookie và Session:** Cookie được thiết lập đầy đủ cờ bảo vệ `httponly: true`, `samesite: 'Lax'`, cookie thời hạn hợp lý; phiên đăng nhập được tái tạo ID an toàn.
5. **Clean Architecture & PSR-4:** Tách biệt rõ ràng tầng Giao diện (Presentation), Tầng Nghiệp vụ (Services) và Tầng Dữ liệu (Models/Data), tự động tải lớp qua Composer Autoload.

