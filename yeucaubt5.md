# HƯỚNG DẪN THỰC HIỆN BÀI TẬP NHÓM SỐ 5 — PHP
### Nhóm 02: Bảo · Tấn · Châu · Thịnh

---

## 📌 ÁNH XẠ TÊN FILE: Yêu cầu → Thực tế của nhóm

Đề bài dùng tên file ví dụ. Bảng dưới đây cho nhóm biết file nào trong yêu cầu = file nào thực tế:

| Tên trong yêu cầu | Tên thực tế của nhóm | Ghi chú |
|:---|:---|:---|
| `danh-sach.php` | `kho-hoc-lieu.php` | Đổi từ `kho-hoc-lieu.html` |
| `chi-tiet.php` | `chi-tiet-giao-an.php` | Đổi từ `chi-tiet-giao-an.html` |
| `lien-he.php` | `ve-chung-toi.php` | Đổi từ `ve-chung-toi.html` (trang này có form liên hệ) |
| `gioi-thieu.php` | `dong-gop-tai-lieu.php` | Đổi từ `dong-gop-tai-lieu.html` |
| `index.php` | `index.php` | Đổi từ `index.html` |
| `gio-hang.php` | `gio-hang.php` | File mới, tương đương "giỏ yêu thích / đăng ký khoá học" |
| `san-pham.json` | `data/giao-an.json` | Đã có sẵn 12 thực thể |
| `SanPham` (lớp) | `GiaoAn` | Lớp thực thể của đề tài nhóm |
| `KhoSanPham` (lớp) | `KhoGiaoAn` | Lớp truy cập dữ liệu |
| `GioHang` (lớp) | `DanhSachLuu` | Tương đương "giỏ hàng" — danh sách giáo án đã lưu |

---

## 🗂️ CẤU TRÚC THƯ MỤC SAU KHI HOÀN THÀNH

```
ltweb-doan-nhom02/
├── index.php                   ← đổi từ index.html
├── kho-hoc-lieu.php            ← đổi từ kho-hoc-lieu.html
├── chi-tiet-giao-an.php        ← đổi từ chi-tiet-giao-an.html
├── ve-chung-toi.php            ← đổi từ ve-chung-toi.html
├── dong-gop-tai-lieu.php       ← đổi từ dong-gop-tai-lieu.html
├── gio-hang.php                ← FILE MỚI
├── dang-nhap.php               ← FILE MỚI
├── dang-xuat.php               ← FILE MỚI
├── quan-tri.php                ← FILE MỚI
├── 404.php                     ← FILE MỚI
├── 500.php                     ← FILE MỚI
├── inc/
│   ├── config.php              ← FILE MỚI (nạp đầu tiên mọi trang)
│   ├── ham.php                 ← FILE MỚI (hàm e(), vnd()...)
│   ├── header.php              ← FILE MỚI (tách từ HTML cũ)
│   ├── footer.php              ← FILE MỚI (tách từ HTML cũ)
│   ├── bao-ve.php              ← FILE MỚI (chặn trang chưa đăng nhập)
│   └── tai-khoan.php           ← FILE MỚI (tài khoản thử đã băm)
├── src/
│   ├── Models/
│   │   └── GiaoAn.php          ← FILE MỚI (lớp thực thể)
│   ├── Data/
│   │   ├── KhoGiaoAn.php       ← FILE MỚI (đọc giao-an.json)
│   │   └── KhoLienHe.php       ← FILE MỚI (đọc/ghi lien-he.jsonl)
│   └── Services/
│       └── DanhSachLuu.php     ← FILE MỚI (bọc $_SESSION)
├── data/
│   └── giao-an.json            ← GIỮ NGUYÊN từ bài 4
├── storage/
│   ├── .htaccess               ← FILE MỚI (nội dung: Require all denied)
│   └── lien-he.jsonl           ← tự tạo khi chạy
├── logs/
│   ├── .htaccess               ← FILE MỚI (Require all denied)
│   └── php-error.log           ← tự tạo khi chạy
├── uploads/
│   └── .gitkeep                ← FILE MỚI (giữ thư mục trong git)
├── css/  js/  images/          ← GIỮ NGUYÊN
├── kiemtra/                    ← GIỮ NGUYÊN + thêm ảnh mới
├── thanhvien/                  ← mỗi người đổi gioithieu.html → .php
├── composer.json               ← FILE MỚI
├── composer.lock               ← tự tạo sau composer install
├── .gitignore                  ← CẬP NHẬT thêm /vendor/, /logs/*, /storage/*
└── README.md                   ← CẬP NHẬT thêm hướng dẫn PHP
```

