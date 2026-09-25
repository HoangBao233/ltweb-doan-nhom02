TRƯỜNG ĐẠI HỌC SƯ PHẠM – ĐẠI HỌC ĐÀ NẴNG
KHOA TOÁN – TIN
Học phần: Thiết kế và Lập trình web (Web Design and Programming) – Học kỳ 1, năm học 2026 – 2027
BÀI TẬP THỰC HÀNH NHÓM SỐ 3
Chương 3 – CSS3 và thiết kế responsive
Phân tích CSS của một website thực tế và khoác giao diện responsive cho website đồ án của nhóm
Hình thức	Làm việc nhóm 4-5 sinh viên — giữ nguyên nhóm của Bài tập nhóm số 1 và số 2. Nộp bài trước buổi thực hành tuần 5;
Sản phẩm nộp	Một báo cáo PDF và mã nguồn kèm link kho GitHub của nhóm; không phải làm slide, không thuyết trình. Giảng viên chấm chủ yếu trên báo cáo PDF, có đối chiếu với kho GitHub. Tên file bắt buộc phải bắt đầu với NhomZZ, trong đó ZZ là mã nhóm. Ví dụ: Nhom05_Baitap3.pdf
Chuẩn đầu ra	CLO2 – thiết kế và xây dựng được giao diện web chuẩn, responsive bằng CSS3 và Bootstrap 5 (Phần A, B, C). CLO5 – tổ chức làm việc nhóm hiệu quả, sử dụng Git và công cụ AI có trách nhiệm.
Công cụ	Visual Studio Code (extension Live Server); Chrome/Firefox với DevTools (device toolbar, tab Elements → Styles / Computed, overlay Grid và Flexbox, tab Network) và Lighthouse; W3C CSS Validator (jigsaw.w3.org/css-validator); Bootstrap 5 (nhúng qua CDN); Git và tài khoản GitHub (GitHub Pages).
1. Mục tiêu
Sau khi hoàn thành bài tập, sinh viên có thể:
–	Đọc và phân tích được phần trình bày của một website thực tế: hệ màu, typography, kỹ thuật dàn trang (Flexbox, Grid, float), các điểm ngắt responsive và cách xử lý hình ảnh; đo chất lượng trang bằng Lighthouse ở cả chế độ di động và máy tính.
–	Viết được tệp CSS có tổ chức cho một website nhiều trang: biến CSS, chuẩn hóa trình duyệt, bố cục bằng CSS Grid và Flexbox, đặt tên lớp theo quy ước thống nhất.
–	Làm cho website đồ án hiển thị tốt trên mọi bề rộng màn hình theo lối mobile-first: media query, hình ảnh co giãn, menu dùng được trên điện thoại; giữ chuẩn khả năng truy cập (a11y) đã đạt ở bài trước.
–	Dùng được Bootstrap 5 cho một trang và so sánh có căn cứ với cách tự viết CSS; duy trì quy trình làm việc nhóm trên Git/GitHub với đóng góp cá nhân rõ ràng.
2. Nhiệm vụ
2.1. Phần A – Phân tích CSS và tính responsive của một website thực tế (3,0 điểm)
Nhóm dùng lại website đã phân tích ở Bài tập nhóm số 2 (hoặc chọn website khác trong danh sách của bài đó). Dùng DevTools (F12), "Xem nguồn trang" và các công cụ kiểm chuẩn, nhóm phân tích và trình bày trong báo cáo:
–	Hệ màu và chữ: dùng tab Elements → Styles / Computed để lấy màu nền, màu chữ chính, màu nhấn (ghi mã hex), phông chữ, cỡ chữ và giãn dòng của đoạn văn thường và của h1; cho biết website có dùng biến CSS (--ten-bien) hay không và nêu một ví dụ tìm được.
–	Kỹ thuật dàn trang: bật overlay Grid / Flex trong tab Elements, chỉ ra ba khối chính của trang chủ (ví dụ thanh điều hướng, khu vực nổi bật, danh sách thẻ) được dàn bằng Flexbox, CSS Grid hay float; vẽ lại sơ đồ khối trang chủ và ghi rõ kỹ thuật của từng khối.
–	Điểm ngắt responsive: thu hẹp dần cửa sổ (hoặc dùng device toolbar) và ghi lại các bề rộng mà bố cục thay đổi; đối chiếu với các điểm ngắt thông dụng 576 / 768 / 992 / 1200px; mô tả thanh điều hướng biến đổi thế nào khi xuống màn hình điện thoại.
–	Hình ảnh: chọn hai ảnh trên trang chủ, dùng tab Network hoặc các thuộc tính srcset / sizes / loading để biết trình duyệt tải tệp nào ở khung 400px và ở màn hình rộng; ghi kích thước tệp (KB) và định dạng (JPG, PNG, WebP, AVIF, SVG).
–	Kiểm chuẩn và hiệu năng: chạy Lighthouse cho trang chủ ở CẢ hai chế độ Mobile và Desktop, ghi bốn điểm số (Performance, Accessibility, Best Practices, SEO); chạy tệp CSS chính qua W3C CSS Validator và thống kê số lỗi / cảnh báo. Ghi mọi số liệu kèm URL và ngày giờ kiểm tra thành bảng; ảnh chụp chỉ để minh hoạ.
–	Chọn hai vấn đề tiêu biểu tìm được (ví dụ: ảnh quá nặng, tương phản chữ – nền thấp, chữ quá nhỏ trên di động, thiếu điểm ngắt, CSS thừa) để giải thích nguyên nhân và đề xuất cách sửa cụ thể bằng kiến thức Chương 3.
2.2. Phần B – Giao diện responsive cho website đồ án (3,5 điểm)
Từ bộ khung HTML đã dựng ở Bài tập nhóm số 2, nhóm viết CSS để website đồ án có giao diện hoàn chỉnh và hiển thị tốt trên mọi màn hình. Làm việc trên chính kho ltweb-doan-nhomZZ; HTML chỉ được sửa ở mức thêm thuộc tính class, không phá cấu trúc ngữ nghĩa đã có. Sau bài này, bộ trang tĩnh chính là nguyên mẫu giao diện của đồ án (A1.2). Yêu cầu:
–	Tổ chức CSS: toàn bộ kiểu dáng nằm trong thư mục css/ (xem gợi ý bên dưới) và được nạp bằng thẻ <link> — không dùng thuộc tính style trên thẻ HTML. Khai báo bảng màu, cỡ chữ, bo góc bằng biến CSS trong :root; có phần chuẩn hóa box-sizing: border-box, lề của body và ảnh.
–	Đặt tên lớp theo một quy ước thống nhất cho cả nhóm (khuyến khích BEM: .the, .the__tieu-de, .the--noi-bat); tên lớp nói vai trò chứ không nói hình thức; không dùng !important (nếu buộc phải dùng, giải thích lý do trong báo cáo).
–	Bố cục: khung trang (header, nav, main, aside nếu có, footer) dàn bằng CSS Grid; các cụm bên trong (menu, hàng thẻ, nhóm nút, chân trang) dàn bằng Flexbox. Yêu cầu riêng của từng trang xem Bảng 1.
–	Responsive theo lối mobile-first: mặc định viết cho màn hình nhỏ rồi mở rộng bằng ít nhất hai điểm ngắt min-width; ở bề rộng 360px không được xuất hiện thanh cuộn ngang; thanh điều hướng phải dùng được trên điện thoại (xuống dòng, xếp dọc hoặc nút mở menu).
–	Hình ảnh: quy tắc img { max-width: 100%; height: auto; }; ảnh nội dung ghi sẵn width và height; ảnh nằm dưới màn hình đầu dùng loading="lazy"; dùng object-fit khi ảnh nằm trong khung cố định; mỗi tệp ảnh không quá 300 KB.
–	Trạng thái và khả năng truy cập: mọi liên kết và nút có đủ :hover, :active và :focus-visible — giữ viền focus, không viết outline: none; tương phản chữ – nền ≥ 4,5:1; transition không quá 0,3 giây.
–	Một trang dựng thêm bằng Bootstrap 5: chọn một trang (gợi ý danh-sach.html), tạo bản song song đặt tên <ten-trang>-bootstrap.html dùng lưới container – row – col-*, ít nhất hai thành phần dựng sẵn (navbar, card, table, form, alert, pagination…) và các utility classes; tùy biến tối thiểu màu chính bằng biến --bs-* trong tệp CSS riêng nạp sau Bootstrap. Kết quả so sánh ghi vào Bảng 3.
–	Chuẩn chất lượng: mọi tệp CSS 0 lỗi W3C CSS Validator; các trang giữ 0 lỗi HTML Validator như bài trước; trang chủ đạt Lighthouse (chế độ Mobile) Accessibility ≥ 90, SEO ≥ 90 và Performance ≥ 80. Kết quả từng trang ghi vào Bảng 2; ảnh chụp lưu trong thư mục kiemtra/ của kho.
Bảng 1. Yêu cầu bố cục cho từng trang (nhóm chép bảng vào báo cáo, điền cột "Người phụ trách" và ghi tên tệp thực tế nếu đã đổi tên theo đề tài)
TT	Tệp HTML	Bố cục yêu cầu	Kỹ thuật bắt buộc	Người phụ trách
1	index.html	Khung trang chung + khu vực nổi bật (hero) + hàng ba thẻ giới thiệu	Grid cho khung trang; hàng thẻ tự xuống dòng khi màn hình hẹp (flex-wrap hoặc auto-fit)	
2	danh-sach.html	Lưới thẻ sản phẩm / bài viết, kèm tiêu đề mục hoặc thanh lọc	Grid với repeat(auto-fit, minmax(…, 1fr)); ảnh dùng object-fit: cover	
3	chi-tiet.html	Nội dung chính + cột phụ (thông tin thêm, mục liên quan)	Grid hai cột ở màn hình rộng, dồn thành một cột trên điện thoại	
4	gioi-thieu.html	Đoạn văn dài + danh sách thành viên nhóm	Giới hạn độ dài dòng (max-width khoảng 65ch); Flexbox cho danh sách thành viên	
5	lien-he.html	Biểu mẫu liên hệ + khối thông tin liên hệ	Định kiểu label, ô nhập, nút; trạng thái :focus-visible rõ ràng; form dùng tốt trên điện thoại	

