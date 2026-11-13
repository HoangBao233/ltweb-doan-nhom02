<?php
/**
 * sua-giao-an.php — Chỉnh sửa thông tin giáo án trong MySQL
 * 
 * Phụ trách: Lê Thị Xuân Châu (MSSV: 3120124006) — Phần A, Chức năng 3
 * Kỹ thuật: yeuCauQuyen('quan_tri'), kiemTraGiaoAn(), giữ ảnh cũ nếu không tải mới, PRG + Flash
 * Bảo mật BTN7: Thêm mã CSRF csrfField(), làm sạch chuỗi chuoi(), loại bỏ 100% inline styles
 */

require_once __DIR__ . '/inc/config.php';
require_once __DIR__ . '/inc/kiem-tra.php';
if (file_exists(__DIR__ . '/inc/csrf.php')) {
    require_once __DIR__ . '/inc/csrf.php';
}

if (!function_exists('chuoi')) {
    function chuoi(mixed $v): string {
        return is_string($v) ? $v : '';
    }
}

if (!function_exists('csrfField')) {
    function csrfField(): string {
        return '<input type="hidden" name="csrf" value="' . (function_exists('csrfToken') ? csrfToken() : '') . '">';
    }
}

// 1. Phân quyền: Chỉ quản trị viên mới được sửa giáo án
yeuCauQuyen('quan_tri');

// 2. Đọc ID từ URL và lấy dữ liệu cũ từ MySQL
$id = filter_var($_GET['id'] ?? '', FILTER_VALIDATE_INT);
if (!$id) {
    http_response_code(404);
    $tep404 = __DIR__ . '/404.php';
    if (file_exists($tep404)) {
        require $tep404;
    } else {
        echo '404 - Không tìm thấy giáo án yêu cầu.';
    }
    exit;
}

$st = db()->prepare('SELECT * FROM giao_an WHERE id = ?');
$st->execute([$id]);
$cu = $st->fetch();

if (!$cu) {
    http_response_code(404);
    $tep404 = __DIR__ . '/404.php';
    if (file_exists($tep404)) {
        require $tep404;
    } else {
        echo '404 - Không tìm thấy giáo án với mã số này.';
    }
    exit;
}

// 3. Lấy danh sách danh mục để đổ vào thẻ <select>
$dsDanhMuc = db()->query('SELECT id, ten FROM danh_muc ORDER BY ten')->fetchAll();
$idDanhMuc = array_map('intval', array_column($dsDanhMuc, 'id'));

$loi = [];

// 4. Xử lý khi gửi dữ liệu POST cập nhật
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    [$du, $loi] = kiemTraGiaoAn($_POST, $idDanhMuc);

    // Mặc định giữ nguyên ảnh cũ
    $tenHinhAnh = $cu['hinh_anh'];

    // Nếu người dùng tải ảnh mới lên thay thế
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
                $tenFileMoi = 'ga_' . bin2hex(random_bytes(8)) . '.' . $ext;

                $uploadDir = UPLOADS_DIR;
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                if (move_uploaded_file($_FILES['hinh_anh']['tmp_name'], $uploadDir . $tenFileMoi)) {
                    $tenHinhAnh = 'uploads/' . $tenFileMoi;
                } else {
                    $loi['hinh_anh'] = 'Không thể lưu tệp ảnh tải lên.';
                }
            }
        }
    }

    // Nếu không có lỗi, thực hiện UPDATE vào MySQL
    if (empty($loi)) {
        $sql = "UPDATE giao_an 
                SET ten = :ten,
                    danh_muc_id = :danh_muc_id,
                    cap_hoc = :cap_hoc,
                    gia = :gia,
                    ton_kho = :ton_kho,
                    hinh_anh = :hinh_anh,
                    video_url = :video_url,
                    mo_ta = :mo_ta
                WHERE id = :id";
        
        $stUpdate = db()->prepare($sql);
        $stUpdate->execute([
            ':ten'         => $du['ten'],
            ':danh_muc_id' => $du['danh_muc_id'],
            ':cap_hoc'     => $du['cap_hoc'],
            ':gia'         => $du['gia'],
            ':ton_kho'     => $du['ton_kho'],
            ':hinh_anh'    => $tenHinhAnh,
            ':video_url'   => $du['video_url'] !== '' ? $du['video_url'] : null,
            ':mo_ta'       => $du['mo_ta'] !== '' ? $du['mo_ta'] : null,
            ':id'          => $id,
        ]);

        $_SESSION['flash'] = "Đã cập nhật thành công giáo án #$id ({$du['ten']})!";
        header('Location: quan-tri.php'); // PRG
        exit;
    }
}

