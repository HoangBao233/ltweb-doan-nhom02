<?php
/**
 * them-giao-an.php — Thêm giáo án mới vào cơ sở dữ liệu MySQL
 * 
 * Phụ trách: Lê Thị Xuân Châu (MSSV: 3120124006) — Phần A, Chức năng 3
 * Kỹ thuật: yeuCauQuyen('quan_tri'), kiemTraGiaoAn(), upload ảnh bằng finfo, PRG + Flash
 */

require_once __DIR__ . '/inc/config.php';
require_once __DIR__ . '/inc/kiem-tra.php';

// 1. Phân quyền: Chỉ quản trị viên mới được thêm giáo án
yeuCauQuyen('quan_tri');

// 2. Lấy danh sách danh mục để đổ vào thẻ <select>
$dsDanhMuc = db()->query('SELECT id, ten FROM danh_muc ORDER BY ten')->fetchAll();
$idDanhMuc = array_map('intval', array_column($dsDanhMuc, 'id'));

$loi = [];
$f = [
    'ten'         => '',
    'danh_muc_id' => '',
    'cap_hoc'     => 'THCS',
    'gia'         => '0',
    'ton_kho'     => '50',
    'video_url'   => '',
    'mo_ta'       => '',
];

// 3. Xử lý khi gửi dữ liệu POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $f = array_merge($f, $_POST);

    // Kiểm tra tính hợp lệ qua hàm dùng chung
    [$du, $loi] = kiemTraGiaoAn($_POST, $idDanhMuc);

    // Xử lý upload ảnh bìa
    $tenHinhAnh = null;
    if (isset($_FILES['hinh_anh']) && $_FILES['hinh_anh']['error'] !== UPLOAD_ERR_NO_FILE) {
        if ($_FILES['hinh_anh']['error'] !== UPLOAD_ERR_OK) {
            $loi['hinh_anh'] = 'Lỗi khi tải tệp ảnh lên máy chủ (Mã: ' . $_FILES['hinh_anh']['error'] . ').';
        } elseif ($_FILES['hinh_anh']['size'] > 2 * 1024 * 1024) { // ≤ 2MB
            $loi['hinh_anh'] = 'Dung lượng tệp ảnh không được vượt quá 2MB.';
        } else {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $_FILES['hinh_anh']['tmp_name']);
            finfo_close($finfo);

            $choPhepMime = [
                'image/jpeg' => 'jpg',
                'image/png'  => 'png',
                'image/webp' => 'webp',
            ];

            if (!array_key_exists($mime, $choPhepMime)) {
                $loi['hinh_anh'] = 'Định dạng ảnh không hợp lệ (chỉ chấp nhận JPG, PNG, WEBP).';
            } else {
                $ext = $choPhepMime[$mime];
                $tenFileMoi = 'ga_' . uniqid('', true) . '.' . $ext;

                $uploadDir = UPLOADS_DIR;
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                if (move_uploaded_file($_FILES['hinh_anh']['tmp_name'], $uploadDir . $tenFileMoi)) {
                    $tenHinhAnh = $tenFileMoi;
                } else {
                    $loi['hinh_anh'] = 'Không thể lưu tệp ảnh tải lên.';
                }
            }
        }
    }

    // Nếu không có lỗi, thực hiện INSERT vào MySQL
    if (empty($loi)) {
        $sql = "INSERT INTO giao_an (ten, danh_muc_id, cap_hoc, gia, ton_kho, hinh_anh, video_url, mo_ta)
                VALUES (:ten, :danh_muc_id, :cap_hoc, :gia, :ton_kho, :hinh_anh, :video_url, :mo_ta)";
        
        $st = db()->prepare($sql);
        $st->execute([
            ':ten'         => $du['ten'],
            ':danh_muc_id' => $du['danh_muc_id'],
            ':cap_hoc'     => $du['cap_hoc'],
            ':gia'         => $du['gia'],
            ':ton_kho'     => $du['ton_kho'],
            ':hinh_anh'    => $tenHinhAnh,
            ':video_url'   => $du['video_url'] !== '' ? $du['video_url'] : null,
            ':mo_ta'       => $du['mo_ta'] !== '' ? $du['mo_ta'] : null,
        ]);

        $idMoi = db()->lastInsertId();
        $_SESSION['flash'] = "Đã thêm thành công giáo án: {$du['ten']} (Mã ID: #$idMoi)!";
        header('Location: quan-tri.php'); // Áp dụng PRG
        exit;
    }
}

$tieuDe = 'Thêm giáo án mới | ITeduShare';
$trang  = 'quan-tri';
require __DIR__ . '/inc/header.php';
?>