---

## 📋 PHÂN CÔNG CÔNG VIỆC (4 người)

### Tổng quan phân công

| Người | Phần A (Bảng 1) | Phần B (Trang cá nhân) |
|:---:|:---|:---|
| **Bảo** | Chức năng 1 — Khung trang dùng chung | 2 chức năng riêng trên trang Bảo |
| **Tấn** | Chức năng 2 — Danh sách + Tìm kiếm/Lọc | 2 chức năng riêng trên trang Tấn |
| **Châu** | Chức năng 3 — Chi tiết + Cookie đã xem | 2 chức năng riêng trên trang Châu |
| **Thịnh** | Chức năng 4 — Form Liên hệ + Upload | 2 chức năng riêng trên trang Thịnh |
| **Bảo + Thịnh** | Chức năng 5 — Giỏ lưu (Session) | — |
| **Tấn + Châu** | Chức năng 6 — Đăng nhập/Quản trị | — |

> **Ghi nhớ:** Mỗi người phải `git commit` từ tài khoản GitHub cá nhân của mình, đặc biệt ở phần B. Commit từ 1 tài khoản duy nhất sẽ bị trừ điểm.

---

## 🚀 CÁC BƯỚC THỰC HIỆN THEO THỨ TỰ

---

### BƯỚC 0 — Chuẩn bị môi trường (Cả nhóm, 1 người làm)

#### 0.1. Gắn thẻ cho commit bài 4 (QUAN TRỌNG — làm trước khi sửa bất cứ thứ gì)
```bash
# Lấy mã commit cuối của bài 4
git log --oneline -5

# Gắn thẻ btn4 cho commit đó (thay <ma-commit> bằng mã thực)
git tag btn4 <ma-commit>
git push origin btn4
```

#### 0.2. Cài Composer
- Tải tại https://getcomposer.org/download/
- Sau khi cài, kiểm tra: `composer --version`

#### 0.3. Tạo file `composer.json` ở thư mục gốc
```json
{
    "name": "nhom02/ltweb-doan",
    "require": { "php": ">=8.1" },
    "autoload": { "psr-4": { "App\\": "src/" } }
}
```
Sau đó chạy: `composer install`

#### 0.4. Cập nhật `.gitignore`
Thêm vào `.gitignore` (tạo mới nếu chưa có):
```gitignore
/vendor/
/logs/*
!/logs/.htaccess
/storage/*
!/storage/.htaccess
/uploads/*
!/uploads/.gitkeep
```

#### 0.5. Tạo các thư mục và file bảo vệ
```bash
mkdir storage logs uploads
# Tạo file .htaccess trong storage/ và logs/
echo "Require all denied" > storage/.htaccess
echo "Require all denied" > logs/.htaccess
# Tạo file giữ thư mục uploads trong git
New-Item uploads/.gitkeep
```

---

### BƯỚC 1 — PHẦN DÙNG CHUNG (Bảo phụ trách, ưu tiên cao nhất)

> Bước này phải làm trước vì mọi trang khác đều phụ thuộc vào nó.

#### 1.1. Tạo `inc/config.php`
```php
<?php
// inc/config.php — nạp đầu tiên ở mọi trang; gọi session, autoload, log
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/ham.php';

const MOI_TRUONG = 'dev'; // Đổi thành 'prod' khi nộp bài thật

error_reporting(E_ALL);
ini_set('display_errors', MOI_TRUONG === 'dev' ? '1' : '0');
ini_set('log_errors', '1');
ini_set('error_log', __DIR__ . '/../logs/php-error.log');

session_start();

set_exception_handler(function (Throwable $e) {
    error_log($e->getMessage());
    if (MOI_TRUONG === 'prod') {
        http_response_code(500);
        require __DIR__ . '/../500.php';
        exit;
    }
    throw $e;
});
```