// Nếu vừa submit lỗi thì lấy dữ liệu từ $_POST, nếu mới mở form thì lấy dữ liệu $cu
$f = $_SERVER['REQUEST_METHOD'] === 'POST' ? array_merge($cu, [
    'ten'         => chuoi($_POST['ten'] ?? ''),
    'danh_muc_id' => $_POST['danh_muc_id'] ?? '',
    'cap_hoc'     => chuoi($_POST['cap_hoc'] ?? 'THCS'),
    'gia'         => $_POST['gia'] ?? '0',
    'ton_kho'     => $_POST['ton_kho'] ?? '50',
    'video_url'   => chuoi($_POST['video_url'] ?? ''),
    'mo_ta'       => chuoi($_POST['mo_ta'] ?? ''),
]) : $cu;

$tieuDe = 'Sửa giáo án #' . $id . ' | ITeduShare';
$trang  = 'quan-tri';
require __DIR__ . '/inc/header.php';
?>

<link rel="stylesheet" href="css/quan-tri.css">

<main class="trang__chinh khung-form-quan-tri">
    <div class="nhom-nut-form">
        <a href="quan-tri.php" class="link-quay-lai-qt">
            ← Quay lại Trang Quản trị
        </a>
    </div>

    <section class="phan-quan-tri">
        <h1 class="tieu-de-trang-qt">
            Chỉnh sửa Giáo án #<?= (int)$id ?>
        </h1>
        <p class="dem-tong-so">Cập nhật thông tin chi tiết của học liệu trong cơ sở dữ liệu.</p>

        <?php if (!empty($loi)): ?>
            <div class="flash-loi">
                <strong>Vui lòng kiểm tra lại các mục báo lỗi bên dưới:</strong>
            </div>
        <?php endif; ?>

        <form method="POST" action="sua-giao-an.php?id=<?= (int)$id ?>" enctype="multipart/form-data" novalidate class="form-them-ga">
            <?= csrfField() ?>

            <!-- Tên giáo án -->
            <div>
                <label for="ten" class="nhan-form-ga">
                    Tên giáo án / Học liệu <span class="dau-sao-bat-buoc">*</span>
                </label>
                <input type="text" id="ten" name="ten" value="<?= e($f['ten'] ?? '') ?>" required
                       class="input-form-ga <?= isset($loi['ten']) ? 'input-form-loi' : '' ?>">
                <?php if (isset($loi['ten'])): ?>
                    <p class="thong-bao-loi-field"><?= e($loi['ten']) ?></p>
                <?php endif; ?>
            </div>

            <!-- Danh mục & Cấp học -->
            <div class="luoi-hai-cot-form">
                <div>
                    <label for="danh_muc_id" class="nhan-form-ga">
                        Danh mục học liệu <span class="dau-sao-bat-buoc">*</span>
                    </label>
                    <select id="danh_muc_id" name="danh_muc_id" required
                            class="select-form-ga <?= isset($loi['danh_muc_id']) ? 'input-form-loi' : '' ?>">
                        <option value="">-- Chọn danh mục --</option>
                        <?php foreach ($dsDanhMuc as $dm): ?>
                            <option value="<?= (int)$dm['id'] ?>" <?= (string)($f['danh_muc_id'] ?? '') === (string)$dm['id'] ? 'selected' : '' ?>>
                                <?= e($dm['ten']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (isset($loi['danh_muc_id'])): ?>
                        <p class="thong-bao-loi-field"><?= e($loi['danh_muc_id']) ?></p>
                    <?php endif; ?>
                </div>

                <div>
                    <label for="cap_hoc" class="nhan-form-ga">
                        Cấp học <span class="dau-sao-bat-buoc">*</span>
                    </label>
                    <select id="cap_hoc" name="cap_hoc" required
                            class="select-form-ga <?= isset($loi['cap_hoc']) ? 'input-form-loi' : '' ?>">
                        <option value="">-- Chọn cấp học --</option>
                        <option value="THCS" <?= ($f['cap_hoc'] ?? '') === 'THCS' ? 'selected' : '' ?>>Trung học cơ sở (THCS)</option>
                        <option value="THPT" <?= ($f['cap_hoc'] ?? '') === 'THPT' ? 'selected' : '' ?>>Trung học phổ thông (THPT)</option>
                        <option value="Tiểu học" <?= ($f['cap_hoc'] ?? '') === 'Tiểu học' ? 'selected' : '' ?>>Tiểu học</option>
                    </select>
                    <?php if (isset($loi['cap_hoc'])): ?>
                        <p class="thong-bao-loi-field"><?= e($loi['cap_hoc']) ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Giá & Tồn kho -->
            <div class="luoi-hai-cot-form">
                <div>
                    <label for="gia" class="nhan-form-ga">
                        Đơn giá (VNĐ) <span class="dau-sao-bat-buoc">*</span>
                    </label>
                    <input type="number" id="gia" name="gia" value="<?= e($f['gia'] ?? 0) ?>" min="0" step="1000" required
                           class="input-form-ga <?= isset($loi['gia']) ? 'input-form-loi' : '' ?>">
                    <small class="chu-thich-nho">Nhập 0 nếu miễn phí</small>
                    <?php if (isset($loi['gia'])): ?>
                        <p class="thong-bao-loi-field"><?= e($loi['gia']) ?></p>
                    <?php endif; ?>
                </div>

                <div>
                    <label for="ton_kho" class="nhan-form-ga">
                        Số lượt cung cấp (Tồn kho) <span class="dau-sao-bat-buoc">*</span>
                    </label>
                    <input type="number" id="ton_kho" name="ton_kho" value="<?= e($f['ton_kho'] ?? 0) ?>" min="0" max="9999" required
                           class="input-form-ga <?= isset($loi['ton_kho']) ? 'input-form-loi' : '' ?>">
                    <?php if (isset($loi['ton_kho'])): ?>
                        <p class="thong-bao-loi-field"><?= e($loi['ton_kho']) ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Ảnh bìa (hiển thị ảnh cũ nếu có) -->
            <div>
                <label for="hinh_anh" class="nhan-form-ga">
                    Ảnh minh họa giáo án
                </label>
                <?php if (!empty($cu['hinh_anh'])): ?>
                    <div class="anh-hien-tai-box">
                        <img src="<?= e(anhGiaoAn($cu['hinh_anh'])) ?>" alt="Ảnh hiện tại" 
                             class="anh-hien-tai-thumb">
                        <span class="chu-thich-nho">Ảnh hiện tại. Chọn tệp bên dưới nếu muốn thay ảnh khác.</span>
                    </div>
                <?php endif; ?>
                <input type="file" id="hinh_anh" name="hinh_anh" accept="image/jpeg,image/png,image/webp"
                       class="input-tep-tin <?= isset($loi['hinh_anh']) ? 'input-tep-tin-loi' : '' ?>">
                <small class="chu-thich-nho">Định dạng: JPG, PNG, WEBP (Tối đa 2MB). Bỏ trống để giữ ảnh cũ.</small>
                <?php if (isset($loi['hinh_anh'])): ?>
                    <p class="thong-bao-loi-field"><?= e($loi['hinh_anh']) ?></p>
                <?php endif; ?>
            </div>

            <!-- Video URL -->
            <div>
                <label for="video_url" class="nhan-form-ga">
                    Đường dẫn Video giảng mẫu (Youtube / Drive)
                </label>
                <input type="url" id="video_url" name="video_url" value="<?= e($f['video_url'] ?? '') ?>"
                       placeholder="https://www.youtube.com/watch?v=..."
                       class="input-form-ga">
            </div>

            <!-- Mô tả -->
            <div>
                <label for="mo_ta" class="nhan-form-ga">
                    Mô tả nội dung &amp; Mục tiêu bài học
                </label>
                <textarea id="mo_ta" name="mo_ta" rows="5"
                          class="textarea-form-ga <?= isset($loi['mo_ta']) ? 'input-form-loi' : '' ?>"><?= e($f['mo_ta'] ?? '') ?></textarea>
                <?php if (isset($loi['mo_ta'])): ?>
                    <p class="thong-bao-loi-field"><?= e($loi['mo_ta']) ?></p>
                <?php endif; ?>
            </div>

            <!-- Nút gửi -->
            <div class="nhom-nut-form">
                <button type="submit" class="nut-luu-form">
                    Lưu Thay đổi
                </button>
                <a href="quan-tri.php" class="nut-huy-form">
                    Hủy bỏ
                </a>
            </div>
        </form>
    </section>
</main>

<?php require __DIR__ . '/inc/footer.php'; ?>
</body>
</html>