Gợi ý tổ chức thư mục css/ và phần đầu tệp CSS
css/
  01-bien.css        :root — màu, cỡ chữ, bo góc, khoảng cách
  02-chuan-hoa.css   box-sizing, lề body, ảnh, phông chữ
  03-bo-cuc.css      header, nav, main, aside, footer   (CSS Grid)
  04-thanh-phan.css  thẻ, nút, biểu mẫu, bảng           (Flexbox)
  05-tien-ich.css    lớp dùng lại: .bao, .chi-hien-tren-may-tinh…
(nạp đúng thứ tự trên bằng năm thẻ <link>, hoặc gộp thành một tệp
 style.css có chú thích chia khối theo đúng năm phần này)
 
/* 01-bien.css */
:root {
  --mau-chinh: #123b6d;   --mau-nhan: #ffc72c;
  --chu: #3a3a3a;         --nen: #ffffff;
  --bo-goc: 8px;          --khoang-cach: 16px;
}
 
/* 02-chuan-hoa.css */
*, *::before, *::after { box-sizing: border-box; }
body { margin: 0; font-family: system-ui, "Segoe UI", sans-serif;
       line-height: 1.6; color: var(--chu); background: var(--nen); }
img { max-width: 100%; height: auto; display: block; }

Gợi ý bố cục mobile-first cho khung trang
/* Mặc định: điện thoại — mọi thứ xếp một cột */
.trang {
  display: grid;
  gap: var(--khoang-cach);
  grid-template-areas: "dau" "menu" "chinh" "ben" "chan";
}
header { grid-area: dau;   }   nav    { grid-area: menu; }
main   { grid-area: chinh; }   aside  { grid-area: ben;  }
footer { grid-area: chan;  }
 
