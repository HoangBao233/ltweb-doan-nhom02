<?php
// thanhvien/3120124027_tan/gioithieu.php — Trang cá nhân Nguyễn Cảnh Tấn (MSSV 3120124027)
// Bài tập 6 - Phần B: Sổ lưu bút phân trang/phân quyền & Tra cứu dự án (Tìm, Lọc, Sắp xếp) + Thống kê lượt xem (MySQL PDO)
// Cách thử: 1. Gửi lưu bút để trống (báo lỗi), gửi hợp lệ rồi F5 (không gửi lại do PRG, xem phân trang ?trang_lb=2).
//           2. Đăng nhập chủ trang (dùng nút kiểm thử nhanh hoặc ?test_login=tan) để hiện nút Xóa; khách không xóa được.
//           3. Tìm kiếm dự án theo từ khóa (LIKE), lọc theo công nghệ, sắp xếp; mỗi lần tải trang bộ đếm lượt xem tự tăng.

require __DIR__ . '/../../inc/config.php';

const MSSV = '3120124027';
$goc    = '../../';
$tieuDe = 'Nguyễn Cảnh Tấn | ITeduShare';
$trang  = '';

// 1. Lấy thông tin thành viên từ bảng thanh_vien
$stTv = db()->prepare('SELECT ho_ten, nguoi_dung_id FROM thanh_vien WHERE mssv = ?');
$stTv->execute([MSSV]);
$tv = $stTv->fetch();

// Hỗ trợ kiểm thử nhanh quyền chủ trang (dành cho giảng viên / kiểm thử viên)
if (isset($_GET['test_login'])) {
    if ($_GET['test_login'] === 'tan') {
        $_SESSION['uid'] = $tv['nguoi_dung_id'] ?? 3;
    } elseif ($_GET['test_login'] === 'khach') {
        $_SESSION['uid'] = 2; // tài khoản học viên (không phải chủ trang)
    } elseif ($_GET['test_login'] === 'logout') {
        unset($_SESSION['uid'], $_SESSION['user'], $_SESSION['user_id']);
    }
    chuyen_trang('gioithieu.php');
}

$ndHienTai  = nguoiDung();
$laChuTrang = (($ndHienTai['id'] ?? 0) === ($tv['nguoi_dung_id'] ?? null)) && ($tv['nguoi_dung_id'] !== null);

// 2. Tự động tăng bộ đếm lượt xem theo ngày (INSERT ... ON DUPLICATE KEY UPDATE)
// Mỗi phiên (Session) chỉ đếm 1 lần để tránh tăng liên tục khi F5
if (empty($_SESSION['da_dem_xem_tan'])) {
    $stTangXem = db()->prepare('INSERT INTO tv_luot_xem (mssv, ngay, so_luot) VALUES (?, CURDATE(), 1) ON DUPLICATE KEY UPDATE so_luot = so_luot + 1');
    $stTangXem->execute([MSSV]);
    $_SESSION['da_dem_xem_tan'] = true;
}

// Lấy lượt xem hôm nay và tổng lượt xem
$stHomNay = db()->prepare('SELECT so_luot FROM tv_luot_xem WHERE mssv = ? AND ngay = CURDATE()');
$stHomNay->execute([MSSV]);
$luotXemHomNay = (int)($stHomNay->fetchColumn() ?: 0);

$stTongXem = db()->prepare('SELECT COALESCE(SUM(so_luot), 0) FROM tv_luot_xem WHERE mssv = ?');
$stTongXem->execute([MSSV]);
$tongLuotXem = (int)$stTongXem->fetchColumn();

// Lấy thống kê lượt xem 7 ngày gần nhất
$st7Ngay = db()->prepare('SELECT ngay, so_luot FROM tv_luot_xem WHERE mssv = ? ORDER BY ngay DESC LIMIT 7');
$st7Ngay->execute([MSSV]);
$dsLuotXem7Ngay = $st7Ngay->fetchAll();

