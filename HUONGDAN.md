# HƯỚNG DẪN & PHÂN CÔNG CHI TIẾT BÀI TẬP THỰC HÀNH NHÓM SỐ 7
## Chương 7: Bảo mật ứng dụng web — Dự án ITeduShare (Nhóm 02)

> **Tài liệu gốc đối chiếu:** [`yeucau.md`](yeucau.md)  
> **Kho mã nguồn:** `c:\xampp\htdocs\ltweb-doan-nhom02` (`ltweb-doan-nhom02`)  
> **CSDL của nhóm:** `ltweb_nhom02` — Tài khoản quyền tối thiểu: `nhom02_app`  
> **Sản phẩm phải nộp (trước buổi thực hành tuần 13 trên `nhhai.net`):**
> 1. Báo cáo PDF đặt tên bắt buộc: **`Nhom02_Baitap7.pdf`** (xuất từ Word/Google Docs, không ảnh quét).
> 2. Link kho GitHub công khai (`ltweb-doan-nhom02`, có thẻ `btn6`, đủ lịch sử commit của 4 tài khoản, ghi rõ mã commit cuối cùng).
> 3. Tệp nén mã nguồn đặt tên bắt buộc: **`LTW_BTN7_Nhom02_code.zip`** (**KHÔNG** chứa `.git/`, `vendor/`, `.env`).

---