#### 1.2. Tạo `inc/ham.php`
```php
<?php
// inc/ham.php — hàm tiện ích dùng chung toàn website

// Thoát HTML an toàn — dùng thay vì echo trực tiếp
function e(mixed $str): string {
    return htmlspecialchars((string)$str, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

// Định dạng tiền VNĐ
function vnd(int $so): string {
    return number_format($so, 0, ',', '.') . ' ₫';
}
```

#### 1.3. Tạo `inc/header.php`
Tách phần `<header>` từ file HTML cũ, bổ sung logic PHP:
- Hiển thị menu, đánh dấu trang đang xem bằng `aria-current="page"`
- Hiển thị số lượng giáo án đã lưu (từ Session)
- Hiển thị tên người đăng nhập / nút Đăng nhập

#### 1.4. Tạo `inc/footer.php`
Tách phần `<footer>` từ file HTML cũ.

#### 1.5. Tạo `404.php` và `500.php`
```php
<?php // 404.php
require __DIR__ . '/inc/config.php';
http_response_code(404);
$tieuDe = 'Không tìm thấy trang';
require __DIR__ . '/inc/header.php';
?>
<main>
  <h1>Oops! Trang không tồn tại (404)</h1>
  <p>Trang bạn tìm kiếm không tồn tại. <a href="index.php">Về trang chủ</a></p>
</main>
<?php require __DIR__ . '/inc/footer.php'; ?>
```

#### 1.6. Đổi các file HTML → PHP bằng `git mv`
```bash
git mv index.html index.php
git mv kho-hoc-lieu.html kho-hoc-lieu.php
git mv chi-tiet-giao-an.html chi-tiet-giao-an.php
git mv ve-chung-toi.html ve-chung-toi.php
git mv dong-gop-tai-lieu.html dong-gop-tai-lieu.php
```
**Sau đó sửa tất cả các liên kết** trong file từ `.html` thành `.php`.

---

### BƯỚC 2 — CÁC LỚP PHP (Bảo + Tấn phụ trách)

#### 2.1. Tạo `src/Models/GiaoAn.php` (Lớp thực thể)
```php
<?php
// src/Models/GiaoAn.php — thực thể Giáo Án, ánh xạ từ giao-an.json
namespace App\Models;

class GiaoAn
{
    public function __construct(
        public readonly int    $id,
        public readonly string $ten,
        public readonly string $mo_ta,
        public readonly string $cap_hoc,
        public readonly int    $gia,
        public readonly string $hinh_anh,
        public readonly string $video_url,
    ) {}

    // Tạo đối tượng từ một mục của mảng JSON
    public static function tuMang(array $d): self
    {
        return new self(
            id:        (int)$d['id'],
            ten:       $d['ten']       ?? '',
            mo_ta:     $d['mo_ta']     ?? '',
            cap_hoc:   $d['cap_hoc']   ?? '',
            gia:       (int)($d['gia'] ?? 0),
            hinh_anh:  $d['hinh_anh']  ?? '',
            video_url: $d['video_url'] ?? '',
        );
    }
}
```

#### 2.2. Tạo `src/Data/KhoGiaoAn.php` (Lớp truy cập dữ liệu)
Dựa theo Gợi ý 3 trong file yêu cầu, đổi `SanPham` → `GiaoAn`, `KhoSanPham` → `KhoGiaoAn`, đường dẫn JSON là `data/giao-an.json`. Thêm phương thức `timKiem(string $q, string $capHoc, string $sapXep): array` cho trang danh sách.

#### 2.3. Tạo `src/Data/KhoLienHe.php` (Lớp lưu liên hệ)
Copy nguyên từ Gợi ý 5 trong file yêu cầu, đổi đường dẫn tệp thành `storage/lien-he.jsonl`.