// =====================================================================
// CHỨC NĂNG 1 — SỔ LƯU BÚT (PHÂN TRANG & PHÂN QUYỀN CHỦ TRANG XÓA)
// =====================================================================
$loi = [];
$du  = [
    'ten_khach' => '',
    'loi_nhan'  => '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $xoa = filter_var($_POST['xoa'] ?? '', FILTER_VALIDATE_INT);
    if ($xoa && $laChuTrang) {
        $stXoa = db()->prepare('DELETE FROM tv_luu_but WHERE id = ? AND mssv = ?');
        $stXoa->execute([$xoa, MSSV]);
        $_SESSION['flash'] = 'Đã xóa lời nhắn lưu bút thành công!';
        chuyen_trang('gioithieu.php');
    } elseif ($xoa && !$laChuTrang) {
        $loi['quyen'] = 'Chỉ chủ trang (Nguyễn Cảnh Tấn) mới có quyền xóa lời nhắn!';
    } elseif (isset($_POST['gui_luu_but'])) {
        $tenKhach = trim($_POST['ten_khach'] ?? '');
        $loiNhan  = trim($_POST['loi_nhan'] ?? '');
        $du['ten_khach'] = $tenKhach;
        $du['loi_nhan']  = $loiNhan;

        if ($tenKhach === '' || mb_strlen($tenKhach) > 50) {
            $loi['ten_khach'] = 'Họ tên người gửi phải từ 1 đến 50 ký tự.';
        }
        if ($loiNhan === '' || mb_strlen($loiNhan) > 500) {
            $loi['loi_nhan'] = 'Lời nhắn phải từ 1 đến 500 ký tự.';
        }

        if (empty($loi)) {
            $stThem = db()->prepare('INSERT INTO tv_luu_but (mssv, ten_khach, loi_nhan) VALUES (?, ?, ?)');
            $stThem->execute([MSSV, $tenKhach, $loiNhan]);
            $_SESSION['flash'] = 'Cảm ơn bạn đã để lại lời nhắn lưu bút!';
            chuyen_trang('gioithieu.php');
        }
    }
}

$thongBaoFlash = $_SESSION['flash'] ?? '';
unset($_SESSION['flash']);

// Phân trang sổ lưu bút: 5 lời nhắn mỗi trang, mới nhất xếp trước
$stDemLB = db()->prepare('SELECT COUNT(*) FROM tv_luu_but WHERE mssv = ?');
$stDemLB->execute([MSSV]);
$tongSoLuuBut   = (int)$stDemLB->fetchColumn();
$soDongMoiTrang = 5;
$soTrangLB      = max(1, (int)ceil($tongSoLuuBut / $soDongMoiTrang));
$trangHienTaiLB = filter_var($_GET['trang_lb'] ?? 1, FILTER_VALIDATE_INT, [
    'options' => ['min_range' => 1, 'default' => 1]
]);
$trangHienTaiLB = min($trangHienTaiLB, $soTrangLB);
$offsetLB       = ($trangHienTaiLB - 1) * $soDongMoiTrang;

$stLuuBut = db()->prepare('SELECT id, ten_khach, loi_nhan, thoi_gian FROM tv_luu_but WHERE mssv = :mssv ORDER BY thoi_gian DESC, id DESC LIMIT :limit OFFSET :offset');
$stLuuBut->bindValue(':mssv', MSSV, PDO::PARAM_STR);
$stLuuBut->bindValue(':limit', $soDongMoiTrang, PDO::PARAM_INT);
$stLuuBut->bindValue(':offset', $offsetLB, PDO::PARAM_INT);
$stLuuBut->execute();
$dsLuuBut = $stLuuBut->fetchAll();

// =====================================================================
// CHỨC NĂNG 2 — TRA CỨU DỰ ÁN CÁ NHÂN (TÌM KIẾM, LỌC, SẮP XẾP)
// =====================================================================
$stCN = db()->prepare('SELECT DISTINCT cong_nghe FROM tv_du_an_tan WHERE mssv = ? ORDER BY cong_nghe ASC');
$stCN->execute([MSSV]);
$dsCongNghe = $stCN->fetchAll(PDO::FETCH_COLUMN);

$tuKhoa   = trim($_GET['q'] ?? '');
$congNghe = trim($_GET['cong_nghe'] ?? 'tat-ca');
$sapXep   = trim($_GET['sx'] ?? 'moi_nhat');