/* Từ máy tính bảng trở lên: thêm cột phụ bên phải */
@media (min-width: 768px) {
  .trang {
    grid-template-columns: 1fr 260px;
    grid-template-areas:
      "dau    dau"
      "menu   menu"
      "chinh  ben"
      "chan   chan";
  }
}
 
/* Lưới thẻ tự đổi số cột, không cần thêm media query */
.luoi-the {
  display: grid;
  gap: 20px;
  grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
}

Cấu trúc kho ltweb-doan-nhomZZ sau Bài tập nhóm số 3 (gợi ý)
ltweb-doan-nhomZZ/
├── index.html   danh-sach.html   chi-tiet.html
├── gioi-thieu.html   lien-he.html
├── danh-sach-bootstrap.html   (bản Bootstrap 5 — Phần B)
├── css/        (01-bien.css … 05-tien-ich.css hoặc style.css)
├── images/     (ảnh đã nén, tên tệp không dấu)
├── kiemtra/    (ảnh kết quả CSS Validator và Lighthouse)
└── thanhvien/
    ├── 23ABC001_an/gioithieu.html   + css/
    ├── 23ABC002_binh/gioithieu.html + css/
    └── …

Bảng 2. Kết quả kiểm chuẩn sau khi có CSS (cột "Lỗi CSS": số lỗi của W3C CSS Validator; Perf. / A11y / SEO: điểm Lighthouse ở chế độ Mobile; mỗi trang một dòng — chép vào báo cáo và điền đầy đủ)
TT	Trang	Lỗi CSS	Perf.	A11y	SEO	Cuộn ngang ở 360px? (có / không)
1	index.html					
2	danh-sach.html					
3	chi-tiet.html					
4	gioi-thieu.html					
5	lien-he.html					
6	danh-sach-bootstrap.html					

