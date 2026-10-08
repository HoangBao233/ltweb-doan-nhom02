TRƯỜNG ĐẠI HỌC SƯ PHẠM – ĐẠI HỌC ĐÀ NẴNG
KHOA TOÁN – TIN
Học phần: Thiết kế và Lập trình web (Web Design and Programming) – Học kỳ 1, năm học 2026 – 2027
BÀI TẬP THỰC HÀNH NHÓM SỐ 5
Chương 5 – Lập trình PHP phía máy chủ
Phân tích hoạt động phía máy chủ của một website thực tế và chuyển website đồ án của nhóm thành website động bằng PHP
Hình thức	Làm việc nhóm 4-5 sinh viên — giữ nguyên nhóm của các Bài tập nhóm số 1 – 4. Nộp bài trước buổi thực hành tuần 9.
Sản phẩm nộp	Một báo cáo PDF và mã nguồn kèm link kho GitHub của nhóm; không phải làm slide, không thuyết trình. Giảng viên chấm chủ yếu trên báo cáo PDF, có đối chiếu với kho GitHub và chạy thử website trên máy. Tên file bắt buộc phải bắt đầu với NhomZZ, trong đó ZZ là mã nhóm. Ví dụ: Nhom05_Baitap5.pdf
Chuẩn đầu ra	CLO3 – xây dựng được ứng dụng web động bằng PHP: xử lý biểu mẫu, kiểm tra và lọc dữ liệu, upload tệp, cookie và session, đăng nhập đơn giản (Phần A, B, C). CLO4 – tổ chức mã nguồn PHP theo mô-đun và hướng đối tượng, dùng Git và Composer (Phần B). CLO5 – tổ chức làm việc nhóm hiệu quả, sử dụng Git và công cụ AI có trách nhiệm.
Công cụ	XAMPP (PHP ≥ 8.1) hoặc Laragon, hoặc PHP cài riêng chạy bằng php -S; Composer (getcomposer.org); Visual Studio Code (extension PHP Intelephense); Chrome/Firefox với DevTools (Network – bật Preserve log, xem Headers; Application – Cookies) và Lighthouse; curl (có sẵn trên Windows 10/11 và macOS; trong PowerShell gõ curl.exe); W3C Markup Validator (validator.w3.org/nu, mục Text input); tiện ích Wappalyzer (tùy chọn); Git và tài khoản GitHub.
1. Mục tiêu
Sau khi hoàn thành bài tập, sinh viên có thể:
–	Quan sát và giải thích được hoạt động phía máy chủ của một website thực tế qua HTTP: công nghệ máy chủ, tham số trên URL, biểu mẫu, chuyển hướng, mã trạng thái, cookie và phiên làm việc.
–	Chuyển được một website tĩnh thành website động bằng PHP 8: tách phần dùng chung bằng require, sinh HTML từ dữ liệu, tổ chức mã theo lớp với namespace và autoload của Composer.
–	Xử lý được biểu mẫu an toàn ở phía máy chủ: kiểm tra và lọc dữ liệu, báo lỗi và điền lại, Post/Redirect/Get, nhận ảnh tải lên an toàn; mọi dữ liệu in ra HTML đều qua hàm e().
–	Dùng được cookie và session để ghi nhớ trạng thái (mục đã xem, giỏ hàng, đăng nhập); xử lý ngoại lệ, trang lỗi và ghi log; kiểm thử phía máy chủ có hệ thống và duy trì quy trình làm việc nhóm trên Git/GitHub.
2. Nhiệm vụ
2.1. Phần A – Phân tích hoạt động phía máy chủ của một website thực tế (3,0 điểm)
Nhóm dùng lại website đã phân tích ở các Bài tập nhóm số 2 – 4 (hoặc chọn một website thương mại, tin tức có tìm kiếm, giỏ hàng, đăng nhập và ghi rõ lý do đổi). Dùng DevTools (F12; tab Network bật Preserve log để giữ lại các request khi trang chuyển hướng), "Xem nguồn trang" và lệnh curl, nhóm phân tích và trình bày trong báo cáo. Chỉ quan sát website như một người dùng bình thường (xem mục 5):
–	Công nghệ phía máy chủ: ở tab Network chọn request đầu tiên của trang chủ (loại document), ghi mã trạng thái, phiên bản HTTP và các header Server, X-Powered-By, Content-Type, Cache-Control, Set-Cookie (nếu có); có thể xem nhanh bằng lệnh curl -I <URL>. Kết hợp tên cookie phiên (PHPSESSID, laravel_session, ASP.NET_SessionId, JSESSIONID…), dấu vết trong mã nguồn (đường dẫn wp-content, thẻ meta generator) và tiện ích Wappalyzer để kết luận website dùng ngôn ngữ / nền tảng nào phía máy chủ, nêu rõ căn cứ; nhận xét việc header để lộ tên và phiên bản phần mềm.
–	URL và tham số: ghi ít nhất bốn URL có tham số hoặc đường dẫn động (tìm kiếm, lọc, sắp xếp, phân trang, chi tiết sản phẩm / bài viết); với mỗi URL, giải thích tên và ý nghĩa từng tham số, và nếu là trang PHP thì máy chủ đọc chúng thế nào ($_GET['…']). Với URL "đẹp" không có dấu ? (ví dụ /san-pham/ao-khoac-gio-12), giải thích vì sao máy chủ vẫn biết đang xem mục nào (viết lại URL, định tuyến – routing, Chương 8).
–	Biểu mẫu: phân tích mã HTML của hai biểu mẫu — ô tìm kiếm và một biểu mẫu đăng nhập, đăng ký, liên hệ hoặc đặt hàng: method, action, enctype, tên (name) các ô, ràng buộc phía client, các ô ẩn (hidden) và mục đích có thể có (mã chống giả mạo request như _token, csrf, nonce, form_key…). Thực hiện một lần tìm kiếm và ghi lại request trong tab Network (phương thức, URL đầy đủ, tham số). Biểu mẫu thứ hai chỉ phân tích mã, không gửi.
–	Chuyển hướng và mã trạng thái: ghi ít nhất hai chuyển hướng (ví dụ gõ http:// thay vì https://, bỏ hoặc thêm www., bấm vào tài khoản / giỏ hàng khi chưa đăng nhập) kèm mã 301 / 302 / 303 / 307 / 308 và header Location. Mở một URL không tồn tại và một trang chi tiết với mã không tồn tại: ghi mã trạng thái (404 thật hay "soft 404" trả về 200); nhận xét trang lỗi có thân thiện, có gợi ý đường đi tiếp và có lộ thông tin kỹ thuật (đường dẫn tệp, câu SQL, phiên bản phần mềm) hay không.
–	Cookie và phiên: mở website trong cửa sổ ẩn danh, ghi các header Set-Cookie của response đầu tiên; ở tab Application → Cookies lập bảng các cookie (tên, tên miền, thời hạn, HttpOnly, Secure, SameSite) và phân loại: cookie phiên, ghi nhớ đăng nhập hoặc tùy chọn, giỏ hàng, thống kê – quảng cáo của bên thứ ba. Nếu website có giỏ hàng: thêm một mục vào giỏ, quan sát request gửi đi và thay đổi trong Cookies, Local Storage để suy luận giỏ hàng được lưu ở máy chủ (session, CSDL) hay ở trình duyệt; kiểm chứng bằng cách xóa riêng cookie phiên (hoặc riêng Local Storage) rồi tải lại trang.
–	Ghi mọi số liệu kèm URL và ngày giờ kiểm tra thành bảng; ảnh chụp chỉ để minh hoạ. Chọn hai vấn đề tiêu biểu tìm được (ví dụ: header lộ phiên bản PHP hoặc phần mềm máy chủ, cookie phiên thiếu HttpOnly / Secure, "soft 404", trang lỗi lộ thông tin kỹ thuật, trang chi tiết không báo lỗi khi mã không tồn tại) để giải thích nguyên nhân và đề xuất cách sửa cụ thể bằng kiến thức Chương 5.
2.2. Phần B – Website động bằng PHP cho đồ án (3,5 điểm)
Tiếp tục trên kho ltweb-doan-nhomZZ (bộ trang có CSS responsive và JavaScript từ Bài tập nhóm số 3 – 4), nhóm chuyển website sang PHP: phần dùng chung viết một lần, HTML do máy chủ sinh từ dữ liệu, biểu mẫu được kiểm tra và xử lý ở máy chủ, trạng thái được ghi nhớ bằng cookie và session. Dữ liệu vẫn lấy từ tệp data/<ten-thuc-the>.json của Bài tập nhóm 4 (Chương 6 chuyển sang MySQL). Đề tài không có "sản phẩm", "giỏ hàng" thì thay bằng thực thể và chức năng tương đương của đề tài, yêu cầu kỹ thuật giữ nguyên. Năm đoạn mã gợi ý (đã chạy thử) ở Phụ lục B. Yêu cầu:
–	Chuyển sang PHP: trước khi sửa, gắn thẻ cho commit đã nộp Bài tập nhóm 4 để giữ bản tĩnh (git tag btn4 <mã-commit>, rồi git push origin btn4). Đổi các trang .html thành .php cùng tên bằng git mv và sửa mọi liên kết; header, menu, footer chỉ còn một bản trong inc/ và được require ở mọi trang; mỗi trang gồm phần xử lý ở đầu tệp (không echo) và phần hiển thị bên dưới (HTML với <?= ?> và cú pháp foreach: … endforeach;) — xem gợi ý 1, 2. Website phải chạy được cả bằng php -S localhost:8000 tại thư mục gốc của kho lẫn khi chép vào htdocs của XAMPP (http://localhost/ltweb-doan-nhomZZ/), nên chỉ dùng đường dẫn tương đối. GitHub Pages chỉ phục vụ tệp tĩnh nên từ bài này không dùng được nữa.
–	Tổ chức mã hướng đối tượng: dùng autoload PSR-4 của Composer (App\ → src/); commit composer.json và composer.lock, không commit vendor/. Có ít nhất ba lớp như Bảng 2: lớp thực thể; lớp truy cập dữ liệu — nơi duy nhất đọc tệp JSON, để Chương 6 chỉ phải viết lại lớp này bằng PDO (gợi ý 3); lớp giỏ hàng bọc $_SESSION. Hàm tiện ích nhỏ (e(), vnd()…) để trong inc/ham.php. Khai báo kiểu cho tham số và giá trị trả về; tên lớp PascalCase, mỗi lớp một tệp cùng tên; đầu mỗi tệp có vài dòng chú thích nói tệp làm gì.
–	Sáu chức năng bắt buộc theo Bảng 1; mỗi thành viên phụ trách ít nhất một chức năng và ghi tên vào cột "Người phụ trách". Mọi chức năng ở Bảng 1 phải dùng được khi tắt JavaScript.
–	Kiểm tra dữ liệu và an toàn: mọi dữ liệu đến từ $_GET, $_POST, $_COOKIE, $_FILES đều được kiểm tra lại ở máy chủ (trim, filter_var, mb_strlen, preg_match, danh sách giá trị cho phép) — kiểm tra bằng HTML và JavaScript của các bài trước vẫn giữ nhưng chỉ là lớp thứ nhất; mọi dữ liệu động in ra HTML đều qua e(); POST thành công thì chuyển hướng (Post/Redirect/Get); mật khẩu chỉ lưu dạng băm; ảnh tải lên được kiểm tra kiểu thật bằng finfo, đặt tên ngẫu nhiên, lưu trong uploads/. Thư mục storage/ và logs/ có tệp .htaccess một dòng Require all denied (Apache chặn truy cập qua URL — Chương 7); dữ liệu phát sinh khi chạy (uploads/, storage/, logs/) không commit (gợi ý 1).
–	Lỗi và log: inc/config.php như slide 51 của Chương 5 (hằng MOI_TRUONG, E_ALL, ghi log vào logs/php-error.log, set_exception_handler hiện trang 500.php khi MOI_TRUONG là 'prod'); có trang 404.php và 500.php thân thiện; nộp bài với MOI_TRUONG = 'dev'.
–	JavaScript của Bài tập nhóm 4: giữ những gì còn hợp lý (menu thu gọn, kiểm tra biểu mẫu phía client, yêu thích bằng localStorage, khối REST API ở trang chủ); bỏ phần dựng danh sách, chi tiết bằng fetch và việc gửi biểu mẫu tới jsonplaceholder — nay PHP làm những việc này.
–	Chuẩn chất lượng: không có Warning, Notice, Deprecated hay Fatal error nào trên trang và trong logs/php-error.log khi thao tác mọi chức năng, kể cả với dữ liệu sai của Bảng 3; mã HTML do PHP sinh ra đạt 0 lỗi W3C Markup Validator (validator không truy cập được localhost: mở trang, bấm Ctrl+U, chép mã nguồn vào mục Text input của validator.w3.org/nu) và không cuộn ngang ở 360px; index.php, danh-sach.php và lien-he.php đạt Lighthouse Accessibility ≥ 90 (chế độ Mobile, chạy trên localhost). Kết quả ghi vào Bảng 3, Bảng 4; ảnh chụp lưu trong kiemtra/.
Bảng 1. Sáu chức năng bắt buộc (nhóm chép bảng vào báo cáo, điền hai cột cuối; đổi tên tệp theo tên thực tế của nhóm)
TT	Trang	Chức năng	Kỹ thuật bắt buộc	Tệp PHP	Người phụ trách
1	Mọi trang	Khung trang dùng chung: header (menu đánh dấu trang đang xem, số món trong giỏ, người đăng nhập hoặc nút Đăng nhập), footer; trang 404, 500 thân thiện	inc/config.php nạp đầu tiên; require + __DIR__; biến $tieuDe, $trang, $goc; aria-current; 404.php, 500.php đi kèm đúng mã trạng thái 404, 500 (gợi ý 1, 2)		
2	danh-sach.php	Danh sách do PHP sinh từ tệp JSON; một biểu mẫu GET: tìm theo từ khóa, lọc theo danh mục, sắp xếp theo giá hoặc tên; giữ lại lựa chọn; báo khi không có kết quả	Danh mục, kiểu sắp xếp phải thuộc danh sách cho phép; array_filter, usort, mb_stripos; URL chia sẻ được: ?q=ao&dm=nam&sx=gia-tang		
3	chi-tiet.php
index.php	Chi tiết theo ?id=, id sai thì trang 404; nút "Thêm vào giỏ"; cookie nhớ bốn mục xem gần nhất, trang chủ hiện khối "Đã xem gần đây"	filter_var kiểm tra id là số nguyên; lớp truy cập dữ liệu (gợi ý 3); setcookie trước mọi output, httponly, samesite; đọc cookie phải kiểm tra lại (gợi ý 4)		
4	lien-he.php	Biểu mẫu liên hệ: kiểm tra mọi ô ở máy chủ, báo lỗi dưới từng ô, điền lại dữ liệu; đính kèm một ảnh (không bắt buộc); lưu lại, báo đã gửi	$du, $loi; filter_var, mb_strlen, preg_match; upload ≤ 2 MB, finfo, tên ngẫu nhiên; lưu storage/lien-he.jsonl; PRG, flash message (gợi ý 5)		
5	gio-hang.php	Giỏ hàng (hoặc tương đương của đề tài: đặt tour, đăng ký khóa học…) trong session: thêm, đổi số lượng, xóa một mục, xóa hết; thành tiền, tổng tiền	Lớp GioHang bọc $_SESSION['gio'] = [id => số lượng]; min_range, max_range; id phải có trong dữ liệu; giá lấy từ dữ liệu, không lấy từ form; PRG		
6	dang-nhap.php
dang-xuat.php
quan-tri.php	Đăng nhập đơn giản; trang quản trị (phải đăng nhập) liệt kê các liên hệ đã nhận kèm ảnh, mới nhất trước; đăng xuất	password_hash, password_verify (tài khoản thử trong inc/tai-khoan.php); cấp mã phiên mới khi đăng nhập; báo lỗi chung; inc/bao-ve.php; ghi log khi đăng nhập sai		

Bảng 2. Các lớp PHP của nhóm (ba dòng đầu là gợi ý — đổi tên theo đề tài, thêm lớp nếu có; chép bảng vào báo cáo và điền đầy đủ)
TT	Lớp (tên đầy đủ) và tệp	Thuộc tính, phương thức chính	Dùng ở trang
1	App\Models\SanPham
src/Models/SanPham.php	id, ten, gia, danhMuc… (readonly); tuMang(array $d): self – tạo đối tượng từ một mục của tệp JSON	index, danh-sach, chi-tiet, gio-hang
2	App\Data\KhoSanPham
src/Data/KhoSanPham.php	tatCa(): array; timTheoId(int $id): ?SanPham; timKiem(…): array cho trang danh sách	index, danh-sach, chi-tiet, gio-hang
3	App\Services\GioHang
src/Services/GioHang.php	them(int $id, int $sl); capNhat(…); xoa(int $id); xoaHet(); soMon(): int; tongTien(KhoSanPham $kho): int	gio-hang, header
4			
5			

Bảng 3. Kiểm thử phía máy chủ (chép bảng vào báo cáo; thực hiện đủ 12 ca trên website của nhóm, điền hai cột cuối; có thể thêm ca)
TT	Ca kiểm thử (cách làm)	Kết quả mong đợi	Kết quả thực tế	Đạt?
1	Liên hệ: tắt JavaScript (hoặc thêm novalidate vào thẻ form bằng DevTools), để trống mọi ô rồi gửi	Hiện lại biểu mẫu, báo lỗi dưới từng ô bắt buộc; không lưu gì		
2	Liên hệ: email "lan@ued", số điện thoại "abc", các ô khác hợp lệ	Báo lỗi đúng hai ô đó; các ô khác giữ nguyên giá trị đã nhập		
3	Liên hệ: đính kèm tệp gia-mao.jpg — tệp văn bản chứa <?php phpinfo(); được đổi đuôi thành .jpg	Từ chối, báo chỉ nhận ảnh JPG, PNG, WebP; uploads/ không có tệp mới		
4	Liên hệ: đính kèm một ảnh lớn hơn 2 MB	Báo lỗi kích thước, không có Warning; các ô khác giữ nguyên		
5	Gửi một liên hệ hợp lệ, rồi bấm F5 ở trang hiện ra sau đó	storage/lien-he.jsonl chỉ thêm một dòng; thông báo "đã gửi" hiện đúng một lần		
6	Nhập "><script>alert(1)</script> vào ô tìm kiếm và vào nội dung liên hệ; xem danh-sach.php và quan-tri.php	Chuỗi hiện nguyên văn như chữ (kể cả trong ô tìm kiếm), không có hộp thoại alert		
7	Mở chi-tiet.php?id=abc, chi-tiet.php?id=999999 và chi-tiet.php (không có id)	Trang 404 thân thiện, mã trạng thái 404 (tab Network), không có Warning		
8	Thêm vào giỏ với số lượng 0, -3, 1000 và với id không tồn tại (sửa ô ẩn, ô số lượng bằng DevTools hoặc dùng curl)	Không thêm vào giỏ, báo lỗi; giỏ hàng giữ nguyên		
9	Thêm hai mục vào giỏ, đổi số lượng, xóa một mục, tải lại trang, chuyển sang trang khác	Thành tiền, tổng tiền và số món trên header luôn đúng		
10	Mở quan-tri.php khi chưa đăng nhập; đăng nhập sai; đăng nhập đúng, đăng xuất rồi mở lại quan-tri.php	Chuyển về dang-nhap.php; báo lỗi chung "Sai tên đăng nhập hoặc mật khẩu"; đăng xuất xong không vào lại được		
11	Sửa giá trị cookie da_xem thành abc,-1,999999 (tab Application → Cookies) rồi mở trang chủ	Trang chủ hiện bình thường, bỏ qua giá trị lạ, không có Warning		
12	Đặt MOI_TRUONG = 'prod', tạm đổi tên tệp data/*.json rồi mở danh-sach.php (thử xong trả lại như cũ)	Trang 500 thân thiện, không lộ đường dẫn hay chi tiết lỗi; chi tiết lỗi có trong logs/php-error.log		

Bảng 4. Chất lượng từng trang (Lỗi PHP: số Warning / Notice / Deprecated trên trang và trong log khi tải và thao tác; Lỗi HTML: số lỗi W3C của mã HTML do PHP sinh ra; A11y: điểm Lighthouse Accessibility ở chế độ Mobile; chép vào báo cáo và điền đầy đủ)
TT	Trang	Lỗi PHP	Lỗi HTML	A11y	Dùng được khi tắt JavaScript? (có / không)	Ghi chú
1	index.php					
2	danh-sach.php					
3	chi-tiet.php?id=1					
4	gio-hang.php					
5	lien-he.php					
6	dang-nhap.php					
7	quan-tri.php					
8	gioi-thieu.php					

2.3. Phần C – Chức năng PHP trên trang cá nhân của từng thành viên (1,5 điểm)
Mỗi thành viên tự thực hiện trên trang thanhvien/<MSSV>_<ten>/ của mình:
1.	Đổi gioithieu.html thành gioithieu.php dùng chung header, footer của nhóm (require qua __DIR__, gán $goc = '../../' như gợi ý 2); giữ CSS và js/canhan.js riêng của mình; trang hiển thị đúng khi mở qua http://localhost:8000/thanhvien/<MSSV>_<ten>/gioithieu.php.
2.	Thêm ít nhất HAI chức năng xử lý ở máy chủ, không trùng với bạn cùng nhóm. Gợi ý: sổ lưu bút (biểu mẫu tên + lời nhắn, kiểm tra ở máy chủ, lưu vào storage/<MSSV>_luubut.jsonl, hiện năm lời nhắn mới nhất); bộ đếm lượt xem, mỗi phiên chỉ tính một lần (tệp trong storage/ + session); danh sách kỹ năng, dự án từ mảng PHP, lọc theo nhóm bằng tham số GET; giao diện sáng / tối ghi nhớ bằng cookie; máy tính điểm học phần theo công thức 0,2 × A1 + 0,3 × A2 + 0,5 × A3, kiểm tra điểm nhập từ 0 đến 10; ảnh đại diện tải lên an toàn — hoặc chức năng khác có độ khó tương đương.
3.	Mọi dữ liệu in ra đều qua e(); biểu mẫu được kiểm tra ở máy chủ, gửi POST thành công thì chuyển hướng (PRG); không có Warning / Notice; mã HTML sinh ra 0 lỗi W3C; đầu mỗi tệp có 3 – 5 dòng chú thích nói tệp làm gì và cách thử. Ghi kết quả vào Bảng 5 kèm ảnh chụp (lưu trong kiemtra/).
4.	git add → commit → push từ tài khoản GitHub của mình, thông điệp commit rõ nghĩa (ví dụ: "Them so luu but cho trang ca nhan cua An") — lịch sử commit là minh chứng đóng góp cá nhân, cho cả Phần C và chức năng mình phụ trách ở Phần B.
Bảng 5. Kết quả Phần C của từng thành viên (mỗi người một dòng; chép vào báo cáo và điền đầy đủ)
TT	Họ và tên	Hai chức năng PHP đã làm (Phần C)	Chức năng phụ trách ở Bảng 1	Lỗi PHP	Lỗi HTML	Số commit
1						
2						
3						
4						
5						

2.4. Phần D – Câu hỏi thảo luận (1,0 điểm)
Nhóm thảo luận và trả lời ngắn gọn (mỗi câu 5 – 10 dòng) trong báo cáo; câu nào có thử nghiệm thì chép lệnh và phần kết quả đáng chú ý:
1.	Máy chủ không tin trình duyệt. Ở câu D3 của Bài tập nhóm 4, nhóm đã liệt kê những gì máy chủ PHP phải kiểm tra lại. Nay dùng curl gửi thẳng request tới website của nhóm, vượt qua mọi kiểm tra phía client, ví dụ: curl -i -d "hoten=A&email=abc" http://localhost:8000/lien-he.php, và một request thêm vào giỏ với số lượng âm (giỏ hàng dùng session nên thêm -c cookie.txt -b cookie.txt để curl giữ cookie phiên). Máy chủ phản hồi thế nào, dòng mã PHP nào đã chặn từng trường hợp? Danh sách ở câu D3 đã được làm đủ chưa?
2.	Chỉ ra ba chỗ trong mã của nhóm in dữ liệu người dùng ra HTML và giải thích vì sao phải qua e() (htmlspecialchars với ENT_QUOTES). Tạm bỏ e() ở thuộc tính value của ô tìm kiếm rồi tìm với chuỗi "><script>alert(1)</script>: điều gì xảy ra, vì sao? Nếu trang quản trị in nội dung liên hệ mà không qua e() thì ai bị hại và hậu quả là gì? (Thử xong khôi phục mã, không commit bản lỗi.)
3.	Post/Redirect/Get và thứ tự output. (a) Tạm bỏ dòng header('Location: …') sau khi lưu liên hệ, gửi biểu mẫu rồi bấm F5: trình duyệt hỏi gì, storage/lien-he.jsonl có thêm mấy dòng? Khôi phục rồi làm lại, mô tả chuỗi request trong tab Network (mã 302, request GET tiếp theo). (b) Thêm một dòng trống trước thẻ <?php của chi-tiet.php rồi chạy website bằng php -d output_buffering=0 -S localhost:8000 (tắt bộ đệm đầu ra — php.ini mặc định đặt output_buffering = 4096 nên lỗi thường bị che): thông báo gì xuất hiện, cookie da_xem còn được ghi không, vì sao? Vì sao mã chạy được trên máy mình vẫn có thể hỏng trên máy chủ khác?
4.	Trạng thái của website: lập bảng mọi dữ liệu website của nhóm ghi nhớ giữa các request (giỏ hàng, người đăng nhập, flash message, mục đã xem gần đây, yêu thích của Bài tập 4, liên hệ, ảnh tải lên…): lưu ở đâu (session, cookie, localStorage, tệp trên máy chủ), ai đọc và sửa được, mất đi khi nào. Giỏ hàng ra sao khi người dùng mở website bằng trình duyệt khác, hoặc xóa cookie PHPSESSID? Khi chuyển sang MySQL ở Chương 6, dữ liệu nào nên đưa vào CSDL, và nhờ lớp truy cập dữ liệu, nhóm sẽ phải sửa những tệp nào?
3. Sản phẩm nộp và quy cách
–	Báo cáo: một tệp PDF (không giới hạn số trang), đặt tên NhomZZ_Baitap5.pdf; xuất từ Word / Google Docs (không nộp ảnh quét). Cấu trúc bắt buộc, dùng đúng tên mục: Trang bìa (tên nhóm, thành viên, mã sinh viên, link kho GitHub, mã commit cuối); Phần A (bảng số liệu, phân tích, ảnh minh hoạ); Phần B (Bảng 1 đã điền, Bảng 2, mô tả ngắn cách hoạt động của từng chức năng kèm tên tệp PHP, ảnh chụp biểu mẫu báo lỗi, gửi thành công, giỏ hàng, trang 404, trang quản trị; Bảng 3, Bảng 4); Phần C (Bảng 5); Phần D; Tài liệu tham khảo; Phụ lục – Bảng phân công. Mọi số liệu phải ghi thành bảng hoặc câu văn; ảnh chụp chỉ để minh hoạ, phải đọc được chữ và có chú thích (Hình 1, Hình 2…).
–	Mã nguồn: link kho GitHub ltweb-doan-nhomZZ (công khai, đủ lịch sử commit của từng thành viên, có thẻ btn4) và mã commit cuối cùng ghi rõ trong báo cáo; các commit sau hạn nộp không được tính. Thư mục gốc của kho có README.md gồm: yêu cầu môi trường (PHP ≥ 8.1, Composer), các lệnh cài đặt và chạy (composer install, php -S localhost:8000), tài khoản thử của trang quản trị, bảng "Chức năng – URL – Tệp PHP". Đồng thời nén toàn bộ kho (trừ thư mục .git và vendor/) thành LTW_BTN5_NhomZZ_code.zip.
–	Nơi nộp: mục "Bài tập nhóm số 5" trên e-Learning (nhhai.net); nhóm trưởng nộp một lần cho cả nhóm, trước buổi thực hành tuần 9.
–	Cách chấm: giảng viên chấm trên báo cáo PDF theo rubric ở mục 4, đối chiếu với kho GitHub (mã PHP, lịch sử commit của từng người); tải kho về đúng mã commit đã ghi, chạy composer install và php -S theo README (PHP 8, báo mọi lỗi E_ALL), thử từng chức năng ở Bảng 1 và lặp lại các ca kiểm thử của Bảng 3 bằng trình duyệt và curl, tắt JavaScript để kiểm chứng, đọc logs/php-error.log. Chức năng không chạy được theo hướng dẫn trong README thì không được tính điểm.
4. Tiêu chí đánh giá (thuộc bài đánh giá A1.2 – Rubric R1.2)
Tiêu chí	Điểm	Tốt (80 – 100%)	Đạt (50 – 79%)	Chưa đạt (< 50%)
A. Phân tích hoạt động phía máy chủ của website thực tế	3,0	Số liệu về header, tham số URL, biểu mẫu, chuyển hướng, mã trạng thái và cookie đầy đủ, ghi thành bảng; nhận diện đúng công nghệ máy chủ kèm căn cứ; suy luận về phiên, giỏ hàng xác đáng; hai đề xuất sửa khả thi.	Đúng phần lớn nội dung; phần chuyển hướng, cookie hoặc đề xuất sửa còn sơ sài.	Chỉ chụp màn hình, không phân tích; nhận diện sai công nghệ hoặc mã trạng thái.
B. Website động bằng PHP của đồ án	3,5	Đủ sáu chức năng của Bảng 1, chạy đúng theo README; phần dùng chung viết một lần, mã chia inc/, src/ rõ ràng, có lớp và autoload Composer; kiểm tra dữ liệu ở máy chủ, e(), PRG, upload an toàn; đạt đủ các ca của Bảng 3; không Warning, HTML 0 lỗi; dùng được khi tắt JavaScript.	Chạy được phần lớn chức năng; thiếu một – hai chức năng hoặc vài ca kiểm thử chưa đạt; còn Warning nhỏ.	Thiếu từ ba chức năng trở lên; header, footer vẫn chép ở từng trang; không kiểm tra dữ liệu ở máy chủ hoặc in dữ liệu người dùng không qua e(); không chạy được theo README.
C. Chức năng PHP trên trang cá nhân của từng thành viên	1,5	Mọi thành viên có trang cá nhân dùng chung header, footer và hai chức năng phía máy chủ chạy đúng, an toàn (Bảng 5 đầy đủ); mỗi người phụ trách ít nhất một chức năng Phần B; commit từ tài khoản của từng người.	Đa số thành viên đạt; một người thiếu chức năng, còn lỗi hoặc thiếu commit.	Thiếu phần của từ hai thành viên trở lên, hoặc không có commit cá nhân.
D. Câu hỏi thảo luận	1,0	Trả lời đúng, có lập luận và dẫn chứng từ chính mã nguồn và thử nghiệm của nhóm (lệnh curl, kết quả) cho cả bốn câu.	Trả lời đúng hướng nhưng còn chung chung hoặc thiếu một câu.	Trả lời sai hoặc bỏ trống từ hai câu trở lên.
E. Hình thức báo cáo và tài liệu tham khảo	1,0	Trình bày rõ ràng, đúng quy cách mục 3 (đúng cấu trúc, số liệu có bảng, ảnh rõ và có chú thích); trích dẫn ít nhất ba tài liệu tin cậy.	Đúng quy cách; trích dẫn còn thiếu hoặc chưa đúng chuẩn.	Sai quy cách, không có tài liệu tham khảo.

5. Quy định
–	Báo cáo hoặc mã nguồn sao chép giữa các nhóm, hoặc sao chép từ nguồn khác mà không trích dẫn, nhận 0 điểm cho tất cả các nhóm liên quan. Chép nguyên đoạn mã PHP trên mạng vào Phần B mà không ghi nguồn cũng bị tính là sao chép; mã gợi ý trong đề bài được dùng lại tự do.
–	Phần B và Phần C viết bằng PHP thuần: không dùng framework (Laravel, Symfony, CodeIgniter…) hay CMS (WordPress…); Composer chỉ dùng cho autoload và thư viện phụ trợ nếu có (ví dụ monolog để ghi log); mọi chức năng ở Bảng 1 phải do nhóm tự viết. Vẫn dùng Bootstrap và JavaScript của các bài trước như cũ.
–	Phần A chỉ quan sát website như một người dùng bình thường: không dò quét, không thử tấn công, không gửi dữ liệu giả vào biểu mẫu đăng ký, liên hệ, đặt hàng của website thật; vi phạm thì Phần A nhận 0 điểm.
–	Không đưa mật khẩu thật hay dữ liệu cá nhân thật vào kho; tài khoản thử chỉ dùng cho website của nhóm và ghi trong README.
–	Được phép dùng công cụ AI để hỗ trợ tra cứu, sửa lỗi, nhưng phải ghi rõ trong báo cáo đã dùng ở đâu; mọi thành viên phải hiểu và giải thích được nội dung nộp khi giảng viên hỏi ngẫu nhiên tại buổi thực hành.
–	Thành viên không tham gia (theo bảng phân công có xác nhận của nhóm) nhận 0 điểm bài tập này.
–	Sau khi chấm, giảng viên có thể yêu cầu nhóm demo trực tiếp sản phẩm và trả lời câu hỏi về phần việc của từng thành viên; điểm có thể được điều chỉnh tăng hoặc giảm theo kết quả demo, cho cả nhóm hoặc riêng từng thành viên.
6. Tài liệu tham khảo và gợi ý
–	Slide bài giảng Chương 5 – Lập trình PHP phía máy chủ (5.1 cơ chế request, php.ini, gỡ lỗi; 5.2 cú pháp PHP 8; 5.3 biểu mẫu, filter_var, Post/Redirect/Get, upload; 5.4 require, lập trình hướng đối tượng, namespace, Composer; 5.5 cookie, session, ngoại lệ, ghi log).
–	J. Duckett – PHP & MySQL: Server-side Web Development, Wiley, 2022: Phần A (ch. 1–4), Phần B (ch. 5–7, 9–10), ch. 15.
–	J. Murach, R. Harris – Murach’s PHP and MySQL, 4th ed., Mike Murach & Associates, 2022: các chương về biểu mẫu, chuỗi, mảng, cookie, session, lập trình hướng đối tượng.
–	R. Nixon – Learning PHP, MySQL & JavaScript, 6th ed., O’Reilly, 2021: các chương về PHP, cookie và session.
–	PHP Manual – php.net/manual: Handling file uploads, Sessions, setcookie, filter_var, password_hash, Classes and Objects, Namespaces; PHP: The Right Way – phptherightway.com.
–	Composer – getcomposer.org/doc: Basic usage, Autoloading (PSR-4).
–	MDN Web Docs – HTTP cookies; Redirections in HTTP; HTTP response status codes – developer.mozilla.org. OWASP Cheat Sheet Series – File Upload, Session Management – cheatsheetseries.owasp.org.
–	wa4e.com – Web Applications for Everybody (ĐH Michigan): PHP, biểu mẫu, session – học miễn phí; curl – curl.se/docs/manpage.html.
Phụ lục A – Mẫu bảng phân công công việc và tự đánh giá
TT	Họ và tên	Mã sinh viên	Tài khoản GitHub	Công việc đảm nhận	Đóng góp (%)
1				Nhóm trưởng; …	
2					
3					
4					
5					

Phụ lục B – Mã gợi ý
Các đoạn mã dưới đây đã được chạy thử, dùng được với PHP 8.1 trở lên, và dùng đúng các tên trong đề bài (tên tệp, tên lớp, tên biến). Nhóm được dùng lại tự do, nhớ đổi tên cho khớp với đề tài và dữ liệu của mình; chỗ ghi … là phần nhóm tự viết.
Gợi ý 1. Cấu trúc kho ltweb-doan-nhomZZ sau Bài tập nhóm số 5; .gitignore; composer.json; đầu tệp inc/config.php
ltweb-doan-nhomZZ/
├── index.php  danh-sach.php  chi-tiet.php  gioi-thieu.php
├── lien-he.php  gio-hang.php  dang-nhap.php  dang-xuat.php
├── quan-tri.php  404.php  500.php
├── inc/
│   ├── config.php     nạp đầu tiên: autoload, lỗi, log, session
│   ├── ham.php        e(), vnd()… — hàm tiện ích dùng chung
│   ├── header.php  footer.php
│   ├── bao-ve.php     trang cần đăng nhập require tệp này
│   └── tai-khoan.php  tài khoản thử, mật khẩu đã băm
├── src/               các lớp PHP, namespace App\ (PSR-4)
│   ├── Models/SanPham.php
│   ├── Data/KhoSanPham.php   Data/KhoLienHe.php
│   └── Services/GioHang.php
├── data/san-pham.json     từ Bài tập 4 (đổi tên theo đề tài)
├── storage/  logs/        ghi khi chạy; mỗi thư mục có tệp .htaccess
├── uploads/               ảnh tải lên, tên ngẫu nhiên
├── css/  js/  images/  kiemtra/  thanhvien/    (từ các bài trước)
├── vendor/                Composer tạo ra — không commit
├── composer.json  composer.lock  .gitignore
└── README.md              cách chạy, tài khoản thử, chức năng – URL
 
Tệp .gitignore — không commit thư viện và dữ liệu phát sinh khi chạy:
/vendor/
/logs/*
!/logs/.htaccess
/storage/*
!/storage/.htaccess
/uploads/*
!/uploads/.gitkeep
 
Tệp composer.json (sửa xong chạy lệnh: composer dump-autoload):
{
    "name": "nhomzz/ltweb-doan",
    "require": { "php": ">=8.1" },
    "autoload": { "psr-4": { "App\\": "src/" } }
}
 
<?php
// inc/config.php: như slide 51 (Chương 5), thêm hai dòng require ở đầu
require_once __DIR__ . '/../vendor/autoload.php';   // mọi lớp App\…
require_once __DIR__ . '/ham.php';                  // e(), vnd()
 
const MOI_TRUONG = 'dev';                  // máy thật: 'prod'
// … error_reporting, ini_set, session_start(), set_exception_handler …

Gợi ý 2. Header dùng chung cho trang ở thư mục gốc lẫn trang cá nhân trong thanhvien/
<?php
// inc/header.php — trang gọi gán trước $tieuDe, $trang (đánh dấu menu);
// trang ở thư mục con gán thêm $goc = '../../' (đường dẫn về gốc)
$goc   ??= '';
$trang ??= '';
// use chỉ có hiệu lực trong từng tệp nên ở đây viết tên lớp đầy đủ
$gio   = new App\Services\GioHang();
$menu  = ['index' => 'Trang chủ', 'danh-sach' => 'Sản phẩm',
          'gioi-thieu' => 'Giới thiệu', 'lien-he' => 'Liên hệ'];
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($tieuDe) ?> | Tên website của nhóm</title>
  <link rel="stylesheet" href="<?= $goc ?>css/style.css">
</head>
<body>
<header class="dau-trang">
  <nav aria-label="Menu chính">
    <ul class="menu">
    <?php foreach ($menu as $tep => $nhan): ?>
      <li><a href="<?= $goc . $tep ?>.php"
             <?= $tep === $trang ? 'aria-current="page"' : '' ?>>
          <?= e($nhan) ?></a></li>
    <?php endforeach; ?>
    </ul>
  </nav>
  <a href="<?= $goc ?>gio-hang.php">Giỏ hàng (<?= $gio->soMon() ?>)</a>
  <?php if (isset($_SESSION['user'])): ?>
    <span><?= e($_SESSION['user']) ?></span>
    <a href="<?= $goc ?>dang-xuat.php">Đăng xuất</a>
  <?php else: ?>
    <a href="<?= $goc ?>dang-nhap.php">Đăng nhập</a>
  <?php endif; ?>
</header>
Trang gọi header: gán biến, require header ở cuối phần xử lý, require footer ở cuối tệp. Trang cá nhân nằm sâu hai cấp nên gán thêm $goc:
<?php
// index.php — mọi trang ở thư mục gốc theo cùng một khung
require __DIR__ . '/inc/config.php';
use App\Data\KhoSanPham;
 
$kho    = new KhoSanPham(__DIR__ . '/data/san-pham.json');
$moiVe  = array_slice($kho->tatCa(), 0, 4);   // phần xử lý: không echo
$tieuDe = 'Trang chủ';
$trang  = 'index';                            // header đánh dấu menu
require __DIR__ . '/inc/header.php';
?>
<main>
  <h1>Sản phẩm mới</h1>
  <?php foreach ($moiVe as $sp): ?>
    <h2><a href="chi-tiet.php?id=<?= $sp->id ?>">
      <?= e($sp->ten) ?></a></h2>
  <?php endforeach; ?>
</main>
<?php require __DIR__ . '/inc/footer.php'; ?>
 
<?php
// thanhvien/<MSSV>_<ten>/gioithieu.php — nằm sâu hai cấp thư mục
require __DIR__ . '/../../inc/config.php';
$goc    = '../../';                 // đường dẫn từ trang này về gốc
$tieuDe = 'Nguyễn Văn An';
require __DIR__ . '/../../inc/header.php';
?>

Gợi ý 3. Lớp truy cập dữ liệu — mọi trang lấy dữ liệu qua lớp này, không tự đọc tệp JSON
<?php
// src/Data/KhoSanPham.php — nơi DUY NHẤT đọc tệp JSON (chương 6: PDO)
namespace App\Data;
 
use App\Models\SanPham;
use RuntimeException;   // lớp có sẵn: use (hoặc viết \RuntimeException)
 
class KhoSanPham
{
    private ?array $ds = null;      // mỗi request chỉ đọc tệp một lần
 
    public function __construct(private string $tepJson) {}
 
    public function tatCa(): array      // mảng các đối tượng SanPham
    {
        if ($this->ds === null) {
            if (!is_file($this->tepJson)) {
                throw new RuntimeException(
                    "Không có tệp dữ liệu {$this->tepJson}");
            }
            $json = file_get_contents($this->tepJson);
            $mang = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
            $this->ds = array_map(fn($d) => SanPham::tuMang($d), $mang);
        }
        return $this->ds;
    }
 
    public function timTheoId(int $id): ?SanPham
    {
        foreach ($this->tatCa() as $sp) {
            if ($sp->id === $id) return $sp;
        }
        return null;
    }
}
SanPham::tuMang($d) là phương thức tĩnh của lớp thực thể, nhóm tự viết (xem slide 38 – 39): nhận một mục của tệp JSON, trả về đối tượng SanPham với các thuộc tính readonly.

Gợi ý 4. Trang chi tiết: trả 404 khi id sai; ghi và đọc cookie "đã xem gần đây"
<?php
// chi-tiet.php — mở bằng chi-tiet.php?id=12
require __DIR__ . '/inc/config.php';
use App\Data\KhoSanPham;
 
$kho = new KhoSanPham(__DIR__ . '/data/san-pham.json');
$id  = filter_var($_GET['id'] ?? '', FILTER_VALIDATE_INT);
$sp  = $id ? $kho->timTheoId($id) : null;
if ($sp === null) {                  // id thiếu, sai kiểu hoặc không có
    http_response_code(404);
    require __DIR__ . '/404.php';    // dùng chung header, footer
    exit;
}
// Đã xem gần đây: tối đa 4 id, mới nhất đứng đầu. setcookie() gửi
// header nên phải gọi TRƯỚC mọi output, giống header().
$cu  = array_map('intval', explode(',', $_COOKIE['da_xem'] ?? ''));
$moi = array_unique([$sp->id, ...array_filter($cu)]);
setcookie('da_xem', implode(',', array_slice($moi, 0, 4)), [
    'expires'  => time() + 30 * 24 * 3600,      // 30 ngày
    'path'     => '/',
    'httponly' => true,
    'samesite' => 'Lax',
]);
$tieuDe = $sp->ten;
$trang  = 'danh-sach';
require __DIR__ . '/inc/header.php';
?>
<!-- … in $sp qua e(); form "Thêm vào giỏ" (POST tới gio-hang.php) -->
 
// index.php — phần xử lý, sau khi đã có $kho.
// Đọc lại cookie: KHÔNG tin giá trị, kiểm tra từng id
$daXem = [];
foreach (explode(',', $_COOKIE['da_xem'] ?? '') as $x) {
    $id = filter_var($x, FILTER_VALIDATE_INT);
    $sp = $id ? $kho->timTheoId($id) : null;
    if ($sp !== null) $daXem[] = $sp;          // bỏ qua giá trị lạ
}

Gợi ý 5. Lưu liên hệ vào tệp JSON Lines trong storage/, rồi chuyển hướng (Post/Redirect/Get)
<?php
// src/Data/KhoLienHe.php — tệp .jsonl: mỗi dòng là một liên hệ (JSON)
namespace App\Data;
 
class KhoLienHe           // chương 6: thay bằng bảng lien_he của MySQL
{
    public function __construct(private string $tep) {}
 
    public function them(array $lh): void
    {
        $dong = json_encode($lh,
                    JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
        file_put_contents($this->tep, $dong . PHP_EOL,
                          FILE_APPEND | LOCK_EX);    // khóa tệp khi ghi
    }
 
    public function tatCa(): array                  // mới nhất đứng đầu
    {
        if (!is_file($this->tep)) return [];
        $dong = file($this->tep,
                     FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $ds = array_map(fn($d) => json_decode($d, true), $dong);
        return array_reverse(array_filter($ds, 'is_array'));
    }
}
 
<?php
// lien-he.php — phần xử lý (kiểm tra từng ô, upload như slide 29 – 33)
require __DIR__ . '/inc/config.php';
use App\Data\KhoLienHe;
 
$du     = ['hoten' => '', 'email' => '', 'sdt' => '', 'noidung' => ''];
$loi    = [];
$tenAnh = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // … trim, kiểm tra từng ô vào $loi; ảnh hợp lệ và các ô khác không
    //   lỗi thì move_uploaded_file vào uploads/, gán $tenAnh (slide 33)
    if (!$loi) {
        $kho = new KhoLienHe(__DIR__ . '/storage/lien-he.jsonl');
        $kho->them([
            'thoiGian' => date('Y-m-d H:i:s'),
            'hoTen'    => $du['hoten'],
            'email'    => $du['email'],
            'noiDung'  => $du['noidung'],
            'anh'      => $tenAnh,   // tên tệp trong uploads/ hoặc null
        ]);
        $_SESSION['flash'] = 'Đã gửi liên hệ, cảm ơn bạn!';
        header('Location: lien-he.php');     // PRG: F5 không gửi lại
        exit;
    }
}
$tb = $_SESSION['flash'] ?? '';              // flash: hiện đúng một lần
unset($_SESSION['flash']);
// … $tieuDe, $trang, require header; form enctype="multipart/form-data"