// Sắp xếp an toàn bằng match theo danh sách cho phép (chống SQL Injection)
$orderClause = match ($sapXep) {
    'ten_az'    => 'ten_du_an ASC',
    'ten_za'    => 'ten_du_an DESC',
    'tai_nhieu' => 'luot_tai DESC, id DESC',
    'nam_tang'  => 'nam_thuc_hien ASC, id ASC',
    default     => 'nam_thuc_hien DESC, id DESC', // moi_nhat
};

// Dựng mảng điều kiện WHERE và mảng tham số prepared statement
$dkDuAn = ['mssv = ?'];
$tsDuAn = [MSSV];

if ($tuKhoa !== '') {
    $dkDuAn[] = '(ten_du_an LIKE ? OR cong_nghe LIKE ? OR mo_ta LIKE ?)';
    $escaped  = '%' . addcslashes($tuKhoa, '%_\\') . '%';
    $tsDuAn[] = $escaped;
    $tsDuAn[] = $escaped;
    $tsDuAn[] = $escaped;
}

if ($congNghe !== '' && $congNghe !== 'tat-ca') {
    $dkDuAn[] = 'cong_nghe = ?';
    $tsDuAn[] = $congNghe;
}

$sqlDuAn = 'SELECT id, ten_du_an, cong_nghe, nam_thuc_hien, mo_ta, luot_tai 
            FROM tv_du_an_tan 
            WHERE ' . implode(' AND ', $dkDuAn) . ' 
            ORDER BY ' . $orderClause;

$stDuAn = db()->prepare($sqlDuAn);
$stDuAn->execute($tsDuAn);
$dsDuAn = $stDuAn->fetchAll();