## MỤC LỤC NHANH
0. [**DÀNH CHO AI AGENT: Trình tự làm việc, phụ thuộc & cổng kiểm tra bắt buộc (Đọc đầu tiên!)**](#0-chỉ-dẫn-bắt-buộc-cho-ai-agent--trình-tự-làm-việc-đọc-trước-tiên)
1. [Quy tắc sống còn & Điều kiện tiên quyết](#1-quy-tắc-sống-còn--điều-kiện-tiên-quyết)
2. [Chuẩn bị trước khi chia nhánh / viết mã (Cả nhóm)](#2-chuẩn-bị-trước-khi-chia-nhánh--viết-mã-cả-nhóm)
3. [Bảng tổng hợp phân công công bằng (4 thành viên × 25%)](#3-bảng-tổng-hợp-phân-công-công-bằng-4-thành-viên--25)
4. [Chi tiết công việc từng thành viên (Từng tệp, từng hàm, từng lệnh test)](#4-chi-tiết-công-việc-từng-thành-viên)
   - [4.1. Thành viên 1: Lê Hoàng Bảo (Nhóm trưởng — `3120124003`)](#41-thành-viên-1-lê-hoàng-bảo-nhóm-trưởng--mssv-3120124003--github-hoangbao233)
   - [4.2. Thành viên 2: Nguyễn Cảnh Tấn (`3120124027`)](#42-thành-viên-2-nguyễn-cảnh-tấn--mssv-3120124027--github-beotan19-cpu)
   - [4.3. Thành viên 3: Lê Thị Xuân Châu (`3120124006`)](#43-thành-viên-3-lê-thị-xuân-châu--mssv-3120124006--github-xuanchau-06)
   - [4.4. Thành viên 4: Nguyễn Tiến Thịnh (`3120222126`)](#44-thành-viên-4-nguyễn-tiến-thịnh--mssv-3120222126--github-cowt1310)
5. [Bảng chú ý kỹ thuật đặc thù cho dự án ITeduShare (Đọc kỹ để không mất điểm oan)](#5-các-chú-ý-kỹ-thuật-đặc-thù-của-dự-án-itedushare)
6. [Quy cách báo cáo, kiểm thử hồi quy & Danh mục kiểm tra trước khi nộp](#6-quy-cách-báo-cáo-kiểm-thử-hồi-quy--checklist-trước-khi-nộp)

---

## 0. CHỈ DẪN BẮT BUỘC CHO AI AGENT & TRÌNH TỰ LÀM VIỆC (ĐỌC TRƯỚC TIÊN)

> **[QUY TẮC BẮT BUỘC DÀNH CHO AI AGENT]:**
> Nhóm 02 gồm 4 thành viên đều sử dụng AI Agent để hỗ trợ lập trình và rà soát. **TRƯỚC KHI chỉnh sửa bất kỳ tệp mã nguồn nào** cho một thành viên, AI Agent **PHẢI** tự động đọc/kiểm tra trạng thái thực tế của kho mã nguồn hiện tại (`git status`, `git tag`, nội dung các tệp nền dùng chung) đối chiếu với **3 Đợt thực hiện** và **Bảng cổng kiểm tra (Pre-flight Check)** dưới đây:
> - Nếu điều kiện tiên quyết của phần việc sắp làm **CHƯA ĐẠT** (chưa có tệp/hàm phụ thuộc, bạn cùng nhóm chưa đẩy lên `main`, hoặc có nguy cơ đè code / gây lỗi `403`, lỗi `CSP`, `Fatal error`), **Agent PHẢI DỪNG lại và THÔNG BÁO RÕ RÀNG cho thành viên đó**: *(1) Thiếu phần nào, (2) Phần đó thuộc trách nhiệm của ai, (3) Cần `git pull` hay phối hợp với ai trước khi tiếp tục, hoặc phần nào có thể làm trước không bị chặn.*
> - **Tuyệt đối không tự ý làm thay phần việc của thành viên khác** (trừ khi người dùng xác nhận rõ đã thống nhất trong nhóm) để bảo đảm đúng lịch sử commit cá nhân và tránh merge conflict.

### 0.1. Phân chia 3 Đợt làm việc (Cái gì PHẢI làm trước — SONG SONG — SAU CÙNG)

#### ĐỢT 1 — BẮT BUỘC LÀM TRƯỚC (Dựng "khung nền dùng chung")
*Mục tiêu: Tạo đủ các hàm dùng chung, `.env` và cấu trúc `inc/config.php` để Đợt 2 cả 4 người `git pull` về làm song song không bị `Fatal error` hay conflict.*

1. **Bước 1.1 — Bảo:** Gắn tag đóng băng Bài tập 6 (`git tag btn6 374fac7 && git push origin btn6`).
2. **Bước 1.2 — Châu:** Sửa `.gitignore` (chuẩn UTF-8 no BOM, chặn `.env`), gỡ `disable-tls`/`secure-http` khỏi `composer.json`, chạy `composer require vlucas/phpdotenv`, tạo `.env.example`, cập nhật `inc/db.php` đọc `$_ENV`. *(Sau khi Châu đẩy bước này, 3 bạn còn lại chạy `git pull`, `composer install` và copy `.env.example` thành `.env` trên máy mình).*
3. **Bước 1.3 — Bảo, Tấn, Thịnh (tạo hàm nền & CSDL — làm song song được nếu không sửa đè nhau):**
   - **Bảo:** Tạo `inc/csrf.php` (`csrfToken()`, `csrfField()`, `csrfHopLe()`).
   - **Tấn:** Thêm `chuoi()` vào `inc/ham.php`; chuẩn bị class CSS dùng chung trong `css/04-thanh-phan.css` và listener `data-confirm` trong `js/main.js`.
   - **Thịnh:** Thêm `ghiLogBaoMat()` vào `inc/ham.php`; thêm bảng `dang_nhap_sai` vào `database/schema.sql` (và chạy trên MariaDB cục bộ); tạo `database/tai-khoan.sql` (`nhom02_app`); bổ sung `dangNhap()`, `dangXuat()`, `soLanSai()`, `ghiLanSai()`, log `cam_truy_cap` vào `inc/xac-thuc.php`.
4. **Bước 1.4 — Chốt 1 lần `inc/config.php` (4 người phối hợp, 1 người ghép & push để tránh conflict):**
   - Ghép theo đúng thứ tự ở **Mục 5.1**: nạp `vendor/autoload.php` + `.env` (Châu) $\rightarrow$ `require` `ham.php`, `db.php`, `xac-thuc.php`, `csrf.php` $\rightarrow$ Header/CSP (Tấn) $\rightarrow$ Session/Cookie/timeout 30p (Thịnh) $\rightarrow$ chặn POST thiếu CSRF (Bảo) $\rightarrow$ `set_exception_handler` (Châu).
   - **Lưu ý sống còn để tránh "khóa chân" nhau khi ghép `inc/config.php`:**
     - Nếu đẩy lên `main` sớm, nên nạp `inc/csrf.php` để mọi người gọi được `csrfField()`, nhưng **chỉ bật dòng chặn `POST` trả `403` và header `CSP` chặt sau khi các form/trang đã kịp gắn `csrfField()` và bỏ inline script/style** (hoặc bật luôn trên `main` kèm cảnh báo rõ: ai vừa `git pull` về phải gắn `csrfField()` và dọn inline `style/script` cho trang mình ngay mới test POST/Console được).

#### ĐỢT 2 — LÀM SONG SONG (Và những việc LÀM TRƯỚC ĐƯỢC NGAY từ đầu)

- **Những việc làm được NGAY LẬP TỨC kể cả khi Đợt 1 chưa xong (0% phụ thuộc):**
  1. Dọn sạch `style="..."`, `onclick="..."`, `onsubmit="return confirm(...)"`, `<script>` nội tuyến sang tệp `.css`/`.js` ở tất cả trang phụ trách (kể cả **4 trang Phần B** — chỉ cần tránh dùng `data-confirm` nếu `js/main.js` chưa có listener, hoặc gắn sau).
  2. Viết `.htaccess` gốc và `uploads/.htaccess` (**Châu**).
  3. Viết `database/tai-khoan.sql` và câu lệnh `CREATE TABLE dang_nhap_sai` (**Thịnh**).
  4. Làm nháp lý thuyết **Phần C** và thử nghiệm độc lập ngoài dự án (như **Câu 4** của Thịnh ở `C:\temp\thu-env-git`).

- **Những việc làm SONG SONG NGAY SAU KHI ĐỢT 1 đã merge lên `main`:**
  - **Bảo:** Gắn `csrfField()` cho cụm trang chung (`chi-tiet-giao-an.php`, `gio-hang.php`, `ve-chung-toi.php`, `dong-gop-tai-lieu.php` + sửa upload `finfo_file()` & `chuoi()`), sửa `dang-xuat.php`, sửa cả 2 `fetch` trong `js/gio-hang.js`, cập nhật `api.http`, hoàn thành **Phần B (`3120124003_bao`)** & test (a–e).
  - **Tấn:** Dọn `inc/header.php`, `kho-hoc-lieu.php`, `500.php`, trang công khai; áp dụng `chuoi()` ở trang công khai & API; bật/tinh chỉnh CSP trong `inc/config.php` không còn đỏ Console; hoàn thành **Phần B (`3120124027_tan`)** & test (a–e).
  - **Châu:** Gia cố cụm Quản trị (`quan-tri.php`, `them-giao-an.php`, `sua-giao-an.php`, `xoa-giao-an.php`, `inc/kiem-tra.php`); hoàn thành **Phần B (`3120124006_chau`)** (xóa `<script>`, 5 `on*=`, 45 `style=`, kiểm tra link `http(s)://`, cờ `secure` cookie `theme`) & test (a–e).
  - **Thịnh:** Gia cố `dang-nhap.php` (429 + `dangNhap()`), `dang-ky.php`, `dat-hang.php`, `don-hang.php`; thêm `secure` cho cookie `da_xem` ở `chi-tiet-giao-an.php`; hoàn thành **Phần B (`3120222126_thinh`)** & test (a–e).

> **Cảnh báo file dễ đụng nhau (Merge Conflict) ở Đợt 2:**
> - `inc/config.php` & `inc/ham.php`: Chỉ sửa ở Đợt 1 hoặc báo cả nhóm trước khi đụng.
> - `chi-tiet-giao-an.php`: Bảo sửa form CSRF, Tấn rà `chuoi($_COOKIE['da_xem'])`, Thịnh thêm `secure` cho cookie `da_xem` $\rightarrow$ ai sửa sau phải `git pull` trước để tránh ghi đè!
> - `js/main.js` & `css/04-thanh-phan.css`: Tấn tạo trước, 3 bạn kia ưu tiên thêm CSS riêng trong thư mục `thanhvien/*/css/` hoặc báo Tấn khi thêm class dùng chung.

#### ĐỢT 3 — BẮT BUỘC LÀM SAU CÙNG (Sau khi 4 người đã merge đủ Đợt 2 vào `main`)
1. **Tấn:** Chạy **4 lệnh `grep`** (Bảng 4) trên toàn bộ mã nguồn hợp nhất.
2. **Bảo:** Chạy `composer audit` (Bảng 4) và `git diff --stat btn6 HEAD`.
3. **Cả 4 người:** Rà soát chéo **Bảng 2 (9 nhóm)** trên code cuối cùng của nhau; chạy đủ **14 ca Bảng 3 BTN7** + **hồi quy BTN6 (`api.http` + 14 ca BTN6)** bằng cả `root` và `nhom02_app`, trên cả `php -S` lẫn XAMPP Apache.
4. **Bảo:** Cập nhật `README.md`, tổng hợp báo cáo `Nhom02_Baitap7.pdf` và đóng gói `LTW_BTN7_Nhom02_code.zip`.

---

### 0.2. Bảng cổng kiểm tra nhanh cho AI Agent (Agent Pre-Flight Check)

Khi Agent nhận yêu cầu làm việc cho thành viên **X**, hãy chạy/đọc các kiểm tra sau **TRƯỚC KHI** thực thi:

| Nếu Agent đang làm cho | Agent PHẢI kiểm tra trước trong repo | Nếu THIẾU / CHƯA ĐẠT, Agent PHẢI thông báo gì cho người dùng? |
|:---|:---|:---|
| **Bất kỳ ai (trước mọi việc)** | Kiểm tra `git status`, nhánh hiện tại, và `git log -n 5 --oneline`. | Nhắc thành viên `git pull origin main` trước nếu nhánh cục bộ đang cũ, và xác nhận đang dùng đúng tài khoản Git cá nhân. |
| **Châu (Đợt 1 — `.env`, `phpdotenv`)** | Kiểm tra `git tag -l btn6`. | *"Cảnh báo: Chưa thấy tag `btn6` trên commit `374fac7`. Nhắn Bảo gắn `btn6` trước hoặc xác nhận đã gắn ở remote rồi `git fetch --tags`."* (Vẫn có thể sửa `.gitignore`, `.htaccess`, hoặc tạo `phpdotenv` nếu xác nhận `btn6` đã có trên remote). |
| **Bảo (gắn CSRF & test Đợt 2)** | 1) Kiểm tra `vendor/autoload.php` & `.env` đã chạy được chưa (từ Châu);<br>2) Kiểm tra `inc/ham.php` đã có `ghiLogBaoMat()` chưa (từ Thịnh — vì `inc/config.php` gọi `ghiLogBaoMat('csrf_sai')`!). | *"Chưa thấy `ghiLogBaoMat()` trong `inc/ham.php` (việc của Thịnh) hoặc chưa có `vlucas/phpdotenv`/`.env` (việc của Châu). Nếu bật kiểm tra CSRF trong `inc/config.php` ngay bây giờ sẽ gây Fatal Error khi gửi sai CSRF hoặc khi thiếu `.env`! Có thể viết `inc/csrf.php` và dọn `style=` trước, nhưng nên chờ Đợt 1 ghép xong rồi mới test POST."* |
| **Tấn (bật CSP & `chuoi()` Đợt 1–2)** | 1) Nếu định bật header `Content-Security-Policy: default-src 'self'` trong `inc/config.php`: kiểm tra `inc/header.php` còn `<script>`/`onclick`/`style=` không, và kiểm tra các trang khác đã dọn inline `style`/`script` chưa;<br>2) Nếu định chạy 4 lệnh `grep` (Bảng 4): kiểm tra cả 4 người đã merge Đợt 2 chưa. | - Nếu trang còn `<script>`/`onclick`/`style=`: *"Cảnh báo: Trong `inc/header.php` (hoặc trang của Châu/Thịnh/Bảo) vẫn còn inline `<script>` / `on*=` / `style=`. Nếu bật CSP ngay thì Console sẽ báo lỗi CSP và nút menu mobile bị hỏng. Cần dọn inline ở `inc/header.php` trước/cùng lúc bật CSP, và nhắc 3 bạn kia dọn phần của họ."*<br>- Nếu định chạy Bảng 4 sớm: *"Chưa nên chốt Bảng 4 vì các thành viên khác chưa merge xong Đợt 2; số dòng `grep` sẽ thay đổi."* |
| **Châu (cụm Quản trị & Phần B Đợt 2)** | Kiểm tra:<br>1) `inc/csrf.php` (`csrfField`) đã tồn tại và được nạp chưa (Bảo);<br>2) `inc/ham.php` đã có `chuoi()` chưa (Tấn);<br>3) `js/main.js` đã có listener `data-confirm` chưa (Tấn). | *"Chưa thấy `csrfField()` (Bảo) hoặc `chuoi()` (Tấn) trong repo. Hiện tại chỉ nên làm trước các phần độc lập: `.htaccess`, `uploads/.htaccess`, xóa inline `<script>`/`style=`/`on*=` ở cụm Quản trị & `3120124006_chau/gioithieu.php`, kiểm tra link `http(s)://`. Chờ `git pull` có `csrfField()` & `chuoi()` rồi mới gắn vào form để tránh lỗi Fatal Error."* |
| **Thịnh (Đợt 1–2: Auth, 429, Cookie, Phần B)** | 1) Khi viết `dang-nhap.php` dùng `chuoi()`, `csrfField()`: kiểm tra `inc/ham.php` (`chuoi`) và `inc/csrf.php` (`csrfField`) đã có chưa;<br>2) Khi test trên web: kiểm tra bảng `dang_nhap_sai` đã tồn tại trong CSDL cục bộ chưa và `.env` đã có chưa (Châu). | *"Chưa thấy `chuoi()` / `csrfField()` / `.env` trong repo (hoặc bảng `dang_nhap_sai` chưa nạp vào MariaDB cục bộ). Phần viết `database/tai-khoan.sql`, `dang_nhap_sai`, `ghiLogBaoMat()`, dọn `style=` ở `dang-nhap/dang-ky/dat-hang/don-hang/3120222126_thinh` và thí nghiệm Phần C Câu 4 (ngoài repo) vẫn làm trước được ngay; chỉ phần gắn `csrfField()`/`chuoi()` và test đăng nhập cần chờ khung Đợt 1."* |
| **Bất kỳ ai (khi sửa `inc/config.php`)** | Kiểm tra xem có thành viên khác đang sửa hoặc đã chỉnh `inc/config.php` chưa, và kiểm tra thứ tự dòng (Mục 5.1). | *"Cảnh báo: `inc/config.php` là tệp dùng chung cả 4 biện pháp (`.env`, CSP, Session, CSRF). Hãy `git pull` mới nhất và chèn đúng thứ tự ở Mục 5.1; tránh tự ý sửa phần của 3 bạn khác nếu không báo nhóm."* |
| **Bảo (Đợt 3 — Chốt báo cáo & ZIP)** | Kiểm tra đủ: `git log` có cả 4 tài khoản, không có `.env` trong lịch sử Git, `composer audit` sạch, đủ `tests/bao-mat.*`, cả 4 Phần B đã hoàn chỉnh. | Liệt kê chính xác những mục còn thiếu từ thành viên nào trước khi xuất `Nhom02_Baitap7.pdf` và `LTW_BTN7_Nhom02_code.zip`. |

---

## 1. QUY TẮC SỐNG CÒN & ĐIỀU KIỆN TIÊN QUYẾT

1. **Giới hạn phạm vi thử nghiệm (Slide 5 & Điều 289 BLHS):**
   - **Mọi thử nghiệm bảo mật CHỈ được thực hiện trên máy cá nhân** (`localhost:8000` qua `php -S` hoặc `http://localhost/ltweb-doan-nhom02/` trên XAMPP Apache), với CSDL `ltweb_nhom02` của chính nhóm.
   - Tuyệt đối không thử nghiệm trên website Trường, website nhóm khác hoặc máy chủ bên ngoài (kể cả *"chỉ thử cho biết"*).
2. **Viết bằng PHP 8.1+ thuần với PDO:**
   - Không dùng framework hay CMS; Composer chỉ dùng cho PSR-4 autoload và thư viện phụ trợ `vlucas/phpdotenv`.
   - Mã CSRF, chống đoán mật khẩu (`dang_nhap_sai`), ghi log bảo mật (`logs/bao-mat.log`) phải do nhóm tự viết theo cấu trúc bài học.
3. **Chạy được cả hai môi trường:**
   - Website bắt buộc phải chạy bình thường trên **`php -S localhost:8000`** (môi trường chấm chính theo `README.md`) **VÀ** trên **Apache của XAMPP** (`http://localhost/ltweb-doan-nhom02/`, để kiểm thử `.htaccess`).
   - Tuyệt đối giữ tương thích ngược mọi chức năng Bài tập 6 (`api.http` và 14 ca Bảng 3 của Bài tập 6 phải tiếp tục ĐẠT sau khi gia cố).
4. **Quy tắc Git & Minh chứng đóng góp cá nhân:**
   - Mỗi thành viên dùng **đúng tài khoản GitHub cá nhân** để `git commit` và `git push` cho phần mình phụ trách (cả biện pháp Phần A, rà soát/sửa Phần B và lỗi tìm thấy trong Bảng 2). Thành viên không có commit hoặc không tham gia theo xác nhận nhóm nhận **0 điểm**.
   - Mỗi biện pháp trong Bảng 1 là một hoặc vài commit riêng, thông điệp commit ghi rõ biện pháp/lỗ hổng (ví dụ: `"Them ma CSRF cho moi form POST va fetch"` hoặc `"Sua: them csrfField cho form luu but cua Bao"`).
   - **KHÔNG BAO GIỜ commit `.env`, `vendor/`, `logs/*.log`**, không commit bản mã lỗi khi làm thí nghiệm Phần C (thử nghiệm tắt CSRF/CSP/đổi timeout xong phải khôi phục ngay).
5. **Không lộ bí mật & minh bạch AI:**
   - Tài khoản thử dùng email giả định (`admin@ued.vn`, `lan@ued.vn`, `an@ued.vn`..., mật khẩu thử ghi trong `README.md`). Mật khẩu trong `.env.example` để rỗng như XAMPP, mật khẩu trong `database/tai-khoan.sql` để chuỗi giữ chỗ (ví dụ `'thay-bang-mat-khau-dai-ngau-nhien'`).
   - Khi chụp ảnh DevTools đưa vào báo cáo PDF, **bắt buộc che bớt giá trị cookie `PHPSESSID` và mã CSRF**.
   - Được dùng AI tra cứu/gợi ý/sửa lỗi nhưng **bắt buộc ghi rõ trong báo cáo đã dùng AI ở phần nào**; cả 4 người đều phải hiểu tường tận để trả lời vấn đáp khi demo.

---

## 2. CHUẨN BỊ TRƯỚC KHI CHIA NHÁNH / VIẾT MÃ (CẢ NHÓM)

### Bước 0.1 — Gắn thẻ đóng băng Bài tập 6 (`btn6`) *(Nhóm trưởng thực hiện ngay đầu tuần)*
Trên nhánh `main` hiện tại (đang dừng ở commit cuối BTN6 là `374fac7`), thực hiện:
```bash
git pull origin main
git tag btn6 374fac7
git push origin btn6
```
> Mục đích: Cuối bài tập 7, nhóm phải chạy `git diff --stat btn6 HEAD` để dán kết quả vào báo cáo.

### Bước 0.2 — Lập Bản đồ rủi ro của website ITeduShare (Mẫu Slide 15)
Cả nhóm họp nhanh thống nhất bảng "Bản đồ rủi ro" (chép vào đầu Phần A trong `Nhom02_Baitap7.pdf`) dựa trên thực tế các tệp đang có trong `ltweb-doan-nhom02`:

| STT | Rủi ro (Tên & Mã OWASP Top 10:2025) | Trang / Chức năng dễ mắc trong ITeduShare | Biện pháp gia cố tương ứng (Bảng 1) |
|:---:|:---|:---|:---|
| 1 | **Cross-Site Request Forgery (CSRF)** — *A01 Broken Access Control* | Mọi form `method="post"` (`dang-nhap.php`, `dang-ky.php`, `gio-hang.php`, `dat-hang.php`, `don-hang.php`, `ve-chung-toi.php`, `them/sua/xoa-giao-an.php`, 4 trang `thanhvien/*/gioithieu.php`), nút GET `dang-xuat.php`, `fetch` của `api/gio-hang.php` | **Biện pháp 1:** `inc/csrf.php`, kiểm tra tập trung trong `inc/config.php`, `csrfField()`, `X-CSRF-Token`, `dang-xuat.php` chỉ nhận POST |
| 2 | **Quản lý phiên yếu & Cookie chưa bảo vệ** — *A07 Authentication Failures* | `inc/config.php` gọi `session_start()` chưa bật `use_strict_mode`, chưa đặt `HttpOnly`/`SameSite`, chưa hết hạn sau 30 phút; cookie `theme` ở trang Châu chưa có cờ `secure` | **Biện pháp 2:** `session.use_strict_mode = 1`, `HttpOnly`, `SameSite=Lax`, `Secure` theo HTTPS, timeout 30 phút, `dangNhap()` / `dangXuat()` |
| 3 | **XSS & Thiếu Header bảo mật / CSP** — *A05 Injection / A02 Security Misconfiguration* | Nhiều trang còn inline `style="..."`, `onclick="..."`, `onsubmit="return confirm(...)"`, 2 thẻ `<script>` nội tuyến trong `inc/header.php`, 1 thẻ `<script>` trong trang Châu; thiếu `CSP`, `nosniff`, `Referrer-Policy` | **Biện pháp 3:** Gửi header CSP chặt chẽ (không `'unsafe-inline'`, không `*`), `nosniff`, `Referrer-Policy`, bỏ `X-Powered-By`; chuyển mọi script/style nội tuyến sang `.js`/`.css` |
| 4 | **Lộ thông tin cấu hình & bí mật DB** — *A02 Security Misconfiguration / A04 Cryptographic Failures* | `inc/db.php` đang viết cứng `$user = 'root'`, `inc/config.php` dùng hằng `MOI_TRUONG = 'dev'`; `.gitignore` chưa có `.env` | **Biện pháp 4:** `vlucas/phpdotenv`, `.env.example`, `.gitignore` chặn `.env`, thay `MOI_TRUONG` bằng `APP_DEBUG` (`DEBUG`) |
| 5 | **Lộ tệp nội bộ & thực thi mã trong thư mục upload** — *A02 / A06 Insecure Design* | Gốc dự án chưa có `.htaccess`, `uploads/` chưa có `uploads/.htaccess` chặn chạy `.php` và thêm `nosniff`; truy cập trực tiếp `.git/`, `inc/`, `vendor/`, `database/`, `composer.json` trên Apache | **Biện pháp 5:** `.htaccess` gốc + `uploads/.htaccess` (chặn liệt kê thư mục, trả 404/403, cấm thực thi script trong `uploads/`) |
| 6 | **Dùng tài khoản DB toàn quyền (`root`)** — *A01 Broken Access Control* | Ứng dụng PHP đang kết nối MariaDB bằng `root` có toàn quyền `DROP`/`CREATE`/`GRANT` | **Biện pháp 6:** Tạo `database/tai-khoan.sql` cấp quyền tối thiểu `SELECT, INSERT, UPDATE, DELETE` cho `nhom02_app` trên `ltweb_nhom02.*` |
| 7 | **Dò mật khẩu Brute-force & Thiếu nhật ký giám sát** — *A07 Authentication Failures / A09 Security Logging Failures* | `dang-nhap.php` chưa giới hạn số lần nhập sai; `yeuCauQuyen()` trả 403 và kiểm tra CSRF sai chưa ghi sự kiện bảo mật có cấu trúc | **Biện pháp 7:** Bảng `dang_nhap_sai` (chặn 5 lần/15 phút → `429`), `ghiLogBaoMat()` ghi `csrf_sai`, `cam_truy_cap`, `dang_nhap_sai` vào `logs/bao-mat.log` |
| 8 | **Kiểm tra đầu vào dạng mảng (`[]`)** — *A05 Injection / A06 Insecure Design* | Truy vấn dạng `?q[]=ao`, `?sx[]=x` hoặc `POST` gửi `ho_ten[]=...` nếu không ép `chuoi()` có thể phát sinh `TypeError` hoặc Warning | Kết hợp kiểm tra ở **Phần A & B:** viết `chuoi()` trong `inc/ham.php` và rà mọi đầu vào chuỗi |

---

## 3. BẢNG TỔNG HỢP PHÂN CÔNG CÔNG BẰNG (4 THÀNH VIÊN × 25%)

Để bảo đảm **4 người làm đều nhau 25%**, bám sát đúng các phần việc bạn đã phụ trách ở Bài tập 6 (dễ hiểu mã nguồn và dễ giải trình vấn đáp):

| Thành viên | Phần A — Biện pháp Bảng 1 | Phần A — Công việc bổ trợ & Công cụ | Rà soát chéo Bảng 2 (Slide 38) | Bảng 3 — Ca kiểm thử phụ trách | Phần B — Trang cá nhân | Phần C — Câu thảo luận chính | Đóng góp |
|:---|:---|:---|:---|:---|:---|:---|:---:|
| **1. Lê Hoàng Bảo**<br>(`3120124003` — `HoangBao233`)<br>*Nhóm trưởng* | **Biện pháp 1:** Mã CSRF (`inc/csrf.php`, `inc/config.php`, `inc/header.php`, `dang-xuat.php`, `js/gio-hang.js`, `api.http`, form POST trang chung) | Gắn `git tag btn6`; chạy `composer audit` (Bảng 4); kiểm tra `git diff --stat btn6 HEAD`; tổng hợp PDF `Nhom02_Baitap7.pdf`, cập nhật `README.md`, nén `LTW_BTN7_Nhom02_code.zip` | **Nhóm 5 (CSRF)**<br>**Nhóm 9 (Thư viện)** | **Ca 2** (POST thiếu CSRF)<br>**Ca 3** (API `X-CSRF-Token`)<br>**Ca 13** (Ca riêng đề tài #1) | `3120124003_bao/gioithieu.php` (`tv_luu_but`, `tv_binh_chon`) + đủ 5 ca (a–e) | **Câu 1:** `SameSite` có thay được mã CSRF không? | **25%** |
| **2. Nguyễn Cảnh Tấn**<br>(`3120124027` — `beotan19-cpu`) | **Biện pháp 3:** Header bảo mật & CSP (`inc/config.php`, dọn script/style/on* nội tuyến toàn bộ các trang chung sang `css/*.css` & `js/*.js`) | Viết `chuoi()` trong `inc/ham.php` + rà đầu vào mảng trang công khai/API; chạy **4 lệnh `grep` (Slide 39)** cho Bảng 4 | **Nhóm 2 (SQL)**<br>**Nhóm 3 (Đầu ra / XSS)** | **Ca 1** (Header & Cookie)<br>**Ca 7** (XSS & thử CSP inline)<br>**Ca 8** (Tham số dạng mảng `[]`) | `3120124027_tan/gioithieu.php` (`tv_luot_xem`, `tv_du_an_tan`, `tv_luu_but`) + đủ 5 ca (a–e) | **Câu 2:** `HttpOnly` còn chặn được gì khi trang đã bị XSS? | **25%** |
| **3. Lê Thị Xuân Châu**<br>(`3120124006` — `XuanChau-06`) | **Biện pháp 4:** `.env`, `.env.example`, `.gitignore`, `vlucas/phpdotenv`, `APP_DEBUG`, `inc/db.php`<br>**Biện pháp 5:** `.htaccess` gốc & `uploads/.htaccess` | Dọn sạch `.gitignore` đúng UTF-8 (xóa BOM/dòng rác); xử lý form CSRF + bỏ `style`/`onsubmit` ở cụm trang Quản trị (`quan-tri.php`, `them/sua/xoa-giao-an.php`) | **Nhóm 1 (Đầu vào)**<br>**Nhóm 7 (Upload)**<br>**Nhóm 8 (Cấu hình)** | **Ca 9** (`.env` & `APP_DEBUG=false`)<br>**Ca 10** (`.htaccess` trên XAMPP Apache)<br>**Ca 14** (Ca riêng đề tài #2) | `3120124006_chau/gioithieu.php` (`tv_du_an`, `theme`, avatar) + đủ 5 ca (a–e) | **Câu 3:** `.htaccess` và máy chủ `php -S` | **25%** |
| **4. Nguyễn Tiến Thịnh**<br>(`3120222126` — `cowt1310`) | **Biện pháp 2:** Cookie phiên & phiên an toàn (`use_strict_mode`, `HttpOnly/SameSite/Secure`, timeout 30p, `dangNhap()`, `dangXuat()`)<br>**Biện pháp 7:** Chống brute-force (`dang_nhap_sai`, 429) & `ghiLogBaoMat()` + `cam_truy_cap` | **Biện pháp 6:** Viết `database/tai-khoan.sql` (`nhom02_app`), kiểm thử toàn bộ web bằng quyền tối thiểu & ca lỗi `1142`; xử lý CSRF + bỏ inline `style` ở `dang-ky.php`, `dang-nhap.php`, `dat-hang.php`, `don-hang.php` | **Nhóm 4 (Phân quyền)**<br>**Nhóm 6 (Phiên, mật khẩu)** | **Ca 4** (GET & quyền + CSRF)<br>**Ca 5** (`use_strict_mode` & Session Fixation)<br>**Ca 6** (Hết hạn phiên)<br>**Ca 11 & 12** (`nhom02_app` & 429) | `3120222126_thinh/gioithieu.php` (`tv_yeu_cau_cv`, tính điểm GPA) + đủ 5 ca (a–e) | **Câu 4:** Lỡ commit tệp `.env` thì làm gì? | **25%** |

> **Lưu ý phối hợp thứ tự (xem chi tiết tại [Mục 0](#0-chỉ-dẫn-bắt-buộc-cho-ai-agent--trình-tự-làm-việc-đọc-trước-tiên)):** Thực hiện theo đúng **3 Đợt** (`Đợt 1` nền dùng chung $\rightarrow$ `Đợt 2` song song $\rightarrow$ `Đợt 3` tổng kiểm & chốt Bảng 2/3/4). Cả 4 thành viên và AI Agent phải chạy cổng kiểm tra **Mục 0.2** trước khi đụng mã nguồn!

---

## 4. CHI TIẾT CÔNG VIỆC TỪNG THÀNH VIÊN

---

### 4.1. THÀNH VIÊN 1: LÊ HOÀNG BẢO (Nhóm trưởng — MSSV: `3120124003` — GitHub: `HoangBao233`)

#### A. Biện pháp 1 — Mã CSRF cho mọi form POST và `fetch`
1. **Tạo tệp mới `inc/csrf.php`** (tạo thư mục/tệp sạch, dùng đúng `random_bytes(32)` và `hash_equals`):
   ```php
   <?php
   // inc/csrf.php — Tạo và kiểm tra mã chống giả mạo yêu cầu CSRF
   function csrfToken(): string
   {
       return $_SESSION['csrf'] ??= bin2hex(random_bytes(32));
   }

   function csrfField(): string
   {
       return '<input type="hidden" name="csrf" value="' . csrfToken() . '">';
   }

   function csrfHopLe(): bool
   {
       $gui = $_POST['csrf'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
       return is_string($gui) && hash_equals(csrfToken(), $gui);
   }
   ```
   - **Cảnh giác ca kiểm thử mảng `csrf[]=`:** Nhờ `is_string($gui)`, nếu kẻ thử gửi `csrf[]=abc`, `$gui` là `array` nên `csrfHopLe()` trả về `false` an toàn, **không văng `TypeError`** trong `hash_equals()`.

2. **Tích hợp kiểm tra CSRF tập trung vào `inc/config.php`** *(phối hợp thứ tự chuẩn với Thịnh, Tấn, Châu — xem Mục 5.1)*:
   - Nạp `require_once __DIR__ . '/csrf.php';` trước khối `session_start()` (hoặc ngay cùng các tệp `ham.php`, `db.php`, `xac-thuc.php`).
   - Ngay **SAU** khi đã `session_start()` và xử lý timeout 30 phút, đặt khối chặn POST:
     ```php
     if ($_SERVER['REQUEST_METHOD'] === 'POST' && !csrfHopLe()) {
         ghiLogBaoMat('csrf_sai', ['trang' => $_SERVER['REQUEST_URI'] ?? '-']);
         $loi = 'Trang đã hết hạn, hãy tải lại trang rồi gửi lại';
         if (str_contains($_SERVER['CONTENT_TYPE'] ?? '', 'json')) {
             traJson(['loi' => $loi], 403);
         }
         http_response_code(403);
         exit($loi);
     }
     ```
   - **Kiểm tra kỹ trang `api/gio-hang.php`:** Nếu client gửi `Content-Type: application/json` thiếu header `X-CSRF-Token`, khối trên sẽ gọi `traJson(['loi' => $loi], 403)` và thoát ngay, trả đúng HTTP `403` dạng JSON (không trả HTML!).

3. **Cập nhật `inc/header.php`:**
   - Thêm vào bên trong thẻ `<head>`:
     ```php
     <meta name="csrf-token" content="<?= csrfToken() ?>">
     ```
   - Thay liên kết `<a href="<?= $goc ?>dang-xuat.php">Đăng xuất</a>` bằng form POST có `csrfField()`:
     ```php
     <form method="post" action="<?= $goc ?>dang-xuat.php" class="form-dang-xuat">
         <?= csrfField() ?>
         <button type="submit" class="nut-dang-xuat">Đăng xuất</button>
     </form>
     ```
   - Kiểm tra hiển thị CSS cho nút `.nut-dang-xuat` (không dùng inline `style="..."` để không vi phạm CSP!).

4. **Sửa `dang-xuat.php`:**
   - Từ chối hoàn toàn `GET` (và mọi phương thức khác `POST`) bằng mã `405 Method Not Allowed` + header `Allow: POST`:
     ```php
     <?php
     require_once __DIR__ . '/inc/config.php';

     if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
         http_response_code(405);
         header('Allow: POST');
         exit;
     }

     dangXuat(); // Hàm do Thịnh hoàn thiện trong inc/xac-thuc.php
     header('Location: index.php');
     exit;
     ```

5. **Cập nhật `js/gio-hang.js` gửi header `X-CSRF-Token`:**
   - Đọc token từ `<meta name="csrf-token">`:
     ```js
     const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
     ```
   - Thêm `'X-CSRF-Token': csrf` vào `headers` của **cả hai lệnh `fetch('api/gio-hang.php', ...)`** đang có trong `js/gio-hang.js` (cả ở nhánh submit `.form-luu-giao-an` lẫn nút `.nut-luu-giao-an` — chú ý sửa **cả hai** để không sót nút "Lưu" ở trang `kho-hoc-lieu.php`!).

6. **Gắn `<?= csrfField() ?>` vào toàn bộ form `method="post"` thuộc nhóm trang dùng chung:**
   - `chi-tiet-giao-an.php` (form `.form-luu-giao-an`)
   - `gio-hang.php` (4 chỗ: form `xoa_het`, form `cap_nhat`, form `xoa`, và mọi form POST khác nếu có)
   - `ve-chung-toi.php` (form liên hệ)
   - `dong-gop-tai-lieu.php` (**BẮT BUỘC gia cố ngay!**): tệp này **CÓ** form `method="POST"` + `enctype="multipart/form-data"` đang thiếu:
     - `<?= csrfField() ?>` trong `<form>`.
     - `chuoi()` cho `$_POST['hoten']`, `$_POST['email']`, `$_POST['sdt']` (hiện đang `trim($_POST['hoten'] ?? '')` sẽ lỗi `TypeError` nếu gửi `hoten[]=abc`).
     - Kiểm tra MIME thực bằng `finfo_file()` + đặt tên file ngẫu nhiên (`bin2hex(random_bytes(8))`) + ánh xạ đuôi từ MIME whitelist (hiện dòng 35-52 mới chỉ kiểm tra `pathinfo` theo tên gốc và lưu `time() . '_' . $file['name']`!).
     - Dọn 5 thuộc tính `style="..."` (dòng 81, 96, 105, 116, 125) sang class CSS.
   - *(Lưu ý: `dat-hang.php` & `don-hang.php` & `dang-nhap.php` & `dang-ky.php` do Thịnh gắn; cụm `quan-tri.php` & `them/sua-giao-an.php` do Châu gắn — Bảo rà lại 1 lượt toàn repo bằng tìm kiếm `method="post"` / `method='post'` không phân biệt hoa thường để chắc chắn **100% form POST trong dự án đều có `csrfField()`**).*

7. **Cập nhật `api.http`:**
   - Bổ sung hướng dẫn và biến `@phpsessid = ...`, `@csrf = ...` ở đầu tệp, thêm request GET mở trang chủ/chi tiết trước để lấy cookie & thẻ meta `csrf-token`, và gắn `Cookie: PHPSESSID={{phpsessid}}` + `X-CSRF-Token: {{csrf}}` cho các request `POST api/gio-hang.php` (cũng bổ sung 1 request mẫu thiếu CSRF để kiểm chứng trả `403 JSON`).

8. **Công việc nhóm trưởng cuối tuần:**
   - Thiết lập tệp lưu lệnh kiểm thử hồi quy `tests/bao-mat.http` (hoặc `tests/bao-mat.sh` — **không ghi giá trị `PHPSESSID`/CSRF thật vào tệp**, dùng biến để điền khi chạy!).
   - Chạy `composer audit` (ghi kết quả dạng chữ cho Bảng 4).
   - Chạy `git diff --stat btn6 HEAD` và phân tích: biện pháp nào chỉ sửa tệp dùng chung, biện pháp nào phải sửa từng trang.
   - Kiểm tra lần cuối trước khi nộp: `git ls-files .env` và `git log --all --oneline -- .env` phải **trống hoàn toàn**.
   - Cập nhật [`README.md`](README.md) đúng quy cách mục 3 đề bài (xem mục 6), hoàn thiện báo cáo **`Nhom02_Baitap7.pdf`**, nén **`LTW_BTN7_Nhom02_code.zip`** (loại trừ `.git/`, `vendor/`, `.env`).

#### B. Rà soát chéo Bảng 2 & Phần B của Bảo (`thanhvien/3120124003_bao/gioithieu.php`)
- **Rà soát chéo Bảng 2 (đọc toàn bộ mã nguồn của cả 4 người):**
  - **Nhóm 5 (CSRF):** Kiểm tra xem có thao tác nào đổi dữ liệu bằng GET không (`dang-xuat.php`, `xoa-giao-an.php`, các trang thành viên); mọi form `POST` có `csrfField()`; mọi `fetch` POST có `X-CSRF-Token`; kiểm tra trang `403` không lỗi `500`.
  - **Nhóm 9 (Thư viện):** Chạy `composer audit`, xem `composer.json` và `composer.lock` (cảnh báo về `disable-tls: true`, `secure-http: false` hiện có trong `composer.json` — nên bỏ 2 dòng tắt TLS đó đi và ghi nhận trong Bảng 2!).
- **Phần B cá nhân (`thanhvien/3120124003_bao/gioithieu.php`):**
  - Thêm `<?= csrfField() ?>` vào **cả 4 form POST**: form `luu_but`, form `xoa_luu_but`, form `binh_chon` (kiểm tra còn form nào khác không!).
  - Dùng hàm `chuoi()` cho `$_POST['ten']`, `$_POST['loi_nhan']`, `$_POST['ngon_ngu']`, `$_POST['action']` và kiểm tra `?trang[]` qua `filter_var(..., FILTER_VALIDATE_INT)`.
  - Dọn sạch **76 chỗ `style="..."`** và **1 chỗ `onsubmit="return confirm(...)"`** sang `thanhvien/3120124003_bao/css/canhan-bao.css` và `data-confirm="..."` (xử lý qua JS ngoài, không dùng inline attribute).
  - Kiểm thử đủ **5 ca bắt buộc Phần B**:
    - `(a)` Gửi lưu bút tên `<b>Bảo Test</b>`, lời nhắn `"Hello ' & <b>XSS</b>"` $\rightarrow$ hiện nguyên văn, không in đậm hay chạy script.
    - `(b)` Gửi form lưu bút/bình chọn thiếu `csrf` (xóa bằng DevTools hoặc curl) $\rightarrow$ HTTP `403`, không thêm dòng trong `tv_luu_but`/`tv_binh_chon`.
    - `(c)` Chưa đăng nhập hoặc đăng nhập bằng `lan@ued.vn`, dùng `curl` gửi `POST` với `action=xoa_luu_but` kèm `PHPSESSID` & `csrf` của Lan $\rightarrow$ bị từ chối, không xóa được.
    - `(d)` Đăng nhập `bao@ued.vn`, sửa `id` ẩn thành ID của một lời nhắn thuộc MSSV `3120124027` (Tấn) trong `tv_luu_but` và submit $\rightarrow$ nhờ `AND mssv = '3120124003'`, lời nhắn của Tấn không bị xóa.
    - `(e)` Mở `?trang[]=1` hoặc POST `ten[]=abc&loi_nhan[]=xyz` $\rightarrow$ không Warning, không `TypeError`, không trang `500`.
  - Commit & push từ tài khoản **`HoangBao233`**.

#### C. Bảng 3 & Phần C của Bảo
- **Bảng 3:** Thực hiện và ghi đầy đủ lệnh `curl` / REST Client + kết quả thực tế + ảnh minh chứng (che token) cho **Ca 2**, **Ca 3**, và **Ca 13 (ca tự đề xuất #1 — ví dụ: kiểm thử form bình chọn `tv_binh_chon` thiếu CSRF và gửi `ngon_ngu[]=PHP`)**.
- **Phần C — Câu 1 (*`SameSite` có thay được mã CSRF không?*):**
  - Tạo thư mục tạm **ngoài dự án** (VD `C:\temp\csrf-test\attack.html`) chứa form POST trỏ `action="http://localhost/ltweb-doan-nhom02/gio-hang.php"` hoặc chạy theo đề `http://localhost:8000/gio-hang.php` với 2 input ẩn `action=them`, `id=2`, `sl=1`, không có `csrf`.
  - Mở 2 cửa sổ server ngoài đó: `php -S localhost:8001` và `php -S 127.0.0.1:8002` (trong khi website nhóm chạy ở `php -S localhost:8000`).
  - Thực hiện đủ 2 vòng (có kiểm tra CSRF trong `inc/config.php` và tạm comment khối kiểm tra CSRF rồi khôi phục ngay), kiểm tra tab **Network (bật Preserve log)**:
    - Chụp/ghi rõ từ `localhost:8001` có gửi kèm `PHPSESSID` hay không (SameSite=Lax: form POST cross-port trên cùng site `localhost` -> trình duyệt vẫn coi cùng *site* nên gửi cookie `PHPSESSID`!), còn từ `127.0.0.1:8002` -> trình duyệt coi là *cross-site*, bỏ cookie `PHPSESSID` khi POST (`SameSite=Lax` chặn được `127.0.0.1` nhưng **không chặn được `localhost:8001` hay tên miền con khác**!).
    - Giải thích tại sao vẫn bắt buộc cần mã CSRF, và tại sao mã CSRF không thay được kiểm tra quyền (dẫn chứng Ca 4).

---

### 4.2. THÀNH VIÊN 2: NGUYỄN CẢNH TẤN — MSSV: `3120124027` — GitHub: `beotan19-cpu`

#### A. Biện pháp 3 — Header bảo mật & Content Security Policy (CSP), hàm `chuoi()`, dọn Inline Script/Style/Handler
1. **Thêm hàm `chuoi()` vào `inc/ham.php`:**
   ```php
   // Làm sạch tham số chuỗi — tránh lỗi TypeError khi người dùng gửi mảng ?q[]=...
   function chuoi(mixed $v): string
   {
       return is_string($v) ? $v : '';
   }
   ```
   - Rà soát và áp dụng `chuoi()` ở `kho-hoc-lieu.php` (`$_GET['q']`, `$_GET['cap_hoc']`, `$_GET['sx']`), `ve-chung-toi.php`, `api/giao-an.php` (`$_GET['q']`), `api/gio-hang.php` (chú ý trong `api/gio-hang.php`: kiểm tra `$data['id']` và `$data['sl']` khi JSON gửi mảng hoặc object lạ không được để văng lỗi không mong muốn!), `chi-tiet-giao-an.php` (`$_COOKIE['da_xem']` phải dùng `chuoi()` vì cookie có thể bị sửa thành mảng `da_xem[]=1`!).

2. **Thiết lập Header bảo mật & CSP trong `inc/config.php`** (đặt **TRƯỚC** `session_start()` và bất kỳ output nào):
   - Dựa trên đúng tài nguyên ngoài thực tế mà `ltweb-doan-nhom02` đang dùng (không copy mù quáng, không dùng `'unsafe-inline'`, không dùng `*`):
     - Hệ thống dùng ảnh inline data URL / SVG nếu có -> thêm `img-src 'self' data:;`
     - `chi-tiet-giao-an.php` nhúng `<iframe src="https://www.youtube.com/embed/...">` -> cần `frame-src 'self' https://www.youtube.com https://www.youtube-nocookie.com;`
     - `js/trang-chu.js` gọi `fetch('https://api.open-meteo.com/v1/forecast?...')` ở `index.php` -> cần `connect-src 'self' https://api.open-meteo.com;`
     - Kiểm tra kỹ xem có dùng CDN Bootstrap hay Google Fonts ở trang nào không (hiện tại CSS nằm ở `css/*.css` nội bộ; nếu có tải font/CDN ở trang nào phải kiểm tra và bổ sung đúng domain vào `style-src`/`font-src`, hoặc tải về cục bộ).
   - Chuỗi header đề xuất cho `inc/config.php`:
     ```php
     header(
         "Content-Security-Policy: default-src 'self'; "
         . "img-src 'self' data:; "
         . "frame-src 'self' https://www.youtube.com https://www.youtube-nocookie.com; "
         . "connect-src 'self' https://api.open-meteo.com; "
         . "object-src 'none'; base-uri 'self'; form-action 'self'; frame-ancestors 'none'"
     );
     header('X-Content-Type-Options: nosniff');
     header('Referrer-Policy: strict-origin-when-cross-origin');
     header_remove('X-Powered-By');
     if (!empty($_SERVER['HTTPS'])) {
         header('Strict-Transport-Security: max-age=31536000');
     }
     ```
   - **Chú ý trang lỗi độc lập `403.php`, `404.php`, `500.php`, `xoa-giao-an.php` (405):**
     - `403.php`, `404.php`, `xoa-giao-an.php`: được require sau `inc/config.php` nên đã có header CSP.
     - `500.php`: khi `inc/config.php` bắt ngoại lệ hoặc khi mở trực tiếp nếu lỗi từ `db()`, header CSP đã được gửi trước đó (trừ trường hợp lỗi xảy ra trước khi gửi header), nhưng vẫn phải xóa toàn bộ `style="..."` và `onclick="window.location.reload();"` trong `500.php`!

3. **Dọn SẠCH mọi `<script>` nội tuyến, `onclick="..."`, `onsubmit="..."`, `style="..."` ở các tệp dùng chung:**
   > Kết quả rà quét hiện tại của dự án cho thấy CSP `default-src 'self'` (chặn cả `script-src` và `style-src` nội tuyến) sẽ báo vi phạm Console ngay ở các vị trí sau — **Tấn chịu trách nhiệm dọn sạch cụm chung & hướng dẫn 3 bạn dọn phần riêng**:
   - **`inc/header.php`:**
     - Bỏ `<script>document.documentElement.classList.add('js');</script>` (hoặc xử lý khởi tạo trong `js/main.js` — lưu ý `inc/header.php` dòng 54 đã có `<html lang="vi" class="js">`, nếu muốn không flash menu mobile khi tắt JS thì giữ hoặc đồng bộ bằng `js/main.js` theo hướng dẫn đề; tuyệt đối bỏ `<script>` nội tuyến thứ 2 dài 27 dòng ở cuối `inc/header.php` vì `js/main.js` ĐÃ CÓ sẵn logic `batTatMenuMobile` và `Escape` y hệt rồi!).
     - Xóa `onclick="window.batTatMenuMobile && window.batTatMenuMobile();"` trên nút `<button class="nut-menu">`.
     - Chuyển toàn bộ **13 inline `style="..."`** trong `inc/header.php` thành class CSS trong `css/04-thanh-phan.css`.
   - **`kho-hoc-lieu.php`:**
     - Xóa `onclick="this.classList.toggle('da-thich'); ..."` ở nút `.nut-luu-giao-an` (chuyển sang xử lý class `.da-thich` trong `js/gio-hang.js` hoặc `js/trang-danh-sach.js` + CSS `.nut-yeu-thich.da-thich { color: #e74c3c; }`).
     - Chuyển **23 `style="..."`** sang class CSS.
   - **`500.php`:**
     - Xóa `onclick="window.location.reload();"` (tạo `<script type="module" src="js/main.js">` hoặc dùng nút `<a href="index.php">` / sự kiện gắn trong `js/main.js` lắng nghe `.nut-tai-lai`). Chú ý **tuyệt đối không để `500.php` gọi CSDL**.
     - Chuyển **12 `style="..."`** sang `css/04-thanh-phan.css`.
   - **Các trang công khai khác (`index.php`, `chi-tiet-giao-an.php`, `gio-hang.php`, `ve-chung-toi.php`, `dong-gop-tai-lieu.php`, `403.php`, `404.php`):**
     - Chuyển toàn bộ `style="..."` sang class trong `css/04-thanh-phan.css`, `css/05-tien-ich.css`, `css/gio-hang.css`.
     - Tại `gio-hang.php`: bỏ `onsubmit="return confirm(...)"` trên form Xóa tất cả $\rightarrow$ thay bằng thuộc tính `data-confirm="Bạn có chắc chắn muốn xoá tất cả giáo án khỏi giỏ hàng?"` và cài đặt listener chung trong `js/main.js`:
       ```js
       document.addEventListener('submit', (e) => {
           const form = e.target;
           const msg = form.getAttribute('data-confirm');
           if (msg && !window.confirm(msg)) {
               e.preventDefault();
           }
       });
       ```
     - Kiểm tra **`js/tim-kiem.js`** và **`js/gio-hang.js`**: chú ý **KHÔNG dùng `.style.cssText = ...`** hay gán thuộc tính `style` làm trigger nếu không cần thiết (vì CSP `style-src 'self'` chặn `<style>` và `style="..."` HTML attribute; tuy nhiên `el.style.color = ...` trong JS thường vẫn chạy trừ khi bật `style-src-attr 'none'`, tốt nhất vẫn ưu tiên dùng `classList.add/toggle(...)` + CSS class trong `css/04-thanh-phan.css` cho cả `js/gio-hang.js` và `js/tim-kiem.js` để sạch 100% và dễ bảo trì!).

4. **Rà soát bằng công cụ (Bảng 4 — 4 lệnh `grep` Slide 39):**
   Tấn chạy 4 lệnh `grep` trên toàn bộ kho nguồn trong Git Bash (hoặc PowerShell quy đổi tương đương), lưu kết quả nguyên văn dạng chữ (KHÔNG chụp ảnh) và phân loại từng dòng phát hiện sang Bảng 2:
   ```bash
   # Lệnh 1: Tìm biến PHP ghép vào lời gọi query / prepare / exec
   grep -rnE "\->(query|prepare|exec)\s*\(.*\\$" --include="*.php" --exclude-dir=vendor .

   # Lệnh 2: Tìm <?= in trực tiếp chưa qua hàm escape (e, vnd, csrfField, anhGiaoAn...)
   grep -rnE "<\?=\s*\\$" --include="*.php" --exclude-dir=vendor .

   # Lệnh 3: Tìm innerHTML trong thư mục js/
   grep -rn "innerHTML" js/

   # Lệnh 4: Kiểm tra trạng thái .env trong Git
   git ls-files .env
   git log --all --oneline -- .env
   ```
   *(Ghi chú: Tương tự với `thanhvien/*/js/` — nên kiểm tra cả `grep -rn "innerHTML" thanhvien/` để không bị sót!)*

#### B. Rà soát chéo Bảng 2 & Phần B của Tấn (`thanhvien/3120124027_tan/gioithieu.php`)
- **Rà soát chéo Bảng 2:**
  - **Nhóm 2 (SQL):** Đọc toàn bộ câu lệnh `prepare`/`query` trong `src/Data/*.php`, `inc/*.php`, `api/*.php`, `don-hang.php`, `them/sua/xoa-giao-an.php`, và 4 trang thành viên. Đặc biệt kiểm tra mọi chỗ nối biến (như `$where`, `$orderBy`, `$dauHoi`) xem có đi qua Whitelist / placeholder `?` hay không và ghi rõ kết luận *"an toàn"* kèm giải thích vào Bảng 2.
  - **Nhóm 3 (Đầu ra / XSS):** Kiểm tra mọi dòng kết quả từ Lệnh 2 & Lệnh 3 (`<?= $... ?>` và JS DOM): số nguyên từ DB (`(int)`) hoặc hàm tạo HTML an toàn vs chuỗi người dùng nhập chưa bọc `e()`; rà soát xem `nl2br()` có luôn đặt **SAU** `e()` không (`nl2br(e(...))` — nếu viết ngược `e(nl2br(...))` hoặc quên `e()` là phải sửa!).
- **Phần B cá nhân (`thanhvien/3120124027_tan/gioithieu.php`):**
  - Thêm `<?= csrfField() ?>` vào **cả 2 form POST**: form `gui_luu_but` và form `xoa` (nút xóa của chủ trang Tấn).
  - Thêm `chuoi()` cho `$_GET['q']`, `$_GET['cong_nghe']`, `$_GET['sx']`, `$_POST['ten_khach']`, `$_POST['loi_nhan']`; đảm bảo `?trang_lb[]=1` hay `ten_khach[]=a` không gây Warning/TypeError.
  - Dọn **29 `style="..."`** và **1 `onsubmit="return confirm(...)"`** sang `thanhvien/3120124027_tan/css/canhan-tan.css` và `data-confirm="..."`.
  - Mở `thanhvien/3120124027_tan/js/canhan.js` kiểm tra xem có dùng `innerHTML` hay fetch ngoài cần thêm CSP không (nếu có thì xử lý triệt để).
  - Thực hiện đủ **5 ca kiểm thử Phần B (a–e)** và commit bằng tài khoản **`beotan19-cpu`**.

#### C. Bảng 3 & Phần C của Tấn
- **Bảng 3:** Thực hiện và ghi đầy đủ lệnh/ảnh/Console cho **Ca 1 (Header & Cookie)**, **Ca 7 (XSS `<b...>`, `" ' &` & thử thêm `<script>` nội tuyến kiểm tra Console CSP rồi xóa ngay)**, và **Ca 8 (Tham số dạng mảng `?q[]=ao`, `?id[]=1`, `?sx[]=x`, form liên hệ `ho_ten[]=...` kèm CSRF đúng)**.
- **Phần C — Câu 2 (*`HttpOnly` còn chặn được gì khi trang đã bị XSS?*):**
  - Mở website nhóm, gõ `document.cookie` trong Chrome DevTools Console: liệt kê chính xác thấy những cookie nào (`da_xem`? `theme`?), có `PHPSESSID` không, tại sao; kiểm tra tất cả cookie phụ của nhóm xem đã có `HttpOnly`, `SameSite=Lax` chưa.
  - Trong Console, chạy thử lệnh `fetch('api/gio-hang.php', { method: 'POST', headers: {'Content-Type': 'application/json', 'X-CSRF-Token': document.querySelector('meta[name=\"csrf-token\"]').content}, body: JSON.stringify({id: 1, sl: 1}) })`: quan sát kết quả `200 OK` và số giỏ hàng tăng $\rightarrow$ phân tích: nếu trang bị XSS, kẻ tấn công không đọc được `PHPSESSID`, nhưng vẫn mượn phiên và CSRF token hợp lệ của nạn nhân để thực hiện hành động thay nạn nhân!
  - Tạm chèn `<script>console.log('XSS test');</script>` vào 1 trang (sau đó xóa ngay): chụp/ghi lại thông báo chặn của CSP trong Console $\rightarrow$ giải thích vai trò bổ trợ của 3 lớp `e()`, `HttpOnly`, và `CSP` (lớp nào là lớp phòng thủ chính).

---

### 4.3. THÀNH VIÊN 3: LÊ THỊ XUÂN CHÂU — MSSV: `3120124006` — GitHub: `XuanChau-06`

#### A. Biện pháp 4 (`.env` & `phpdotenv`) + Biện pháp 5 (`.htaccess` & `uploads/.htaccess`) + Bảo mật cụm Quản trị
1. **Biện pháp 4 — Cấu hình & bí mật trong `.env` (`vlucas/phpdotenv`):**
   - Kiểm tra `composer.json`: hiện có 2 dòng `"disable-tls": true, "secure-http": false` không an toàn $\rightarrow$ **gỡ bỏ 2 dòng này** (nếu mạng XAMPP lỗi CA cert thì xử lý cấu hình cert máy cá nhân thay vì tắt TLS toàn dự án), rồi chạy tại thư mục gốc dự án:
     ```bash
     composer require vlucas/phpdotenv
     ```
     Xác nhận `composer.json` có `"vlucas/phpdotenv": "^5.7"` (hoặc bản tương đương mới nhất).
   - **Tạo `.env.example`** (tệp này ĐƯỢC commit lên GitHub, KHÔNG chứa mật khẩu thật):
     ```env
     # .env.example — Chép thành .env rồi cấu hình trên máy chạy
     APP_DEBUG=true
     DB_HOST=localhost
     DB_PORT=3306
     DB_DATABASE=ltweb_nhom02
     DB_USERNAME=root
     DB_PASSWORD=
     ```
   - **Chép `.env.example` thành `.env`** trên máy cá nhân để chạy (KHÔNG commit).
   - **Sửa lại tệp `.gitignore` sạch chuẩn UTF-8 (không BOM, xóa bỏ các dòng rác `yeucau.md`, `y e u c a u 2 . m d`, `y e u c a u b t 5 . m d` bị lỗi encoding ở cuối file):**
     ```gitignore
     # Không đưa lên GitHub
     .env
     /vendor/
     /logs/*.log
     /uploads/*
     !/uploads/.htaccess
     !/uploads/.gitkeep
     /storage/*
     !/storage/.htaccess

     # Editor & OS
     .DS_Store
     Thumbs.db
     .idea/
     .vscode/
     *.swp
     ```
     > **CHÚ Ý CỰC KỲ QUAN TRỌNG:** Kiểm tra ngay `git ls-files .env` và `git log --all --oneline -- .env` để chắc chắn `.env` chưa từng xuất hiện trong kho và lịch sử Git!
   - **Cập nhật `inc/config.php` đọc `.env` & thay hằng `MOI_TRUONG` bằng `DEBUG` (`APP_DEBUG`):**
     ```php
     require_once __DIR__ . '/../vendor/autoload.php';

     $env = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
     $env->load();
     $env->required(['DB_HOST', 'DB_DATABASE', 'DB_USERNAME']);

     define('DEBUG', ($_ENV['APP_DEBUG'] ?? '') === 'true'); // Thiếu APP_DEBUG: tự động tắt (an toàn)
     error_reporting(E_ALL);
     ini_set('display_errors', DEBUG ? '1' : '0');
     ini_set('log_errors', '1');
     ini_set('error_log', __DIR__ . '/../logs/php-error.log');
     ```
     - Cập nhật `set_exception_handler(...)` trong `inc/config.php`: nếu `DEBUG === true` thì in `<pre><?= e((string)$e) ?></pre>` (nhớ bọc `e()`!), nếu `DEBUG === false` thì đặt `http_response_code(500); require __DIR__ . '/../500.php'; exit;` (lưu ý: nếu lỗi từ API thì file `api/*.php` có handler trả JSON riêng hoặc xử lý tương thích khi gọi lỗi kết nối DB!).
     - Tìm toàn bộ `MOI_TRUONG` trong dự án để bảo đảm không còn chỗ nào sót hằng cũ này!
   - **Cập nhật `inc/db.php` đọc thông tin từ `$_ENV`:**
     ```php
     $host    = $_ENV['DB_HOST'];
     $port    = $_ENV['DB_PORT'] ?? '3306';
     $dbname  = $_ENV['DB_DATABASE'];
     $charset = 'utf8mb4';
     $user    = $_ENV['DB_USERNAME'];
     $pass    = $_ENV['DB_PASSWORD'] ?? '';
     ```
     - Bỏ hoàn toàn mật khẩu/tên DB viết cứng `'ltweb_nhom02'`, `'root'` trong `inc/db.php`.

2. **Biện pháp 5 — Chặn tệp nội bộ trên Apache (`.htaccess` & `uploads/.htaccess`):**
   - **Tạo `.htaccess` ở thư mục gốc `c:\xampp\htdocs\ltweb-doan-nhom02\.htaccess`:**
     ```apache
     # .htaccess — Thư mục gốc dự án (Apache / XAMPP / Hosting)
     # Không liệt kê nội dung thư mục
     Options -Indexes

     # Tệp và thư mục ẩn (.env, .git...) — chừa .well-known
     RedirectMatch 404 /\.(?!well-known/)

     # Thư mục nội bộ của dự án
     RedirectMatch 404 /(inc|src|vendor|database|logs|storage|tests)(/|$)

     # Chặn tệp SQL, log, sao lưu, cấu hình và tệp khóa Composer
     <FilesMatch "\.(sql|log|bak|ini|lock)$|^composer\.json$">
         Require all denied
     </FilesMatch>

     # Khi triển khai lên hosting có HTTPS, bỏ dấu # ở ba dòng dưới:
     # RewriteEngine On
     # RewriteCond %{HTTPS} !=on
     # RewriteRule ^ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
     ```
     > **Cảnh báo:** Trong tệp `.htaccess`, chú thích `# ...` **PHẢI đặt trên dòng riêng**, tuyệt đối không viết sau chỉ thị trên cùng 1 dòng (ví dụ `Options -Indexes # abc` sẽ làm Apache báo lỗi **500 cho toàn bộ website**!).
   - **Tạo `uploads/.htaccess`:**
     ```apache
     # uploads/.htaccess — Chỉ chứa ảnh tải lên, cấm chạy mã và cấm liệt kê
     Options -Indexes

     # Chặn chạy tệp thực thi
     <FilesMatch "\.(php\d?|phtml|phar|cgi|pl|py|sh)$">
         Require all denied
     </FilesMatch>

     <IfModule mod_headers.c>
         Header set X-Content-Type-Options "nosniff"
     </IfModule>
     ```
   - Kiểm tra lại `logs/.htaccess` và `storage/.htaccess` đã có (`Require all denied`) — giữ nguyên hoặc thêm `Options -Indexes`.

3. **Gia cố cụm trang Quản trị CRUD (`quan-tri.php`, `them-giao-an.php`, `sua-giao-an.php`, `xoa-giao-an.php`, `inc/kiem-tra.php`):**
   - Thêm `<?= csrfField() ?>` vào form xóa trong `quan-tri.php`, form thêm ở `them-giao-an.php`, form sửa ở `sua-giao-an.php`.
   - Áp dụng `chuoi()` cho các đầu vào chuỗi (`$_POST['ten']`, `$_POST['cap_hoc']`, `$_POST['video_url']`, `$_POST['mo_ta']`, v.v.) trong `them-giao-an.php`, `sua-giao-an.php`, `inc/kiem-tra.php` để gửi mảng `ten[]=abc` không bị `TypeError`.
   - Gỡ toàn bộ `style="..."` và `onsubmit="return confirm(...)"` ở `quan-tri.php` (69 `style`, 1 `onsubmit`), `them-giao-an.php` (43 `style`), `sua-giao-an.php` (46 `style`), `xoa-giao-an.php` (6 `style`) $\rightarrow$ thay bằng class CSS + `data-confirm="..."`.

#### B. Rà soát chéo Bảng 2 & Phần B của Châu (`thanhvien/3120124006_chau/gioithieu.php`)
- **Rà soát chéo Bảng 2:**
  - **Nhóm 1 (Đầu vào):** Kiểm tra mọi biến `$_GET`, `$_POST`, `$_COOKIE` trên toàn dự án xem đã có `chuoi()` cho chuỗi và `filter_var(..., FILTER_VALIDATE_INT)` cho số nguyên chưa; rà soát mảng gửi lên gây lỗi.
  - **Nhóm 7 (Upload):** Kiểm tra cả **5 nơi upload** trong dự án (`them-giao-an.php`, `sua-giao-an.php`, `ve-chung-toi.php`, **`dong-gop-tai-lieu.php`**, `thanhvien/3120124006_chau/gioithieu.php`): có kiểm tra `UPLOAD_ERR_OK`, kích thước hợp lệ, kiểm tra MIME thật bằng `finfo_file()`, đặt tên file ngẫu nhiên (`uniqid`/`random_bytes`), đuôi file từ bảng ánh xạ MIME whitelist chứ không lấy đuôi gốc từ `$_FILES['name']` (lưu ý `dong-gop-tai-lieu.php` hiện chưa kiểm tra `finfo_file()` và còn ghép `$file['name']` — phối hợp với Bảo sửa ngay!).
  - **Nhóm 8 (Cấu hình):** Kiểm tra `.env.example`, `.gitignore`, `.htaccess`, `uploads/.htaccess`, `500.php` không lộ lỗi SQL/đường dẫn khi `APP_DEBUG=false`.
- **Phần B cá nhân (`thanhvien/3120124006_chau/gioithieu.php`):**
  - *(Chú ý: Trang của Châu hiện là trang có nhiều thứ cần dọn CSP nhất ở Phần B!)*:
    1. Thêm `<?= csrfField() ?>` vào **cả 5 form POST**: `doi-theme`, `tai-avatar`, `khoi-phuc-avatar`, `them-du-an`, `xoa-du-an`.
    2. Áp dụng `chuoi()` cho `$_POST['ten_du_an']`, `$_POST['mo_ta']`, `$_POST['lien_ket']`, `$_POST['action']`, `$_COOKIE['theme']`.
    3. **Xóa 1 thẻ `<script>` nội tuyến** (`<script>document.body.classList.add('dark-theme');</script>`): Thay bằng thêm thẳng class ở thẻ `<body>` hoặc thêm CSS/JS ngoài trong `thanhvien/3120124006_chau/js/canhan.js` (cách sạch nhất: truyền biến `$lop_body = ($themeHienTai === 'dark') ? 'dark-theme' : '';` hoặc đọc từ `js/canhan.js` / class CSS trên `<main class="trang__chinh <?= $themeHienTai === 'dark' ? 'dark-theme' : '' ?>">`).
    4. **Xóa toàn bộ 5 inline `on*=`:** 2 cặp `oninvalid`/`oninput` trên `#ten_du_an` & `#mo_ta` (chuyển thông báo tiếng Việt sang `js/canhan.js` qua `addEventListener('invalid', ...)` và `input`), và 1 `onsubmit="return confirm(...)"` chuyển sang `data-confirm="..."`.
    5. **Chuyển 45 `style="..."`** sang `thanhvien/3120124006_chau/css/xchau.css`.
    6. Bổ sung `'secure' => !empty($_SERVER['HTTPS'])` vào `setcookie('theme', ...)` (đã có `httponly => true`, `samesite => 'Lax'`).
    7. Kiểm tra xem `lien_ket` dự án in vào `<a href="<?= e($da['lien_ket']) ?>">`: **cảnh báo XSS `javascript:alert(1)`!** Phải kiểm tra `lien_ket` chỉ chấp nhận `http://` hoặc `https://` (`str_starts_with($lienKet, 'http://') || str_starts_with($lienKet, 'https://')`) trước khi in ra `href`, nếu không hợp lệ thì báo lỗi khi thêm hoặc không tạo thẻ `<a>`!
  - Test đủ **5 ca (a–e)** cho trang Châu + commit bằng tài khoản **`XuanChau-06`**.

#### C. Bảng 3 & Phần C của Châu
- **Bảng 3:** Thực hiện và ghi đầy đủ lệnh + ảnh cho **Ca 9 (`.env` & `APP_DEBUG=false`)**, **Ca 10 (`.htaccess` khi chạy trong XAMPP Apache `http://localhost/ltweb-doan-nhom02/...`)**, và **Ca 14 (ca đề xuất riêng #2 — ví dụ: kiểm thử upload giả mạo tệp `.php` đổi đuôi/MIME lên `them-giao-an.php` hoặc `ve-chung-toi.php` và kiểm thử xác thực link `javascript:` ở trang cá nhân)**.
- **Phần C — Câu 3 (*`.htaccess` và máy chủ `php -S`*):**
  - Chạy dự án bằng **`php -S localhost:8000`**, dùng `curl.exe -i` (hoặc trình duyệt) mở `/.env`, `/.git/config`, `/database/schema.sql`, `/logs/bao-mat.log`, `/inc/db.php` $\rightarrow$ đối chiếu bảng mã trạng thái và nội dung nhận được với khi chạy trên **Apache của XAMPP** (`Ca 10`). Giải thích vì sao `.htaccess` vô hiệu với `php -S`.
  - Tạo **tạm** tệp `uploads/test.php` (`<?php echo "CHAY MA PHP"; ?>`), mở qua `php -S localhost:8000/uploads/test.php` và qua `http://localhost/ltweb-doan-nhom02/uploads/test.php` (**NHỚ XÓA NGAY SAU KHI THỬ!**). Phân tích kết quả và giải thích vì sao kiểm tra `finfo`, đuôi MIME, tên ngẫu nhiên ở Bài tập 5 vẫn bắt buộc.
  - Kết luận có nên dùng `php -S` chạy production không; nêu kiến trúc thư mục gốc web `public/` (đưa `inc/`, `src/`, `vendor/`, `.env`, `logs/`, `database/` ra khỏi `DocumentRoot`).

---

### 4.4. THÀNH VIÊN 4: NGUYỄN TIẾN THỊNH — MSSV: `3120222126` — GitHub: `cowt1310`

#### A. Biện pháp 2 (Cookie & Phiên an toàn) + Biện pháp 6 (Tài khoản CSDL quyền tối thiểu) + Biện pháp 7 (Chống Brute-force & Ghi log bảo mật)
1. **Biện pháp 2 — Cookie phiên và phiên an toàn (`inc/config.php`, `inc/xac-thuc.php`, cookie phụ):**
   - Trong `inc/config.php`, đặt cấu hình phiên **TRƯỚC `session_start()`** (và sau khi đã gửi header bảo mật):
     ```php
     ini_set('session.use_strict_mode', '1');
     session_set_cookie_params([
         'lifetime' => 0,
         'path'     => '/',
         'secure'   => !empty($_SERVER['HTTPS']),
         'httponly' => true,
         'samesite' => 'Lax',
     ]);

     if (session_status() === PHP_SESSION_NONE) {
         session_start();
     }

     // Hết hạn sau 30 phút không hoạt động
     if (time() - ($_SESSION['hoat_dong'] ?? time()) > 30 * 60) {
         $_SESSION = [];
         session_regenerate_id(true);
     }
     $_SESSION['hoat_dong'] = time();
     ```
   - **Kiểm tra lại khối CSRF chạy SAU `session_start()`:** Khi phiên quá 30 phút bị reset + đổi ID phiên, token `$_SESSION['csrf']` cũng mất nên form cũ gửi lên sau 30 phút sẽ trả `403 "Trang đã hết hạn..."` đúng như Ca 6 Bảng 3!
   - **Viết / chuẩn hóa hàm `dangNhap(int $uid)` và `dangXuat()` trong `inc/xac-thuc.php`:**
     ```php
     function dangNhap(int $uid): void
     {
         session_regenerate_id(true);
         $_SESSION['uid'] = $uid;
         $_SESSION['hoat_dong'] = time();
     }

     function dangXuat(): void
     {
         $_SESSION = [];
         setcookie(session_name(), '', [
             'expires'  => time() - 3600,
             'path'     => '/',
             'secure'   => !empty($_SERVER['HTTPS']),
             'httponly' => true,
             'samesite' => 'Lax',
         ]);
         session_destroy();
     }
     ```
     - Thay đoạn tự gán `session_regenerate_id(true); $_SESSION['uid'] = ...;` trong `dang-nhap.php` thành gọi `dangNhap((int)$u['id'])` để tập trung ở 1 chỗ.
   - Rà soát lại cookie phụ trong `chi-tiet-giao-an.php` (`da_xem`): đảm bảo đã có đủ `'secure' => !empty($_SERVER['HTTPS'])`, `'httponly' => true`, `'samesite' => 'Lax'`.

2. **Biện pháp 6 — Tài khoản CSDL quyền tối thiểu (`database/tai-khoan.sql`):**
   - Tạo `database/tai-khoan.sql` (chạy 1 lần bằng tài khoản `root` sau khi đã chạy `schema.sql` và `seed.sql`):
     ```sql
     -- database/tai-khoan.sql — Chạy MỘT lần bằng root (phpMyAdmin hoặc mysql CLI)
     -- Tạo tài khoản ứng dụng nhom02_app chỉ có quyền thao tác dữ liệu (SELECT, INSERT, UPDATE, DELETE) trên ltweb_nhom02.*
     CREATE USER IF NOT EXISTS 'nhom02_app'@'localhost'
       IDENTIFIED BY 'thay-bang-mat-khau-dai-ngau-nhien';

     GRANT SELECT, INSERT, UPDATE, DELETE
       ON ltweb_nhom02.* TO 'nhom02_app'@'localhost';

     FLUSH PRIVILEGES;
     ```
   - Dùng `root` chạy thử tệp này trên MariaDB của máy cá nhân, tạo mật khẩu ngẫu nhiên trên `.env` máy mình, đổi `DB_USERNAME=nhom02_app`, `DB_PASSWORD=...` trong `.env` và kiểm thử lại toàn bộ chức năng BTN6 & 7 xem chạy trơn tru 100% không; kiểm tra `DROP TABLE danh_muc;` bằng `mysql -u nhom02_app -p ltweb_nhom02` báo lỗi **`1142` (`command denied to user`)**.

3. **Biện pháp 7 — Chống đoán mật khẩu và ghi log bảo mật:**
   - **Cập nhật `database/schema.sql`** (thêm bảng `dang_nhap_sai`):
     ```sql
     -- Bảng ghi lại các lần đăng nhập sai để chặn đoán mật khẩu (Chương 7)
     CREATE TABLE dang_nhap_sai (
       id        INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
       email     VARCHAR(255) NOT NULL,
       ip        VARCHAR(45) NOT NULL,
       thoi_gian DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
       INDEX idx_dang_nhap_sai_email_tg (email, thoi_gian)
     ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
     ```
     *(Nhớ chạy câu `CREATE TABLE` này trên MariaDB máy cục bộ và đảm bảo `database/schema.sql` trên Git có đầy đủ để giảng viên nạp mới từ đầu không bị thiếu bảng!)*
   - **Thêm hàm `ghiLogBaoMat()` vào `inc/ham.php`:**
     ```php
     // Ghi mỗi sự kiện bảo mật trên 1 dòng JSON trong logs/bao-mat.log
     function ghiLogBaoMat(string $suKien, array $chiTiet = []): void
     {
         $chiTiet += [
             'ip'  => $_SERVER['REMOTE_ADDR'] ?? '-',
             'uid' => $_SESSION['uid'] ?? null,
         ];
         $json = json_encode($chiTiet, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
         $dong = date('Y-m-d H:i:s') . " {$suKien} {$json}";
         $thuMucLog = __DIR__ . '/../logs';
         if (!is_dir($thuMucLog)) {
             mkdir($thuMucLog, 0755, true);
         }
         error_log($dong . PHP_EOL, 3, $thuMucLog . '/bao-mat.log');
     }
     ```
     > **TUYỆT ĐỐI KHÔNG** đưa `mat_khau`, `PHPSESSID`, hay `csrf` vào mảng `$chiTiet`!
   - **Cập nhật `inc/xac-thuc.php`:**
     - Trong `yeuCauQuyen(string $vaiTro)`: **Bổ sung `ghiLogBaoMat('cam_truy_cap', ['trang' => $_SERVER['REQUEST_URI'] ?? '-']);` ngay trước khi trả mã HTTP `403`**. (Cũng chú ý sửa chữ ký / giá trị trả về nếu muốn trả về `$nd` hoặc giữ `void` phù hợp mọi chỗ gọi).
     - Thêm hai hàm chống đoán mật khẩu ở cuối `inc/xac-thuc.php`:
       ```php
       function soLanSai(string $email): int
       {
           $st = db()->prepare(
               'SELECT COUNT(*) FROM dang_nhap_sai WHERE email = ? AND thoi_gian > NOW() - INTERVAL 15 MINUTE'
           );
           $st->execute([$email]);
           return (int) $st->fetchColumn();
       }

       function ghiLanSai(string $email): void
       {
           $ip = $_SERVER['REMOTE_ADDR'] ?? '-';
           $st = db()->prepare('INSERT INTO dang_nhap_sai (email, ip) VALUES (?, ?)');
           $st->execute([$email, $ip]);
           ghiLogBaoMat('dang_nhap_sai', ['email' => $email]);
       }
       ```
   - **Cập nhật `dang-nhap.php`:**
     - Gắn `<?= csrfField() ?>` vào form đăng nhập.
     - Dùng `chuoi()` cho `$_POST['email']`, `$_POST['mat_khau']`.
     - **Kiểm tra `soLanSai($email) >= 5` TRƯỚC khi truy vấn `nguoi_dung` và `password_verify()`** (để tránh CPU tốn bcrypt khi đang bị khóa!):
       - Nếu `>= 5`: đặt `http_response_code(429);` và hiện thông báo `'Sai quá nhiều lần, hãy thử lại sau 15 phút'` (chú ý thông báo giữ chung nhất quán, không lộ tài khoản có tồn tại hay không).
       - Nếu `< 5`: kiểm tra email + `password_verify(...)` (+ `password_needs_rehash(...)` giữ nguyên như BTN6).
         - **Đúng:** Xóa sạch lịch sử sai của email đó (`DELETE FROM dang_nhap_sai WHERE email = ?`), gọi `dangNhap((int)$u['id'])`, chuyển hướng PRG.
         - **Sai:** Gọi `ghiLanSai($email)` (vừa ghi bảng `dang_nhap_sai`, vừa ghi dòng `dang_nhap_sai` vào `logs/bao-mat.log`), hiển thị thông báo lỗi chung.
     - Chuyển các `style="..."` trong `dang-nhap.php`, `dang-ky.php`, `dat-hang.php`, `don-hang.php` về CSS class + gắn `<?= csrfField() ?>` + `chuoi()`.

#### B. Rà soát chéo Bảng 2 & Phần B của Thịnh (`thanhvien/3120222126_thinh/gioithieu.php`)
- **Rà soát chéo Bảng 2:**
  - **Nhóm 4 (Phân quyền):** Kiểm tra toàn bộ trang quản trị, `don-hang.php` (`AND nguoi_dung_id = ?`), 4 trang cá nhân Phần B (`$laChuTrang` + `AND mssv = ?`), kiểm tra xem có chỗ nào chỉ ẩn nút trên HTML mà quên kiểm tra lại trên máy chủ khi nhận POST hay không; đặc biệt kiểm tra xem `csrf` có thay kiểm tra quyền không.
  - **Nhóm 6 (Phiên, mật khẩu):** Kiểm tra `password_hash()`, `password_verify()`, `password_needs_rehash()`, thông báo lỗi chung đăng nhập, Session chỉ giữ `uid`, `session_regenerate_id(true)`, `use_strict_mode = 1`, `HttpOnly`, `SameSite=Lax`, timeout 30 phút, `dangXuat()` xóa session + cookie.
- **Phần B cá nhân (`thanhvien/3120222126_thinh/gioithieu.php`):**
  - Thêm `<?= csrfField() ?>` vào **tất cả form POST**: form `tinh_diem` (tính GPA), form `gui_yeu_cau_cv`, form `xoa_cv` (xóa 1 dòng), form xóa tất cả CV (nếu có).
  - Dùng `chuoi()` cho `$_POST['email']`, `$_POST['loi_nhan']`, `$_POST['action']`, kiểm tra `?trang_cv[]=1` bằng `filter_var(..., FILTER_VALIDATE_INT)`.
  - Dọn **124 chỗ `style="..."`** (tạo file/class trong `thanhvien/3120222126_thinh/css/canhan-thinh.css`) và **1 chỗ `onsubmit="return confirm(...)"`** sang `data-confirm="..."`.
  - Test đủ **5 ca (a–e)** cho trang Thịnh + commit bằng tài khoản **`cowt1310`**.

#### C. Bảng 3 & Phần C của Thịnh
- **Bảng 3:** Thực hiện và ghi đầy đủ lệnh `curl` + kết quả DB/log + ảnh chụp cho **Ca 4 (GET xóa/đăng xuất `405` & khách có CSRF gửi POST xóa `403` + `cam_truy_cap`)**, **Ca 5 (`use_strict_mode` & Session Fixation)**, **Ca 6 (Hết hạn phiên tạm đổi `30*60` thành `60` rồi trả lại ngay!)**, **Ca 11 (tài khoản `nhom02_app` chạy đủ + lỗi `1142`)**, và **Ca 12 (5 lần sai -> lần 6 trả `429`, email khác vẫn vào được, lùi `thoi_gian` quá 15p đăng nhập lại được và xóa dòng sai + dòng `dang_nhap_sai` không chứa mật khẩu)**.
- **Phần C — Câu 4 (*Lỡ commit tệp `.env` thì làm gì?*):**
  - Tạo **1 thư mục riêng hoàn toàn ngoài repo đồ án** (VD `C:\temp\thu-env-git`, `git init` cục bộ, **KHÔNG `git push` đi đâu, KHÔNG làm trong `ltweb-doan-nhom02`**):
    1. Tạo `.env` giả có `DB_PASSWORD=MatKhauGia123`, `git add .env && git commit -m "Lo commit .env"`.
    2. Xóa `.env` bằng `git rm .env && git commit -m "Xoa .env o commit sau"`.
    3. Chạy `git log --all --oneline -- .env` và `git show <mã-commit-đầu>:.env` $\rightarrow$ chứng minh mật khẩu giả vẫn nằm nguyên trong lịch sử Git dù commit mới nhất đã xóa!
    4. Thử tạo lại `.env`, thêm `.env` vào `.gitignore` khi tệp đã đang được theo dõi (`tracked`) $\rightarrow$ chạy `git status` giải thích vì sao `.gitignore` không tự gỡ tệp đã theo dõi.
  - Trình bày đúng thứ tự xử lý khi lỡ lộ `.env` chứa mật khẩu thật lên kho công khai (Bước 1 tối quan trọng: **đổi ngay toàn bộ mật khẩu/API key** vì khóa đã bị lộ/clone/cache; sau đó mới dọn lịch sử bằng `git rm --cached`, `git filter-repo` / BFG, force push, giải thích vì sao chuyển repo sang Private hoặc xóa ở commit sau vẫn chưa đủ).
  - Đặt tạm `APP_DEBUG=true` + sai mật khẩu DB trong `.env` (rồi trả lại ngay!): chụp/ghi chính xác trang lỗi lộ những gì so với Slide 13; liệt kê các biến trong `.env` phải đổi khác khi đưa lên hosting thật (`APP_DEBUG=false`, `DB_HOST`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` mạnh dài).

---

## 5. CÁC CHÚ Ý KỸ THUẬT ĐẶC THÙ CỦA DỰ ÁN `ITeduShare`

### 5.1. Thứ tự bắt buộc trong `inc/config.php`
Sai thứ tự bất kỳ dòng nào trong `inc/config.php` đều có thể làm mất header CSP, làm hỏng cookie HttpOnly/SameSite, hoặc gây lỗi `session_start()` / `csrfHopLe()`. Thứ tự chuẩn phải đúng **chính xác** như sau:

1. `require_once __DIR__ . '/../vendor/autoload.php';`
2. Nạp `.env` qua `Dotenv\Dotenv::createImmutable(dirname(__DIR__))->load()` và `$env->required(...)`.
3. Định nghĩa `DEBUG` từ `$_ENV['APP_DEBUG']`, cấu hình `error_reporting(E_ALL)`, `ini_set('display_errors', ...)`, `ini_set('log_errors', '1')`, `ini_set('error_log', ...)`.
4. `require_once` các tệp `ham.php`, `db.php`, `xac-thuc.php`, `csrf.php` (để `ghiLogBaoMat()`, `traJson()`, `csrfHopLe()` sẵn sàng trước khi chặn POST).
5. Gửi toàn bộ **Header bảo mật** (`Content-Security-Policy`, `X-Content-Type-Options: nosniff`, `Referrer-Policy`, `header_remove('X-Powered-By')`, `HSTS` nếu HTTPS) — **PHẢI chạy trước mọi output HTML và trước mọi `exit`**.
6. Đặt `ini_set('session.use_strict_mode', '1')` và `session_set_cookie_params(...)` — **PHẢI chạy trước `session_start()`**.
7. Gọi `session_start()` và kiểm tra quá hạn 30 phút (`$_SESSION['hoat_dong']`).
8. Kiểm tra mã CSRF cho mọi `$_SERVER['REQUEST_METHOD'] === 'POST'` (gọi `ghiLogBaoMat('csrf_sai', ...)` và trả 403 JSON hoặc 403 text).
9. Đăng ký `set_exception_handler(...)` (khi `DEBUG === false` nạp `500.php` không đụng CSDL; lưu ý `api/*.php` có `set_exception_handler` riêng trả `{"loi": "Lỗi máy chủ"}`).

### 5.2. Danh sách những điểm "ẩn" trong code hiện tại dễ làm rớt kiểm thử
1. **Hai thẻ `<script>` + `onclick` trong `inc/header.php`:** Bắt buộc xóa sạch khỏi `inc/header.php` và để `js/main.js` đảm nhiệm, nếu không **mọi trang trên website** đều đỏ lòm lỗi CSP trong DevTools Console!
2. **File `thanhvien/3120124006_chau/gioithieu.php`:** Đang chứa inline `<script>document.body.classList.add('dark-theme');</script>` và 5 thuộc tính `on*=` (`oninvalid`, `oninput`, `onsubmit`). Phải dọn sạch thì trang Châu mới hết báo vi phạm CSP.
3. **Inline `style="..."` đang rải ở hơn 20 tệp PHP (~750 dòng `style=`)**: Vì `yeucau.md` cấm dùng `'unsafe-inline'` và yêu cầu *"chuyển mọi thẻ `<script>` nội tuyến, thuộc tính `onclick="…"`, `style="…"` sang tệp `.js`, `.css` của dự án"*, cả 4 thành viên **bắt buộc** chuyển dần theo khu vực mình phụ trách (Tấn lo cụm trang chung + header/footer/403/404/500; Châu lo cụm Quản trị + trang Châu; Thịnh lo cụm Auth/Đơn hàng + trang Thịnh; Bảo lo rà lại cụm trang chủ/chi tiết/giỏ/liên hệ + trang Bảo). Nên gom các class dùng chung (`the-thong-bao`, `nut-nguy-hiem`, `khung-chinh-hep`, `can-giua`, `luoi-hai-cot`, `nhan-trang-thai`...) vào `css/04-thanh-phan.css` và `css/05-tien-ich.css` để dùng lại rất nhanh!
4. **Cả `api/gio-hang.php` và các nút `Lưu` trong `kho-hoc-lieu.php`:** `js/gio-hang.js` đang có tới 2 chỗ gọi `fetch('api/gio-hang.php')`. Nhớ gắn `X-CSRF-Token` ở cả 2 chỗ.
5. **Cookie `da_xem` trong `chi-tiet-giao-an.php` và `theme` trong `thanhvien/3120124006_chau/gioithieu.php`:** Cả 2 đều đang thiếu `'secure' => !empty($_SERVER['HTTPS'])` (nếu thiếu thì khi triển khai HTTPS sẽ bị mất cờ Secure). Và khi đọc `$_COOKIE['da_xem']`, phải đi qua `chuoi($_COOKIE['da_xem'] ?? '')` trước khi `explode()`.
6. **File `.gitignore` hiện tại có BOM UTF-8 và 3 dòng lỗi khoảng trắng (`y e u c a u ...`)**: Châu nhớ lưu `.gitignore` ở chuẩn **UTF-8 (no BOM)**, ghi đầy đủ `.env` và kiểm tra thử tạo file `.env` giả rồi gõ `git status` — nếu `.env` vẫn hiện trong `Untracked files` là `.gitignore` cấu hình sai!
7. **Không dùng `innerHTML` ở bất kỳ file `.js` nào** (kể cả `thanhvien/*/js/canhan.js`). Dùng `document.createElement` + `textContent` + `replaceChildren()`.

---

## 6. QUY CÁCH BÁO CÁO, KIỂM THỬ HỒI QUY & CHECKLIST TRƯỚC KHI NỘP

### 6.1. Cấu trúc bắt buộc của báo cáo `Nhom02_Baitap7.pdf`
Dùng đúng tên mục theo `yeucau.md`:
1. **Trang bìa:** Tên nhóm (`Nhóm 02`), họ tên + MSSV 4 thành viên, link kho GitHub (`https://github.com/HoangBao233/ltweb-doan-nhom02`), **mã commit cuối cùng**.
2. **Phần A:**
   - Bản đồ rủi ro của website nhóm (theo Slide 15).
   - **Bảng 1** đã điền đầy đủ (tên biện pháp, tệp, người phụ trách, mã commit).
   - Mô tả ngắn cách làm từng biện pháp (1–7) kèm tên tệp.
   - Kết quả chạy lệnh `git diff --stat btn6 HEAD` + nhận xét biện pháp nào chỉ sửa tệp dùng chung, biện pháp nào phải sửa từng trang.
   - **Bảng 2** (đủ 9 nhóm câu hỏi Slide 38, mỗi nhóm ít nhất 1 dòng, mỗi phát hiện 1 dòng, ghi rõ `Tệp:dòng`, kết luận `an toàn` / `báo nhầm` / `đã sửa`, cách sửa, mã commit).
   - **Bảng 3** (đủ 14 ca kiểm thử phủ định: 12 ca chuẩn + 2 ca riêng của đề tài; mỗi ca ghi rõ lệnh `curl`/REST Client đã chạy, mã HTTP, DB/log thực tế).
   - **Bảng 4** cùng nguyên văn kết quả chữ của 4 lệnh `grep`, `git log .env`, `composer audit`, tình trạng Console (không chụp ảnh bảng kết quả chữ).
   - Ảnh chụp minh họa (có chú thích `Hình 1`, `Hình 2`..., chữ rõ ràng, **che bớt `PHPSESSID` & CSRF**): Response Headers, cột `HttpOnly`/`SameSite` của Cookie, DevTools Console không còn lỗi CSP, trang `403`, trang `429`.
3. **Phần B:** Bảng 5 đầy đủ cho 4 thành viên + trình bày các ca (a–e) của từng người.
4. **Phần C:** 4 câu hỏi thảo luận (mỗi câu ~0.5 trang, có dẫn chứng thử nghiệm thực tế trên web của nhóm).
5. **Tài liệu tham khảo:** Ít nhất 3 tài liệu tin cậy đúng chuẩn trích dẫn.
6. **Phụ lục A — Bảng phân công công việc và tự đánh giá** + **Giải trình minh bạch sử dụng công cụ AI** (nếu có dùng ở mục nào phải ghi rõ).

### 6.2. Yêu cầu cập nhật `README.md` (BẮT BUỘC — sai không tính điểm biện pháp!)
Mục "Cài đặt và chạy" trong `README.md` phải ghi rõ từng bước đúng như giảng viên sẽ làm khi chấm:
1. `composer install`
2. Tạo và nạp CSDL:
   ```bash
   mysql -u root < database/schema.sql
   mysql -u root ltweb_nhom02 < database/seed.sql
   ```
3. Sao chép cấu hình môi trường:
   ```bash
   cp .env.example .env    # Windows: copy .env.example .env
   ```
4. *(Tùy chọn bật quyền tối thiểu)* Chạy `database/tai-khoan.sql` bằng quyền `root` (phpMyAdmin $\rightarrow$ SQL), sau đó đổi `DB_USERNAME=nhom02_app` và `DB_PASSWORD=...` trong `.env`.
5. Chạy thử bằng PHP built-in server: `php -S localhost:8000` **hoặc** chạy trong `htdocs` của XAMPP (`http://localhost/ltweb-doan-nhom02/`, lưu ý `.htaccess` chỉ có hiệu lực trên Apache).
6. Tài khoản thử & mật khẩu (`admin@ued.vn`, `lan@ued.vn`, `an@ued.vn`, `bao@ued.vn`, `tan@ued.vn`, `chau@ued.vn`, `thinh@ued.vn` — `matkhau123`).
7. Bảng **"Biện pháp – Tệp – Slide"** tóm tắt như Bảng 1.

### 6.3. Checklist nghiệm thu cuối cùng trước khi nộp lên `nhhai.net`
- [ ] Gắn tag `btn6` trên commit nộp BTN6 (`374fac7`) và đã `git push origin btn6`.
- [ ] Cả 4 tài khoản (`HoangBao233`, `beotan19-cpu`, `XuanChau-06`, `cowt1310`) đều có commit & push cho Phần A + Phần B + sửa lỗi Bảng 2.
- [ ] **Toàn bộ `tests/bao-mat.http` (hoặc `tests/bao-mat.sh`) và `api.http` + 14 ca Bảng 3 của BTN6 đều ĐẠT** khi chạy bằng cả `root` lẫn `nhom02_app`, trên cả `php -S localhost:8000` và Apache XAMPP.
- [ ] **Mọi trang** mở trên Chrome/Firefox đều có **`0` lỗi JS, `0` vi phạm CSP trong Console**, **`0` PHP Warning/Notice/Deprecated/Fatal**, `logs/php-error.log` sạch sau các ca chuẩn.
- [ ] `logs/bao-mat.log` ghi nhận đủ 3 loại sự kiện (`csrf_sai`, `cam_truy_cap`, `dang_nhap_sai`) và **không chứa** mật khẩu, mã `PHPSESSID`, mã CSRF.
- [ ] `composer audit` báo không có lỗ hổng nào.
- [ ] `git ls-files .env` và `git log --all --oneline -- .env` đều **không in ra dòng nào**.
- [ ] Kho GitHub có: `.env.example`, `.gitignore`, `.htaccess`, `uploads/.htaccess`, `database/tai-khoan.sql`, `tests/bao-mat.http` (hoặc `.sh`), `README.md` đã cập nhật; **KHÔNG có** `.env`, `vendor/`, `logs/*.log`, tệp test tạm (`uploads/test.php`), hay `C:\temp\...`.
- [ ] Đóng gói `LTW_BTN7_Nhom02_code.zip` (**loại trừ `.git/`, `vendor/`, `.env`** — và không kèm các file hướng dẫn/ghi chú nháp nội bộ như `HUONGDAN.md`, `yeucau*.md` nếu nhóm chỉ dùng cục bộ).
- [ ] Nhóm trưởng nộp **1 lần duy nhất** `Nhom02_Baitap7.pdf` + `LTW_BTN7_Nhom02_code.zip` + link GitHub lên `nhhai.net` **trước buổi thực hành tuần 13**.