Bảng 3. So sánh bản tự viết CSS và bản Bootstrap 5 của cùng một trang (chép vào báo cáo và điền)
Tiêu chí so sánh	Bản tự viết CSS	Bản Bootstrap 5
Thời gian nhóm bỏ ra (giờ)		
Số dòng CSS nhóm tự viết		
Tổng dung lượng CSS trang tải về (KB — xem tab Network)		
Lighthouse Performance (chế độ Mobile)		
Mức tự do khi muốn đổi thiết kế		
Nhận xét của nhóm: khi nào nên dùng framework, khi nào nên tự viết (2 – 3 câu)		

2.3. Phần C – Định kiểu trang cá nhân của từng thành viên (1,5 điểm)
Mỗi thành viên tự thực hiện:
1.	Đổi tên thư mục cá nhân thành thanhvien/<MSSV>_<ten>/ bằng lệnh git mv (ví dụ: thanhvien/23ABC001_an/) và sửa lại mọi liên kết trỏ tới thư mục này — để giảng viên đối chiếu đúng người khi chấm.
2.	Viết tệp CSS riêng cho trang cá nhân của mình, dùng lại bảng màu và biến CSS chung của nhóm; bố cục bằng Grid hoặc Flexbox; bảng thời khóa biểu và danh sách kỹ năng phải đọc được trên điện thoại (cho bảng cuộn ngang trong khung riêng hoặc đổi cách trình bày).
3.	Trang cá nhân phải responsive với ít nhất một điểm ngắt min-width; ảnh chân dung co giãn theo khung; mọi liên kết và nút có trạng thái :hover và :focus-visible.
4.	Kiểm tra trang của mình: CSS Validator 0 lỗi, Lighthouse Accessibility ≥ 90, không có thanh cuộn ngang ở bề rộng 360px. Ghi kết quả vào Bảng 4 kèm ảnh chụp (lưu trong kiemtra/).
5.	git add → commit → push từ tài khoản GitHub của mình, thông điệp commit rõ nghĩa (ví dụ: "Them CSS responsive cho trang ca nhan cua An") — lịch sử commit là minh chứng đóng góp cá nhân.
Bảng 4. Kết quả Phần C của từng thành viên (mỗi người một dòng; chép vào báo cáo và điền đầy đủ)
TT	Họ và tên	Đường dẫn trang cá nhân	Lỗi CSS	Điểm a11y	Cuộn ngang 360px	Số commit
1						
2						
3						
4						