<main class="trang__chinh" style="max-width: 800px; margin: 0 auto; padding: 1.5rem 1rem;">
    <div style="margin-bottom: 1.5rem;">
        <a href="quan-tri.php" style="color: #0284c7; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 0.35rem;">
            ← Quay lại Trang Quản trị
        </a>
    </div>

    <section style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 2rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
        <h1 style="margin: 0 0 0.5rem; font-size: 1.5rem; color: var(--mau-chinh, #004085);">Thêm Giáo án Mới</h1>
        <p style="margin: 0 0 1.5rem; color: #64748b; font-size: 0.95rem;">Điền đầy đủ thông tin bên dưới để đưa tài liệu lên hệ thống.</p>

        <?php if (!empty($loi)): ?>
            <div style="padding: 0.85rem 1.25rem; margin-bottom: 1.5rem; background: #fff1f2; border: 1px solid #fecdd3; border-left: 4px solid #f43f5e; color: #9f1239; border-radius: 6px;">
                <strong>Vui lòng kiểm tra lại các mục báo lỗi bên dưới:</strong>
            </div>
        <?php endif; ?>

        <form method="POST" action="them-giao-an.php" enctype="multipart/form-data" novalidate style="display: flex; flex-direction: column; gap: 1.25rem;">
            <!-- Tên giáo án -->
            <div>
                <label for="ten" style="display: block; font-weight: 600; margin-bottom: 0.35rem; color: #1e293b;">
                    Tên giáo án / Học liệu <span style="color: #ef4444;">*</span>
                </label>
                <input type="text" id="ten" name="ten" value="<?= e($f['ten']) ?>" required
                       placeholder="Ví dụ: Kế hoạch bài dạy Tin học 7 - Chủ đề E: Soạn thảo văn bản"
                       style="width: 100%; padding: 0.65rem 0.85rem; border: 1px solid <?= isset($loi['ten']) ? '#ef4444' : '#cbd5e1' ?>; border-radius: 6px; font-size: 0.95rem; box-sizing: border-box;">
                <?php if (isset($loi['ten'])): ?>
                    <p style="margin: 0.3rem 0 0; color: #ef4444; font-size: 0.85rem;"><?= e($loi['ten']) ?></p>
                <?php endif; ?>
            </div>

            <!-- Danh mục & Cấp học (2 cột) -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1rem;">
                <div>
                    <label for="danh_muc_id" style="display: block; font-weight: 600; margin-bottom: 0.35rem; color: #1e293b;">
                        Danh mục học liệu <span style="color: #ef4444;">*</span>
                    </label>
                    <select id="danh_muc_id" name="danh_muc_id" required
                            style="width: 100%; padding: 0.65rem 0.85rem; border: 1px solid <?= isset($loi['danh_muc_id']) ? '#ef4444' : '#cbd5e1' ?>; border-radius: 6px; font-size: 0.95rem; box-sizing: border-box; background: #fff;">
                        <option value="">-- Chọn danh mục --</option>
                        <?php foreach ($dsDanhMuc as $dm): ?>
                            <option value="<?= (int)$dm['id'] ?>" <?= (string)$f['danh_muc_id'] === (string)$dm['id'] ? 'selected' : '' ?>>
                                <?= e($dm['ten']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (isset($loi['danh_muc_id'])): ?>
                        <p style="margin: 0.3rem 0 0; color: #ef4444; font-size: 0.85rem;"><?= e($loi['danh_muc_id']) ?></p>
                    <?php endif; ?>
                </div>

                <div>
                    <label for="cap_hoc" style="display: block; font-weight: 600; margin-bottom: 0.35rem; color: #1e293b;">
                        Cấp học <span style="color: #ef4444;">*</span>
                    </label>
                    <select id="cap_hoc" name="cap_hoc" required
                            style="width: 100%; padding: 0.65rem 0.85rem; border: 1px solid <?= isset($loi['cap_hoc']) ? '#ef4444' : '#cbd5e1' ?>; border-radius: 6px; font-size: 0.95rem; box-sizing: border-box; background: #fff;">
                        <option value="THCS" <?= $f['cap_hoc'] === 'THCS' ? 'selected' : '' ?>>Trung học cơ sở (THCS)</option>
                        <option value="THPT" <?= $f['cap_hoc'] === 'THPT' ? 'selected' : '' ?>>Trung học phổ thông (THPT)</option>
                        <option value="Tiểu học" <?= $f['cap_hoc'] === 'Tiểu học' ? 'selected' : '' ?>>Tiểu học</option>
                    </select>
                    <?php if (isset($loi['cap_hoc'])): ?>
                        <p style="margin: 0.3rem 0 0; color: #ef4444; font-size: 0.85rem;"><?= e($loi['cap_hoc']) ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Giá & Tồn kho (2 cột) -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1rem;">
                <div>
                    <label for="gia" style="display: block; font-weight: 600; margin-bottom: 0.35rem; color: #1e293b;">
                        Đơn giá (VNĐ) <span style="color: #ef4444;">*</span>
                    </label>
                    <input type="number" id="gia" name="gia" value="<?= e($f['gia']) ?>" min="0" step="1000" required
                           placeholder="0 (nếu chia sẻ miễn phí)"
                           style="width: 100%; padding: 0.65rem 0.85rem; border: 1px solid <?= isset($loi['gia']) ? '#ef4444' : '#cbd5e1' ?>; border-radius: 6px; font-size: 0.95rem; box-sizing: border-box;">
                    <small style="color: #64748b; font-size: 0.8rem;">Nhập 0 để chia sẻ miễn phí</small>
                    <?php if (isset($loi['gia'])): ?>
                        <p style="margin: 0.3rem 0 0; color: #ef4444; font-size: 0.85rem;"><?= e($loi['gia']) ?></p>
                    <?php endif; ?>
                </div>

                <div>
                    <label for="ton_kho" style="display: block; font-weight: 600; margin-bottom: 0.35rem; color: #1e293b;">
                        Số lượt cung cấp (Tồn kho) <span style="color: #ef4444;">*</span>
                    </label>
                    <input type="number" id="ton_kho" name="ton_kho" value="<?= e($f['ton_kho']) ?>" min="0" max="9999" required
                           style="width: 100%; padding: 0.65rem 0.85rem; border: 1px solid <?= isset($loi['ton_kho']) ? '#ef4444' : '#cbd5e1' ?>; border-radius: 6px; font-size: 0.95rem; box-sizing: border-box;">
                    <?php if (isset($loi['ton_kho'])): ?>
                        <p style="margin: 0.3rem 0 0; color: #ef4444; font-size: 0.85rem;"><?= e($loi['ton_kho']) ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Ảnh bìa -->
            <div>
                <label for="hinh_anh" style="display: block; font-weight: 600; margin-bottom: 0.35rem; color: #1e293b;">
                    Ảnh minh họa giáo án
                </label>
                <input type="file" id="hinh_anh" name="hinh_anh" accept="image/jpeg,image/png,image/webp"
                       style="width: 100%; padding: 0.5rem; border: 1px dashed <?= isset($loi['hinh_anh']) ? '#ef4444' : '#cbd5e1' ?>; border-radius: 6px; font-size: 0.9rem; box-sizing: border-box; background: #f8fafc;">
                <small style="color: #64748b; font-size: 0.8rem;">Định dạng: JPG, PNG, WEBP (Tối đa 2MB).</small>
                <?php if (isset($loi['hinh_anh'])): ?>
                    <p style="margin: 0.3rem 0 0; color: #ef4444; font-size: 0.85rem;"><?= e($loi['hinh_anh']) ?></p>
                <?php endif; ?>
            </div>

            <!-- Video URL -->
            <div>
                <label for="video_url" style="display: block; font-weight: 600; margin-bottom: 0.35rem; color: #1e293b;">
                    Đường dẫn Video giảng mẫu (Youtube / Drive)
                </label>
                <input type="url" id="video_url" name="video_url" value="<?= e($f['video_url']) ?>"
                       placeholder="https://www.youtube.com/watch?v=..."
                       style="width: 100%; padding: 0.65rem 0.85rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.95rem; box-sizing: border-box;">
            </div>

            <!-- Mô tả -->
            <div>
                <label for="mo_ta" style="display: block; font-weight: 600; margin-bottom: 0.35rem; color: #1e293b;">
                    Mô tả nội dung &amp; Mục tiêu bài học
                </label>
                <textarea id="mo_ta" name="mo_ta" rows="5"
                          placeholder="Tóm tắt nội dung chính, tiến trình bài giảng, phẩm chất và năng lực hình thành..."
                          style="width: 100%; padding: 0.65rem 0.85rem; border: 1px solid <?= isset($loi['mo_ta']) ? '#ef4444' : '#cbd5e1' ?>; border-radius: 6px; font-size: 0.95rem; box-sizing: border-box; resize: vertical;"><?= e($f['mo_ta']) ?></textarea>
                <?php if (isset($loi['mo_ta'])): ?>
                    <p style="margin: 0.3rem 0 0; color: #ef4444; font-size: 0.85rem;"><?= e($loi['mo_ta']) ?></p>
                <?php endif; ?>
            </div>

            <!-- Nút gửi -->
            <div style="display: flex; gap: 0.75rem; margin-top: 0.5rem;">
                <button type="submit" 
                        style="cursor: pointer; background: #0284c7; color: #fff; padding: 0.75rem 1.75rem; border: none; border-radius: 6px; font-weight: 600; font-size: 1rem;">
                    Lưu Giáo án
                </button>
                <a href="quan-tri.php" 
                   style="display: inline-flex; align-items: center; padding: 0.75rem 1.25rem; border: 1px solid #cbd5e1; background: #fff; color: #475569; border-radius: 6px; text-decoration: none; font-weight: 500;">
                    Hủy bỏ
                </a>
            </div>
        </form>
    </section>
</main>

<?php require __DIR__ . '/inc/footer.php'; ?>
</body>
</html>

