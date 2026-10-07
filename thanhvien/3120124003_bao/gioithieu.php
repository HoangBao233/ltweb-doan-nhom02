<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Trang giới thiệu cá nhân của Lê Hoàng Bảo, thành viên dự án IT-EduShare.">
  <title>Giới thiệu cá nhân | Lê Hoàng Bảo</title>
  
  <!-- Nạp 5 file CSS chung của nhóm -->
  <link rel="stylesheet" href="../../css/01-bien.css">
  <link rel="stylesheet" href="../../css/02-chuan-hoa.css">
  <link rel="stylesheet" href="../../css/03-bo-cuc.css">
  <link rel="stylesheet" href="../../css/04-thanh-phan.css">
  <link rel="stylesheet" href="../../css/05-tien-ich.css">
  
  <!-- Nạp file CSS cá nhân -->
  <link rel="stylesheet" href="css/canhan-bao.css">
</head>
<body class="trang">
  
  <!-- Header -->
  <header class="trang-dau can-giua-chu">
    <p class="trang-dau__khieu-giao">ITeduShare - Cùng giáo viên Tin học kiến tạo tương lai</p>
  </header>

  <!-- Menu điều hướng -->
  <nav class="thanh-dieu-huong" aria-label="Menu chính">
    <ul class="menu-chinh">
      <li><a href="../../index.php" class="menu-chinh__lien-ket">Quay về Trang chủ Nhóm</a></li>
      <li><a href="../../ve-chung-toi.php" class="menu-chinh__lien-ket">Quay về Trang Giới thiệu Nhóm</a></li>
    </ul>
  </nav>

  <main class="trang__chinh phan-lien-he">
    <h1 class="phan-lien-he__tieu-de can-giua-chu">Hồ sơ thành viên: Lê Hoàng Bảo</h1>

    <section class="bieu-mau">
      <h2>Giới thiệu bản thân</h2>
      <div class="gioi-thieu-grid">
          <div class="anh-dai-dien">
             <img src="avatar-bao.jpg" alt="Ảnh chân dung của Lê Hoàng Bảo" class="anh-chan-dung">
          </div>
          <div class="noi-dung-gioi-thieu">
             <p>Xin chào, tôi là Lê Hoàng Bảo, sinh viên Khoa Toán - Tin, Trường Đại học Sư phạm - Đại học Đà Nẵng. Tôi là người đảm nhận nhiệm vụ trưởng nhóm cho dự án website này.</p>
             
             <h3>Danh sách kỹ năng nổi bật</h3>
             <ul class="danh-sach-ky-nang">
               <li>Lập trình Python (Pygame, Tkinter, OpenCV)</li>
               <li>Thiết kế CSDL (PostgreSQL, SQL Server)</li>
               <li>HTML5 &amp; CSS3</li>
               <li>Quản lý mã nguồn với Git/GitHub</li>
             </ul>
          </div>
      </div>
    </section>

    <article class="bieu-mau">
      <h2>Vai trò trong dự án ITeduShare &amp; Sở thích</h2>
      <p>Hiện tại tôi đang đảm nhận vai trò trưởng nhóm trong dự án IT-EduShare. Ngoài ra, tôi cũng có nhiều sở thích như đọc sách, chơi game và tham gia các hoạt động thể thao.</p>
      <button type="button" class="nut-thao-tac nut-thao-tac--xem nut-lien-he-ca-nhan">Kết nối với tôi</button>
    </article>

    <section class="thong-tin">
      <h2>Thời khóa biểu cá nhân</h2>
      <div class="khung-cuon-bang">
        <table class="bang-hoc-lieu">
          <caption class="bang-hoc-lieu__chu-thich">Kế hoạch học tập và làm việc trong tuần</caption>
          <thead>
            <tr>
              <th scope="col" class="bang-hoc-lieu__dau-cot">Thứ</th>
              <th scope="col" class="bang-hoc-lieu__dau-cot">Sáng</th>
              <th scope="col" class="bang-hoc-lieu__dau-cot">Chiều</th>
              <th scope="col" class="bang-hoc-lieu__dau-cot">Tối</th>
            </tr>
          </thead>
          <tbody>
            <tr class="bang-hoc-lieu__hang">
              <th scope="row" class="bang-hoc-lieu__o-tieu-de">Thứ Hai</th>
              <td class="bang-hoc-lieu__o">Học trên trường</td>
              <td class="bang-hoc-lieu__o">Tự học HTML5</td>
              <td class="bang-hoc-lieu__o">Dạy kèm tin học</td>
            </tr>
            <tr class="bang-hoc-lieu__hang">
              <th scope="row" class="bang-hoc-lieu__o-tieu-de">Thứ Ba</th>
              <td class="bang-hoc-lieu__o">Nghỉ</td>
              <td class="bang-hoc-lieu__o">Học trên trường</td>
              <td class="bang-hoc-lieu__o">Làm bài tập nhóm</td>
            </tr>
            <tr class="bang-hoc-lieu__hang">
              <th scope="row" class="bang-hoc-lieu__o-tieu-de">Thứ Tư</th>
              <td class="bang-hoc-lieu__o">Học trên trường</td>
              <td class="bang-hoc-lieu__o">Họp nhóm</td>
              <td class="bang-hoc-lieu__o">Coding</td>
            </tr>
          </tbody>
        </table>
      </div>
    </section>
  </main>

  <footer class="trang__chan can-giua-chu">
    <p class="trang__ban-quyen">© 2026 Nhóm 02 - Khoa Toán - Tin, Trường Đại học Sư phạm - Đại học Đà Nẵng</p>
  </footer>
  
  <script type="module" src="js/canhan.js"></script>
</body>
</html>