2.4. Phần D – Câu hỏi thảo luận (1,0 điểm)
Nhóm thảo luận và trả lời ngắn gọn (mỗi câu 5 – 10 dòng) trong báo cáo:
1.	Trong lúc làm bài, nhóm chắc chắn gặp tình huống "CSS viết rồi mà không ăn". Hãy kể lại một trường hợp thật trong dự án của nhóm, giải thích bằng cascade và độ ưu tiên (specificity) vì sao khai báo đó bị thua, và cách nhóm đã sửa mà không cần dùng !important.
2.	Vì sao cách viết mobile-first (min-width) thường ít phải ghi đè hơn desktop-first (max-width)? Lấy một đoạn trong chính tệp CSS của nhóm để minh họa; nếu viết theo lối ngược lại thì đoạn đó sẽ dài thêm những gì?
3.	Dựa vào Bảng 3, dùng Bootstrap 5 giúp nhóm nhanh hơn ở chỗ nào và phải đánh đổi những gì (dung lượng tải về, bản sắc riêng, sự phụ thuộc, khả năng sửa)? Với đồ án của nhóm, nhóm chọn tự viết CSS hay dùng framework, vì sao?
4.	Vì sao không được xóa viền focus (outline: none) và vì sao tương phản chữ – nền phải đạt ít nhất 4,5:1? Nêu hai thay đổi CSS cụ thể nhóm đã thực hiện để giữ điểm Accessibility ≥ 90 sau khi trang trí giao diện.
3. Sản phẩm nộp và quy cách
–	Báo cáo: một tệp PDF (không giới hạn số trang), đặt tên NhomZZ_Baitap3.pdf; xuất từ Word / Google Docs (không nộp ảnh quét). Cấu trúc bắt buộc, dùng đúng tên mục: Trang bìa (tên nhóm, thành viên, mã sinh viên, link kho GitHub, URL GitHub Pages); Phần A (bảng số liệu, sơ đồ khối, phân tích, ảnh minh hoạ); Phần B (Bảng 1 đã điền, ảnh chụp từng trang ở hai bề rộng 360px và 1280px, Bảng 2, Bảng 3); Phần C (Bảng 4); Phần D; Tài liệu tham khảo; Phụ lục – Bảng phân công. Mọi số liệu phải ghi thành bảng hoặc câu văn; ảnh chụp chỉ để minh hoạ, phải đọc được chữ và có chú thích (Hình 1, Hình 2…).
–	Mã nguồn: link kho GitHub ltweb-doan-nhomZZ (công khai, đủ lịch sử commit của từng thành viên), URL GitHub Pages và mã commit cuối cùng ghi rõ trong báo cáo; các commit sau hạn nộp không được tính. Đồng thời nén toàn bộ kho (trừ thư mục .git) thành LTW_BTN3_NhomZZ_code.zip.
–	Nơi nộp: mục "Bài tập nhóm số 3" trên e-Learning (nhhai.net); nhóm trưởng nộp một lần cho cả nhóm, trước buổi thực hành tuần 5.
–	Cách chấm: giảng viên chấm trên báo cáo PDF theo rubric ở mục 4, đối chiếu với kho GitHub (mã nguồn CSS, lịch sử commit của từng người), mở website qua GitHub Pages, thu hẹp cửa sổ về 360px và chạy lại CSS Validator / Lighthouse để kiểm chứng.
4. Tiêu chí đánh giá (thuộc bài đánh giá A1.2 – Rubric R1.2)
Tiêu chí	Điểm	Tốt (80 – 100%)	Đạt (50 – 79%)	Chưa đạt (< 50%)
A. Phân tích CSS và tính responsive của website thực tế	3,0	Nhận diện đúng hệ màu, typography và kỹ thuật dàn trang; tìm đúng các điểm ngắt; số liệu Lighthouse và CSS Validator đầy đủ, được giải thích và có đề xuất sửa khả thi.	Đúng phần lớn nội dung; phần điểm ngắt hoặc đề xuất sửa còn sơ sài.	Chỉ chụp màn hình, không phân tích; nhận diện sai kỹ thuật dàn trang.
B. Giao diện responsive của website đồ án	3,5	CSS có tổ chức, dùng biến và quy ước đặt tên nhất quán; bố cục Grid + Flexbox đúng Bảng 1; mobile-first với ≥ 2 điểm ngắt, không cuộn ngang ở 360px; đạt chuẩn Lighthouse; có bản Bootstrap và Bảng 3 so sánh.	Giao diện chạy được và có responsive; còn vài lỗi CSS, thiếu một yêu cầu của Bảng 1 hoặc bản Bootstrap sơ sài.	CSS lộn xộn, lạm dụng !important hoặc style nội tuyến; trang vỡ trên điện thoại; không có bản Bootstrap.
C. Trang cá nhân của từng thành viên	1,5	Mọi thành viên có trang đã định kiểu, responsive, 0 lỗi (Bảng 4 đầy đủ); thư mục đúng quy ước MSSV; commit từ tài khoản của từng người.	Đa số thành viên đạt; một trang còn lỗi, chưa responsive hoặc thiếu commit.	Thiếu trang của từ hai thành viên trở lên, hoặc không có commit cá nhân.
D. Câu hỏi thảo luận	1,0	Trả lời đúng, có lập luận và dẫn chứng từ chính mã nguồn của nhóm cho cả bốn câu.	Trả lời đúng hướng nhưng còn chung chung hoặc thiếu một câu.	Trả lời sai hoặc bỏ trống từ hai câu trở lên.
E. Hình thức báo cáo và tài liệu tham khảo	1,0	Trình bày rõ ràng, đúng quy cách mục 3 (đúng cấu trúc, số liệu có bảng, ảnh rõ và có chú thích); trích dẫn ít nhất ba tài liệu tin cậy.	Đúng quy cách; trích dẫn còn thiếu hoặc chưa đúng chuẩn.	Sai quy cách, không có tài liệu tham khảo.