#### 2.4. Tạo `src/Services/DanhSachLuu.php` (Tương đương GioHang)
Bọc `$_SESSION['danh_sach_luu']` — lưu mảng `[id => 1]` của giáo án người dùng đã lưu.
Cần các phương thức: `them(int $id)`, `xoa(int $id)`, `xoaHet()`, `soMon(): int`, `tatCa(): array`.

---

### BƯỚC 3 — SÁU CHỨC NĂNG BẢNG 1

---

#### ✅ Chức năng 1 — Khung trang (BẢO)
**File cần làm:** `inc/config.php`, `inc/ham.php`, `inc/header.php`, `inc/footer.php`, `inc/bao-ve.php`, `404.php`, `500.php`

**Cách làm:**
- Sửa lại tất cả 5 trang `.php` chính: đầu mỗi file là `require __DIR__ . '/inc/config.php';`, rồi gán `$tieuDe` và `$trang`, rồi `require __DIR__ . '/inc/header.php';`, cuối file `require __DIR__ . '/inc/footer.php';`
- `header.php` dùng `foreach` để in menu, kiểm tra `$trang === $tep` để thêm `aria-current="page"`
- Tạo `inc/tai-khoan.php` với tài khoản thử:
```php
<?php
// inc/tai-khoan.php — tài khoản thử cho trang quản trị
// KHÔNG đưa mật khẩu thật vào đây
const TAI_KHOAN = [
    'admin' => password_hash('nhom02@ltweb', PASSWORD_DEFAULT),
];
```
- Tạo `inc/bao-ve.php`:
```php
<?php
// inc/bao-ve.php — require file này ở đầu trang cần đăng nhập
if (empty($_SESSION['user'])) {
    header('Location: ' . ($goc ?? '') . 'dang-nhap.php');
    exit;
}
```

---

#### ✅ Chức năng 2 — Danh sách (TẤN)
**File cần làm:** `kho-hoc-lieu.php`

**Cách làm:**
- Xoá toàn bộ JS fetch cũ trong `js/trang-danh-sach.js` (thay = PHP)
- Phần xử lý ở đầu file:
  1. Đọc tham số GET: `$_GET['q']` (từ khoá), `$_GET['cap_hoc']` (lọc), `$_GET['sx']` (sắp xếp)
  2. Kiểm tra `cap_hoc` phải thuộc `['Tất cả', 'THCS', 'THPT']`; `sx` phải thuộc `['ten-az', 'gia-tang', 'gia-giam']`
  3. Gọi `$kho->timKiem($q, $capHoc, $sapXep)` → mảng kết quả
- Phần HTML: dùng `foreach` để render thẻ giáo án, `e()` cho mọi giá trị in ra
- Giữ lại lựa chọn trong form: ô tìm kiếm `value="<?= e($q) ?>"`, các `<option>` dùng `selected` nếu khớp
- Báo "Không tìm thấy" nếu mảng rỗng
- **URL chia sẻ được:** `kho-hoc-lieu.php?q=python&cap_hoc=THPT&sx=gia-tang`
- **Tắt JavaScript vẫn dùng được** vì form dùng method GET thông thường

---

#### ✅ Chức năng 3 — Chi tiết + Cookie đã xem (CHÂU)
**File cần làm:** `chi-tiet-giao-an.php`, thêm khối "Đã xem gần đây" vào `index.php`

**Cách làm:**
- Đọc `?id=` bằng `filter_var($_GET['id'] ?? '', FILTER_VALIDATE_INT)`
- Nếu id sai hoặc không tồn tại: `http_response_code(404); require '404.php'; exit;`
- Ghi cookie `da_xem` (tối đa 4 id) TRƯỚC mọi `echo` / `require header`:
```php
$cu  = array_map('intval', explode(',', $_COOKIE['da_xem'] ?? ''));
$moi = array_unique([$sp->id, ...array_filter($cu)]);
setcookie('da_xem', implode(',', array_slice($moi, 0, 4)), [
    'expires'  => time() + 30 * 24 * 3600,
    'path'     => '/',
    'httponly' => true,
    'samesite' => 'Lax',
]);
```
- In tên, mô tả, video, ảnh qua `e()`. Video dùng `<iframe src="<?= e($sp->video_url) ?>">`
- Nút "Lưu giáo án" → POST tới `gio-hang.php`
- Ở `index.php`: đọc cookie `da_xem`, kiểm tra từng id bằng `$kho->timTheoId($id)`, bỏ qua id lạ, hiện khối "Đã xem gần đây"