require __DIR__ . '/../../inc/header.php';
?>
    <!-- CSS Cá nhân của Tấn -->
    <link rel="stylesheet" href="<?= $goc ?>thanhvien/3120124027_tan/css/canhan-tan.css">

    <!-- Nút Quay về -->
    <nav class="thanh-dieu-huong" aria-label="Điều hướng phụ" style="background: var(--nen-phu, #f4f6f8); padding: 0.75rem 1rem;">
        <ul style="display: flex; gap: 1rem; list-style: none; padding: 0; margin: 0; flex-wrap: wrap;">
            <li><a href="<?= $goc ?>index.php" class="menu-chinh__lien-ket">⬅ Quay về Trang chủ</a></li>
            <li><a href="<?= $goc ?>ve-chung-toi.php" class="menu-chinh__lien-ket">⬅ Quay về Giới thiệu Nhóm</a></li>
        </ul>
    </nav>

    <main class="trang-ca-nhan">
        <!-- CỘT TRÁI: THÔNG TIN CƠ BẢN & THỐNG KÊ LƯỢT XEM -->
        <div class="cot-trai">
            <img src="<?= $goc ?>thanhvien/3120124027_tan/avatar-tan.jpg"
                 alt="Nguyễn Cảnh Tấn" class="anh-dai-dien">
            <h1>Nguyễn Cảnh Tấn</h1>
            <p>MSSV: 3120124027</p>
            <p>Ngành: Sư phạm Tin học</p>
            <p>Trường Đại học Sư phạm – ĐH Đà Nẵng</p>

            <!-- Bộ đếm lượt xem trang theo ngày và tổng lượt xem -->
            <div class="cong-cu-doc" style="margin-top: 1.5rem; padding: 1rem;
                 background: #f0f8ff; border: 1px solid #bee3f8; border-radius: 8px;">
                <h2 style="font-size: 1rem; margin-bottom: 0.5rem;">📊 Lượt xem hôm nay</h2>
                <p style="font-size: 2rem; font-weight: bold; color: #2b6cb0; margin: 0;">
                    <?= number_format($luotXemHomNay) ?>
                </p>
                <p style="font-size: 0.85rem; color: #4a5568; margin-top: 0.25rem;">
                    Tổng toàn thời gian: <strong><?= number_format($tongLuotXem) ?></strong> lượt
                </p>
            </div>

            <!-- Bảng thống kê lượt xem 7 ngày gần nhất -->
            <div class="khung-thong-ke-xem">
                <h2 style="font-size: 0.95rem; margin-bottom: 0.5rem; color: #2d3748;">📅 Lượt xem 7 ngày gần nhất</h2>
                <div class="bang-cuon-ngang" style="min-width: 0;">
                    <table class="bang-mini">
                        <thead>
                            <tr>
                                <th scope="col">Ngày</th>
                                <th scope="col">Số lượt</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($dsLuotXem7Ngay)): ?>
                                <tr><td colspan="2" style="color: #718096; font-style: italic;">Chưa có dữ liệu</td></tr>
                            <?php else: ?>
                                <?php foreach ($dsLuotXem7Ngay as $item): ?>
                                    <tr>
                                        <td><?= e(date('d/m/Y', strtotime($item['ngay']))) ?></td>
                                        <td><strong><?= number_format((int)$item['so_luot']) ?></strong></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Hỗ trợ đọc (giữ từ bài 4, 0 lỗi W3C) -->
            <div class="cong-cu-doc" style="margin-top: 1.5rem;">
                <h2>Hỗ trợ đọc</h2>
                <div class="nhom-nut">
                    <button type="button" id="btn-giam-chu" class="nut-cong-cu" aria-label="Thu nhỏ cỡ chữ">A-</button>
                    <button type="button" id="btn-mac-dinh" class="nut-cong-cu" aria-label="Cỡ chữ mặc định">A</button>
                    <button type="button" id="btn-tang-chu" class="nut-cong-cu" aria-label="Phóng to cỡ chữ">A+</button>
                </div>
            </div>
        </div>

        <!-- CỘT PHẢI: NỘI DUNG CHÍNH -->
        <div class="cot-phai">
            <!-- Thanh trạng thái kiểm thử quyền chủ trang (dành cho GV/chấm điểm) -->
            <aside class="thanh-test-quyen" aria-label="Kiểm thử phân quyền chủ trang">
                <div>
                    <strong>👤 Trạng thái đăng nhập:</strong>
                    <?php if ($ndHienTai): ?>
                        <?= e($ndHienTai['ho_ten']) ?> (<?= e($ndHienTai['email']) ?>)
                        <?php if ($laChuTrang): ?>
                            <span style="color: #2b6cb0; font-weight: bold;">[Chủ trang Tấn - Có quyền xóa]</span>
                        <?php else: ?>
                            <span style="color: #c53030;">[Khách - Không có quyền xóa]</span>
                        <?php endif; ?>
                    <?php else: ?>
                        <span style="color: #718096;">Chưa đăng nhập (Khách vãng lai)</span>
                    <?php endif; ?>
                </div>
                <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                    <?php if ($laChuTrang): ?>
                        <a href="gioithieu.php?test_login=logout" class="nut-cong-cu" style="text-decoration: none; font-size: 0.8rem;">Đăng xuất</a>
                        <a href="gioithieu.php?test_login=khach" class="nut-cong-cu" style="text-decoration: none; font-size: 0.8rem;">Thử làm Khách</a>
                    <?php else: ?>
                        <a href="gioithieu.php?test_login=tan" class="nut-cong-cu" style="text-decoration: none; font-size: 0.8rem; background: #2b6cb0; color: #fff;">Đăng nhập Chủ trang (Tấn)</a>
                    <?php endif; ?>
                </div>
            </aside>

            <!-- Thông báo Flash (PRG) -->
            <?php if ($thongBaoFlash !== ''): ?>
                <div class="thong-bao-flash" role="alert">
                    ✅ <?= e($thongBaoFlash) ?>
                </div>
            <?php endif; ?>

            <?php if (isset($loi['quyen'])): ?>
                <div class="thong-bao-loi" role="alert">
                    ⚠️ <?= e($loi['quyen']) ?>
                </div>
            <?php endif; ?>

            <!-- Đồng hồ đếm ngược (giữ từ bài 4) -->
            <div class="khung-dem-nguoc">
                <h2>⏳ Đếm ngược đến ngày báo cáo Thiết kế lập trình Web</h2>
                <div id="dong-ho-thi" class="dong-ho" aria-live="polite" aria-atomic="true">
                    Đang tải thời gian...
                </div>
            </div>

            <!-- ===================================================== -->
            <!-- CHỨC NĂNG 2 — TRA CỨU DỰ ÁN CÁ NHÂN                  -->
            <!-- (Tìm kiếm LIKE + Lọc công nghệ + Sắp xếp match)       -->
            <!-- ===================================================== -->
            <section aria-labelledby="tieu-de-du-an">
                <h2 id="tieu-de-du-an">📂 Danh sách Dự án &amp; Học liệu tiêu biểu</h2>
                <p style="color: #718096; margin-top: -0.5rem; margin-bottom: 1rem;">
                    Tra cứu và tải về các sản phẩm phần mềm giáo dục do Nguyễn Cảnh Tấn phát triển.
                </p>

                <!-- Form tìm kiếm, lọc và sắp xếp (GET chuẩn W3C có đầy đủ label) -->
                <div class="khung-tim-kiem-du-an">
                    <form method="get" action="gioithieu.php" role="search" class="form-tim-du-an">
                        <div class="nhom-nhap">
                            <label for="tu-khoa-du-an">Từ khóa tìm kiếm:</label>
                            <input type="text" id="tu-khoa-du-an" name="q"
                                   value="<?= e($tuKhoa) ?>"
                                   placeholder="Tên dự án hoặc mô tả...">
                        </div>

                        <div class="nhom-nhap">
                            <label for="loc-cong-nghe">Công nghệ:</label>
                            <select id="loc-cong-nghe" name="cong_nghe">
                                <option value="tat-ca">-- Tất cả công nghệ --</option>
                                <?php foreach ($dsCongNghe as $cn): ?>
                                    <option value="<?= e($cn) ?>" <?= $congNghe === $cn ? 'selected' : '' ?>>
                                        <?= e($cn) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="nhom-nhap">
                            <label for="sap-xep-du-an">Sắp xếp theo:</label>
                            <select id="sap-xep-du-an" name="sx">
                                <option value="moi_nhat" <?= $sapXep === 'moi_nhat' ? 'selected' : '' ?>>Năm mới nhất</option>
                                <option value="nam_tang" <?= $sapXep === 'nam_tang' ? 'selected' : '' ?>>Năm cũ nhất</option>
                                <option value="tai_nhieu" <?= $sapXep === 'tai_nhieu' ? 'selected' : '' ?>>Tải nhiều nhất</option>
                                <option value="ten_az" <?= $sapXep === 'ten_az' ? 'selected' : '' ?>>Tên dự án (A-Z)</option>
                                <option value="ten_za" <?= $sapXep === 'ten_za' ? 'selected' : '' ?>>Tên dự án (Z-A)</option>
                            </select>
                        </div>

                        <div>
                            <button type="submit" class="nut-cong-cu" style="background: #2b6cb0; color: #fff; width: 100%; padding: 0.65rem 1rem;">
                                🔍 Lọc &amp; Tìm
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Danh sách kết quả dự án -->
                <?php if (empty($dsDuAn)): ?>
                    <p style="color: #718096; font-style: italic; padding: 1.5rem; background: #f7fafc; border: 1px dashed #cbd5e0; border-radius: 8px; text-align: center;">
                        Không tìm thấy dự án hoặc học liệu nào phù hợp với điều kiện tra cứu.
                    </p>
                <?php else: ?>
                    <div class="du-an-grid">
                        <?php foreach ($dsDuAn as $da): ?>
                            <article class="du-an-card">
                                <div>
                                    <h3><?= e($da['ten_du_an']) ?></h3>
                                    <div class="du-an-tags">
                                        <span class="tag-cn"><?= e($da['cong_nghe']) ?></span>
                                        <span class="tag-nam">Năm <?= e($da['nam_thuc_hien']) ?></span>
                                    </div>
                                    <p class="du-an-mo-ta"><?= e($da['mo_ta']) ?></p>
                                </div>
                                <div class="du-an-footer">
                                    <span>📥 <strong><?= number_format((int)$da['luot_tai']) ?></strong> lượt tải</span>
                                    <span style="color: #38a169; font-weight: 600;">Miễn phí</span>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </section>

            <!-- ===================================================== -->
            <!-- CHỨC NĂNG 1 — SỔ LƯU BÚT (PHÂN TRANG & XÓA CHỦ TRANG) -->
            <!-- ===================================================== -->
            <section aria-labelledby="tieu-de-luu-but" style="margin-top: 2rem;">
                <h2 id="tieu-de-luu-but">✍️ Sổ lưu bút &amp; Nhận xét của khách</h2>
                <p style="color: #718096; margin-top: -0.5rem; margin-bottom: 1rem;">
                    Để lại lời nhắn đóng góp, nhận xét hoặc câu hỏi trao đổi với Tấn nhé!
                </p>

                <div class="khung-luu-but">
                    <!-- Form gửi lưu bút mới (POST chuẩn W3C có label đầy đủ) -->
                    <form method="post" action="gioithieu.php" novalidate class="form-luu-but">
                        <div class="nhom-nhap">
                            <label for="ten-khach">Họ tên của bạn <span style="color: #e53e3e;">*</span>:</label>
                            <input type="text" id="ten-khach" name="ten_khach"
                                   required maxlength="50"
                                   value="<?= e($du['ten_khach']) ?>"
                                   placeholder="Ví dụ: Thầy Lê Viết Chung, Bạn Nguyễn Văn A...">
                            <?php if (isset($loi['ten_khach'])): ?>
                                <span class="tin-loi"><?= e($loi['ten_khach']) ?></span>
                            <?php endif; ?>
                        </div>

                        <div class="nhom-nhap">
                            <label for="loi-nhan">Lời nhắn lưu bút <span style="color: #e53e3e;">*</span>:</label>
                            <textarea id="loi-nhan" name="loi_nhan" rows="3"
                                      required maxlength="500"
                                      placeholder="Nhập nội dung lời nhắn hoặc lời chúc của bạn (1 - 500 ký tự)..."><?= e($du['loi_nhan']) ?></textarea>
                            <?php if (isset($loi['loi_nhan'])): ?>
                                <span class="tin-loi"><?= e($loi['loi_nhan']) ?></span>
                            <?php endif; ?>
                        </div>

                        <div>
                            <button type="submit" name="gui_luu_but" value="1" class="nut-cong-cu"
                                    style="background: #2b6cb0; color: #fff; padding: 0.65rem 1.5rem; font-size: 0.95rem;">
                                ✉️ Gửi lời nhắn
                            </button>
                        </div>
                    </form>

                    <!-- Danh sách lời nhắn đã nhận (Phân trang 5 dòng/trang) -->
                    <h3 style="font-size: 1.1rem; margin-bottom: 1rem; color: #2d3748;">
                        Danh sách lời nhắn (<?= number_format($tongSoLuuBut) ?> lời nhắn)
                    </h3>

                    <?php if (empty($dsLuuBut)): ?>
                        <p style="color: #718096; font-style: italic;">Chưa có lời nhắn nào trong sổ lưu bút.</p>
                    <?php else: ?>
                        <ul class="ds-luu-but">
                            <?php foreach ($dsLuuBut as $lb): ?>
                                <li class="item-luu-but">
                                    <div class="header-luu-but">
                                        <div>
                                            <span class="ten-khach-lb"><?= e($lb['ten_khach']) ?></span>
                                            <span class="thoi-gian-lb">• <?= e(date('d/m/Y H:i', strtotime($lb['thoi_gian']))) ?></span>
                                        </div>

                                        <!-- Nút XÓA: CHỈ HIỂN THỊ KHI ĐĂNG NHẬP CHỦ TRANG CỦA TẤN -->
                                        <?php if ($laChuTrang): ?>
                                            <form method="post" action="gioithieu.php"
                                                  onsubmit="return confirm('Bạn có chắc chắn muốn xóa lời nhắn của <?= e(addslashes($lb['ten_khach'])) ?>?');"
                                                  style="display: inline; margin: 0; padding: 0;">
                                                <input type="hidden" name="xoa" value="<?= (int)$lb['id'] ?>">
                                                <button type="submit" class="nut-xoa-lb" aria-label="Xóa lời nhắn của <?= e($lb['ten_khach']) ?>">
                                                    🗑️ Xóa
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                    <p class="noi-dung-lb"><?= nl2br(e($lb['loi_nhan'])) ?></p>
                                </li>
                            <?php endforeach; ?>
                        </ul>

                        <!-- Điều khiển phân trang Sổ lưu bút -->
                        <?php if ($soTrangLB > 1): ?>
                            <nav class="phan-trang" aria-label="Phân trang sổ lưu bút">
                                <?php if ($trangHienTaiLB > 1): ?>
                                    <a href="gioithieu.php?trang_lb=<?= $trangHienTaiLB - 1 ?>" class="nut-trang" aria-label="Trang trước">« Trước</a>
                                <?php endif; ?>

                                <?php for ($p = 1; $p <= $soTrangLB; $p++): ?>
                                    <a href="gioithieu.php?trang_lb=<?= $p ?>"
                                       class="nut-trang <?= $p === $trangHienTaiLB ? 'hien-tai' : '' ?>"
                                       <?= $p === $trangHienTaiLB ? 'aria-current="page"' : '' ?>>
                                        <?= $p ?>
                                    </a>
                                <?php endfor; ?>

                                <?php if ($trangHienTaiLB < $soTrangLB): ?>
                                    <a href="gioithieu.php?trang_lb=<?= $trangHienTaiLB + 1 ?>" class="nut-trang" aria-label="Trang sau">Sau »</a>
                                <?php endif; ?>
                            </nav>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </section>

            <!-- Thời khóa biểu (giữ từ bài 4, đảm bảo đạt chuẩn a11y & W3C) -->
            <section aria-labelledby="tieu-de-tkb" style="margin-top: 2rem;">
                <h2 id="tieu-de-tkb">Thời khóa biểu (Học kỳ hiện tại)</h2>
                <div class="bang-cuon-ngang">
                    <table>
                        <caption>Thời khóa biểu học tập các môn chuyên ngành Tin học</caption>
                        <thead>
                            <tr>
                                <th scope="col">Thứ</th>
                                <th scope="col">Thời gian</th>
                                <th scope="col">Môn học</th>
                                <th scope="col">Phòng học</th>
                                <th scope="col">Giảng viên</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr><td rowspan="2"><strong>Thứ 2</strong></td><td>07:00 - 08:40</td><td>An Toàn Thông Tin</td><td>Phòng B3-206</td><td>Đoàn Duy Bình</td></tr>
                            <tr><td>18:00</td><td><strong>Học Tiếng Anh</strong></td><td>Thể thao</td><td>—</td></tr>
                            <tr><td><strong>Thứ 3</strong></td><td>07:00 - 09:35</td><td>Phân Tích và Thiết Kế HTTT</td><td>Phòng B1.106</td><td>Lê Thị Thanh Bình</td></tr>
                            <tr><td rowspan="3"><strong>Thứ 4</strong></td><td>09:40 - 12:15</td><td>Phương Pháp Dạy Học Bộ Môn Tin Học</td><td>Phòng A5-406</td><td>Lê Viết Chung</td></tr>
                            <tr><td>13:00 - 16:30</td><td>Hệ Quản Trị Cơ Sở Dữ Liệu</td><td>Phòng B3-404</td><td>Đoàn Duy Bình</td></tr>
                            <tr><td>18:00</td><td><strong>Học Tiếng Anh</strong></td><td>Thể thao</td><td>—</td></tr>
                            <tr><td><strong>Thứ 5</strong></td><td>07:00 - 08:40</td><td>Lịch Sử Đảng Cộng Sản Việt Nam</td><td>Phòng A6-502</td><td>Nguyễn Thế Hà</td></tr>
                            <tr><td><strong>Thứ 6</strong></td><td>18:00</td><td><strong>Học Tiếng Anh</strong></td><td>Thể thao</td><td>—</td></tr>
                            <tr><td><strong>Thứ 7</strong></td><td>07:00 - 09:35</td><td>Thiết Kế và Lập Trình Web</td><td>Phòng B3-303</td><td>Nguyễn Hoàng Hải</td></tr>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </main>

    <!-- JS cá nhân (đồng hồ, hỗ trợ đọc — giữ từ bài 4) -->
    <script type="module" src="<?= $goc ?>thanhvien/3120124027_tan/js/canhan.js"></script>

<?php require __DIR__ . '/../../inc/footer.php'; ?>