5. Quy định
–	Báo cáo hoặc mã nguồn sao chép giữa các nhóm, hoặc sao chép từ nguồn khác mà không trích dẫn, nhận 0 điểm cho tất cả các nhóm liên quan. Dùng lại mẫu giao diện (template) có sẵn trên mạng cho Phần B cũng bị tính là sao chép.
–	Được phép dùng công cụ AI để hỗ trợ tra cứu, sửa lỗi, nhưng phải ghi rõ trong báo cáo đã dùng ở đâu; mọi thành viên phải hiểu và giải thích được nội dung nộp khi giảng viên hỏi ngẫu nhiên tại buổi thực hành.
–	Thành viên không tham gia (theo bảng phân công có xác nhận của nhóm) nhận 0 điểm bài tập này.
–	Sau khi chấm, giảng viên có thể yêu cầu nhóm demo trực tiếp sản phẩm và trả lời câu hỏi về phần việc của từng thành viên; điểm có thể được điều chỉnh tăng hoặc giảm theo kết quả demo, cho cả nhóm hoặc riêng từng thành viên.
6. Tài liệu tham khảo và gợi ý
–	Slide bài giảng Chương 3 – CSS3 và thiết kế responsive (3.1 cú pháp, bộ chọn, độ ưu tiên, box model, đơn vị đo; 3.2 màu sắc và typography; 3.3 Flexbox và Grid; 3.4 responsive; 3.5 Bootstrap 5; 3.6 tổ chức CSS và kiểm tra chất lượng).
–	J. N. Robbins – Learning Web Design, 5th ed., O’Reilly, 2018: Phần III – CSS for Presentation.
–	MDN Web Docs – CSS reference; "A complete guide to Flexbox" và "CSS grid layout" – developer.mozilla.org.
–	web.dev/learn/css và web.dev/learn/design (Google) – khóa học miễn phí về CSS và thiết kế responsive.
–	Bootstrap 5 Documentation – getbootstrap.com/docs (mục Layout, Components, Utilities).
–	W3C CSS Validation Service – jigsaw.w3.org/css-validator; Lighthouse – developer.chrome.com/docs/lighthouse; caniuse.com – tra mức hỗ trợ của trình duyệt.
–	Công cụ kiểm tra tương phản màu: WebAIM Contrast Checker – webaim.org/resources/contrastchecker; công cụ nén ảnh: squoosh.app.
Phụ lục A – Mẫu bảng phân công công việc và tự đánh giá
TT	Họ và tên	Mã sinh viên	Tài khoản GitHub	Công việc đảm nhận	Đóng góp (%)
1				Nhóm trưởng; …	
2					
3					
4					