---

#### ✅ Chức năng 4 — Form Liên hệ + Upload (THỊNH)
**File cần làm:** `ve-chung-toi.php`

**Cách làm:**
- Xoá phần fetch JS cũ trong `js/trang-lien-he.js` (giữ lại phần validate client-side)
- Phần xử lý PHP:
  1. Khởi tạo `$du = ['hoten' => '', 'email' => '', 'sdt' => '', 'noidung' => '']` và `$loi = []`
  2. Kiểm tra `$_SERVER['REQUEST_METHOD'] === 'POST'`
  3. Validate từng ô: `trim`, `filter_var($email, FILTER_VALIDATE_EMAIL)`, `mb_strlen >= 10`...
  4. Upload ảnh: kiểm tra `$_FILES['anh']['error']`, kích thước ≤ 2MB, `finfo_file` xác nhận mime là `image/jpeg|png|webp`, đặt tên ngẫu nhiên `uniqid() . '.' . $ext`, `move_uploaded_file`
  5. Nếu không có lỗi: lưu vào `storage/lien-he.jsonl`, gán `$_SESSION['flash']`, `header('Location: ve-chung-toi.php'); exit;` (PRG)
- Phần HTML: điền lại `value="<?= e($du['hoten']) ?>"` cho từng ô, hiện `$loi['hoten']` dưới ô nếu có
- Flash message: đọc và xoá `$_SESSION['flash']` ngay đầu phần hiển thị

---

#### ✅ Chức năng 5 — Giỏ lưu trong Session (BẢO + THỊNH)
**File cần làm:** `gio-hang.php` (FILE MỚI)

**Tương đương "giỏ hàng" của đề tài nhóm:** Đây là danh sách giáo án người dùng đã lưu trong phiên làm việc (Session). Thay "số lượng" bằng trạng thái lưu (0/1).

**Cách làm:**
- Xử lý các POST action: `them` (thêm id vào session), `xoa` (xoá id), `xoa_het`
- Validate: id phải là số nguyên, phải tồn tại trong `KhoGiaoAn`
- Giá lấy từ dữ liệu JSON, không lấy từ form
- Sau mỗi POST thành công: `header('Location: gio-hang.php'); exit;` (PRG)
- Trang hiển thị danh sách giáo án đã lưu, số lượng hiển thị trên header

---

#### ✅ Chức năng 6 — Đăng nhập / Quản trị (TẤN + CHÂU)
**File cần làm:** `dang-nhap.php`, `dang-xuat.php`, `quan-tri.php`

**Cách làm:**

**`dang-nhap.php`:**
```php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tenDangNhap = trim($_POST['ten_dang_nhap'] ?? '');
    $matKhau     = $_POST['mat_khau'] ?? '';
    
    require __DIR__ . '/inc/tai-khoan.php';
    if (isset(TAI_KHOAN[$tenDangNhap]) && 
        password_verify($matKhau, TAI_KHOAN[$tenDangNhap])) {
        session_regenerate_id(true); // Cấp mã phiên mới!
        $_SESSION['user'] = $tenDangNhap;
        header('Location: quan-tri.php');
        exit;
    }
    // Báo lỗi chung, KHÔNG nói tên sai hay mật khẩu sai
    $loi = 'Sai tên đăng nhập hoặc mật khẩu.';
    // Ghi log
    error_log("Đăng nhập sai: user=$tenDangNhap, ip=" . $_SERVER['REMOTE_ADDR']);
}
```

**`dang-xuat.php`:**
```php
session_destroy();
header('Location: index.php');
exit;
```

