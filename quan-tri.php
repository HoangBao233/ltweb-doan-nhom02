<?php
/**
 * quan-tri.php — Trang quản trị (chỉ admin)
 */
require_once 'inc/bao-ve.php';   // phải đứng đầu, tự require config.php
require_once 'src/Data/KhoLienHe.php';
require_once 'src/Data/KhoGiaoAn.php';

$khoLienHe   = new KhoLienHe();
$khoGiaoAn   = new KhoGiaoAn();

$danhSachLienHe = $khoLienHe->tatCa();
$tatCaGiaoAn    = $khoGiaoAn->tatCa();

// Thống kê
$soThcs  = count(array_filter($tatCaGiaoAn, fn($g) => $g->cap_hoc === 'THCS'));
$soThpt  = count(array_filter($tatCaGiaoAn, fn($g) => $g->cap_hoc === 'THPT'));
$soMienPhi = count(array_filter($tatCaGiaoAn, fn($g) => $g->gia === 0));

$tieu_de_trang  = 'Quản trị';
$meta_mo_ta     = '';
$trang_hien_tai = 'quan-tri';

require_once 'inc/header.php';
?>

    <main class="trang__chinh">
        <h1>⚙ Trang quản trị</h1>
        <p>Xin chào, <strong><?= e($_SESSION['admin']['ten_hien_thi']) ?></strong>!
           <a href="dang-xuat.php" style="margin-left:1rem; font-size:0.9rem; color:red;">Đăng xuất</a>
        </p>

        <!-- Thống kê -->
        <section style="display:flex; gap:1.5rem; flex-wrap:wrap; margin: 1.5rem 0;">
            <div style="flex:1; min-width:140px; background:#e3f2fd; padding:1.2rem; border-radius:8px; text-align:center;">
                <p style="font-size:2rem; font-weight:bold; margin:0;"><?= count($tatCaGiaoAn) ?></p>
                <p style="margin:0; color:#555;">Tổng giáo án</p>
            </div>
            <div style="flex:1; min-width:140px; background:#e8f5e9; padding:1.2rem; border-radius:8px; text-align:center;">
                <p style="font-size:2rem; font-weight:bold; margin:0;"><?= $soThcs ?></p>
                <p style="margin:0; color:#555;">Cấp THCS</p>
            </div>
            <div style="flex:1; min-width:140px; background:#fff3e0; padding:1.2rem; border-radius:8px; text-align:center;">
                <p style="font-size:2rem; font-weight:bold; margin:0;"><?= $soThpt ?></p>
                <p style="margin:0; color:#555;">Cấp THPT</p>
            </div>
            <div style="flex:1; min-width:140px; background:#fce4ec; padding:1.2rem; border-radius:8px; text-align:center;">
                <p style="font-size:2rem; font-weight:bold; margin:0;"><?= $soMienPhi ?></p>
                <p style="margin:0; color:#555;">Miễn phí</p>
            </div>
            <div style="flex:1; min-width:140px; background:#ede7f6; padding:1.2rem; border-radius:8px; text-align:center;">
                <p style="font-size:2rem; font-weight:bold; margin:0;"><?= count($danhSachLienHe) ?></p>
                <p style="margin:0; color:#555;">Tin liên hệ</p>
            </div>
        </section>

        <!-- Bảng liên hệ -->
        <section>
            <h2>📬 Danh sách tin nhắn liên hệ</h2>

            <?php if (empty($danhSachLienHe)): ?>
                <p style="color:#888; font-style:italic;">Chưa có tin nhắn liên hệ nào.</p>
            <?php else: ?>
            <div class="khung-cuon-bang">
                <table class="bang-hoc-lieu" style="width:100%;">
                    <thead>
                        <tr>
                            <th class="bang-hoc-lieu__dau-cot">#</th>
                            <th class="bang-hoc-lieu__dau-cot">Họ tên</th>
                            <th class="bang-hoc-lieu__dau-cot">Email</th>
                            <th class="bang-hoc-lieu__dau-cot">Tin nhắn</th>
                            <th class="bang-hoc-lieu__dau-cot">Thời gian</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($danhSachLienHe as $i => $lh): ?>
                        <tr class="bang-hoc-lieu__hang">
                            <td class="bang-hoc-lieu__o"><?= $i + 1 ?></td>
                            <td class="bang-hoc-lieu__o"><?= e($lh['ho_ten'] ?? '') ?></td>
                            <td class="bang-hoc-lieu__o">
                                <a href="mailto:<?= e($lh['email'] ?? '') ?>"><?= e($lh['email'] ?? '') ?></a>
                            </td>
                            <td class="bang-hoc-lieu__o" style="max-width:300px; white-space:pre-wrap;"><?= e($lh['tin_nhan'] ?? '') ?></td>
                            <td class="bang-hoc-lieu__o" style="white-space:nowrap;"><?= e($lh['thoi_gian'] ?? '') ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </section>
    </main>

<?php require_once 'inc/footer.php'; ?>

    <script type="module" src="js/main.js"></script>
</body>
</html>