**`quan-tri.php`:**
```php
require __DIR__ . '/inc/config.php';
require __DIR__ . '/inc/bao-ve.php'; // Chặn nếu chưa đăng nhập
// Đọc tất cả liên hệ từ KhoLienHe, hiển thị mới nhất trước
// Hiển thị ảnh đính kèm nếu có
```

---

### BƯỚC 4 — TRANG CÁ NHÂN (Phần B — mỗi người tự làm)

Mỗi người đổi `gioithieu.html` thành `gioithieu.php` và thêm **2 chức năng PHP**.

**Đầu file `gioithieu.php` của mỗi người:**
```php
<?php
// thanhvien/MSSV_ten/gioithieu.php — giới thiệu cá nhân + 2 chức năng PHP
require __DIR__ . '/../../inc/config.php';
$goc    = '../../';
$tieuDe = 'Họ Tên';
$trang  = '';
require __DIR__ . '/../../inc/header.php';
?>
```

**Gợi ý phân công chức năng riêng (không được trùng nhau):**

| Người | Chức năng 1 | Chức năng 2 |
|:---:|:---|:---|
| **Bảo** | Sổ lưu bút (form tên + lời nhắn, lưu `storage/3120124003_luubut.jsonl`, hiện 5 mới nhất) | Máy tính điểm học phần (0.2×A1 + 0.3×A2 + 0.5×A3, validate 0–10) |
| **Tấn** | Bộ đếm lượt xem (mỗi phiên tính 1 lần, lưu `storage/3120124027_luotxem.txt`) | Danh sách kỹ năng lọc theo nhóm bằng tham số GET (`?nhom=frontend`) |
| **Châu** | Giao diện sáng/tối ghi nhớ bằng cookie PHP (`setcookie('theme', 'dark', ...)`) | Ảnh đại diện tải lên an toàn (finfo, tên ngẫu nhiên, lưu `uploads/`) |
| **Thịnh** | Sổ lưu bút biến thể (form email + lời nhắn, kiểm tra email hợp lệ) | Bộ đếm lượt xem kết hợp session (`storage/3120222126_luotxem.txt`) |

> ⚠️ Chức năng Bảo và Thịnh hơi giống nhau — nếu thầy hỏi, giải thích khác nhau ở: Bảo không cần email, Thịnh bắt buộc nhập email hợp lệ và validate ở máy chủ. Nếu muốn khác hoàn toàn, Thịnh có thể làm **máy tính điểm** hoặc **danh sách kỹ năng**.

---

### BƯỚC 5 — KIỂM TRA JAVASCRIPT CÒN GIỮ GÌ, BỎ GÌ

**Giữ lại:**
- `js/main.js` — menu mobile
- `js/yeu-thich.js` — yêu thích localStorage (vẫn song song với Session)
- `js/trang-chu.js` — widget thời tiết API
- Phần validate phía client trong `js/trang-lien-he.js` (chỉ là lớp UX, PHP vẫn kiểm tra lại)

**Xoá/vô hiệu hoá:**
- Toàn bộ `js/trang-danh-sach.js` — PHP đã thay thế
- Toàn bộ `js/trang-chi-tiet.js` — PHP đã thay thế
- Phần `fetch POST` tới jsonplaceholder trong `js/trang-lien-he.js`

---

### BƯỚC 6 — KIỂM THỬ 12 CA (Bảng 3)

Thực hiện từng ca dưới đây, chụp màn hình, điền "Đạt/Không đạt" vào Bảng 3:

| Ca | Cách thực hiện |
|:--:|:---|
| 1 | DevTools → Elements → thêm `novalidate` vào `<form>` → để trống mọi ô → bấm Gửi |
| 2 | Điền email "lan@ued", SĐT "abc", các ô khác hợp lệ → Gửi |
| 3 | Tạo file text đặt tên `gia-mao.jpg` → đính kèm vào form liên hệ → Gửi |
| 4 | Đính kèm ảnh thật nhưng > 2MB → Gửi |
| 5 | Gửi liên hệ hợp lệ → bấm F5 ở trang kết quả → kiểm tra `storage/lien-he.jsonl` |
| 6 | Nhập `"><script>alert(1)</script>` vào ô tìm kiếm → xem kết quả |
| 7 | Mở `chi-tiet-giao-an.php?id=abc` và `?id=999999` và `?id=` |
| 8 | Dùng DevTools sửa ô số lượng thành 0, -3, 1000 rồi submit → kiểm tra |
| 9 | Thêm 2 giáo án vào giỏ → xoá 1 → tải lại → chuyển trang → kiểm tra số đếm |
| 10 | Mở `quan-tri.php` chưa đăng nhập → đăng nhập sai → đăng nhập đúng → đăng xuất |
| 11 | DevTools → Application → Cookies → sửa `da_xem` thành `abc,-1,999999` → mở trang chủ |
| 12 | Đổi `MOI_TRUONG = 'prod'` → đổi tên `data/giao-an.json` → mở `kho-hoc-lieu.php` |

---

### BƯỚC 7 — CHẤT LƯỢNG & KIỂM TRA CUỐI

**W3C Validator (0 lỗi HTML):**
- Mở trang → Ctrl+U → chép HTML → dán vào https://validator.w3.org/nu/#textarea
- Làm cho 5 trang chính: `index.php`, `kho-hoc-lieu.php`, `chi-tiet-giao-an.php`, `ve-chung-toi.php`, `gio-hang.php`

**Lighthouse Accessibility ≥ 90 (Mobile):**
- F12 → Lighthouse → Mobile → chọn Accessibility → Generate report
- Phải đạt ≥ 90 cho: `index.php`, `kho-hoc-lieu.php`, `ve-chung-toi.php`

**Kiểm tra không có Warning PHP:**
- Mở `logs/php-error.log` → phải trống hoàn toàn sau khi thao tác mọi chức năng

---

### BƯỚC 8 — CẬP NHẬT README.md

```markdown
# Kho học liệu — Nhóm 02

## Yêu cầu môi trường
- PHP ≥ 8.1
- Composer

## Cài đặt và chạy
```bash
composer install
php -S localhost:8000
```

## Tài khoản thử trang quản trị
- Tên đăng nhập: `admin`
- Mật khẩu: `nhom02@ltweb`

## Chức năng – URL – Tệp PHP
| Chức năng | URL | Tệp |
|:---|:---|:---|
| Trang chủ | / | index.php |
| Kho học liệu | /kho-hoc-lieu.php | kho-hoc-lieu.php |
| Chi tiết giáo án | /chi-tiet-giao-an.php?id=1 | chi-tiet-giao-an.php |
| Liên hệ | /ve-chung-toi.php | ve-chung-toi.php |
| Danh sách đã lưu | /gio-hang.php | gio-hang.php |
| Đăng nhập | /dang-nhap.php | dang-nhap.php |
| Quản trị | /quan-tri.php | quan-tri.php |
```

---

## ⚠️ LƯU Ý QUAN TRỌNG

1. **`setcookie()` và `header()` phải gọi TRƯỚC mọi output** (trước khi `require header.php`). Nếu không, PHP sẽ báo lỗi "Cannot modify header information - headers already sent".

2. **Mọi dữ liệu in ra HTML phải qua `e()`** — không bao giờ echo trực tiếp `$_GET`, `$_POST`, hay dữ liệu từ JSON/file.

3. **Sau POST thành công phải redirect (PRG)** — `header('Location: ...'); exit;` — để F5 không gửi lại form.

4. **`session_regenerate_id(true)`** sau khi đăng nhập thành công để chống tấn công Session Fixation.

5. **Upload ảnh:** phải dùng `finfo_file()` để kiểm tra mime thật (không tin đuôi file hay `$_FILES['type']`), đặt tên ngẫu nhiên `uniqid() . '.' . $ext`.

6. **GitHub Pages không chạy PHP** — từ bài này không push branch gh-pages nữa.

7. **Không commit `vendor/`** — chỉ commit `composer.json` và `composer.lock`.

