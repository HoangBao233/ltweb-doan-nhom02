<?php
/**
 * inc/don-hang.php — Nghiệp vụ giao dịch Đặt hàng (Transaction)
 * Đồ án Lập trình Web - Nhóm 02
 * Phụ trách: Nguyễn Tiến Thịnh (MSSV: 3120222126) & Lê Hoàng Bảo — Chức năng 5
 */

declare(strict_types=1);

require_once __DIR__ . '/db.php';

/**
 * Lưu đơn hàng mới trong một GIAO DỊCH (MySQL Transaction).
 * Đảm bảo tính toàn vẹn dữ liệu: Trừ tồn kho, lưu đơn hàng, lưu chi tiết đơn.
 * Nếu bất kỳ món nào không đủ tồn kho, toàn bộ giao dịch sẽ bị ROLLBACK.
 *
 * @param int $nguoiDungId ID người mua
 * @param array $gioHang Mảng giỏ hàng [giao_an_id => so_luong] hoặc danh sách ID
 * @param string $ghiChu Ghi chú thêm cho đơn hàng
 * @return int ID đơn hàng vừa tạo
 * @throws Exception Nếu giỏ rỗng, không đủ tồn kho, hoặc lỗi CSDL
 */
function luuDonHang(int $nguoiDungId, array $gioHang, string $ghiChu = ''): int {
    if (empty($gioHang)) {
        throw new Exception("Giỏ hàng của bạn đang trống. Vui lòng chọn giáo án trước khi đặt.");
    }

    // Chuẩn hóa giỏ hàng thành mảng [id => so_luong]
    $matHang = [];
    foreach ($gioHang as $k => $v) {
        if (is_int($k) && $k > 0 && is_numeric($v) && (int)$v > 0) {
            $matHang[$k] = (int)$v;
        } elseif (is_numeric($v) && (int)$v > 0) {
            $matHang[(int)$v] = 1;
        }
    }

    if (empty($matHang)) {
        throw new Exception("Dữ liệu giỏ hàng không hợp lệ.");
    }

    $db = db();

    // Bắt đầu giao dịch Transaction
    $db->beginTransaction();

    try {
        $ids = array_keys($matHang);
        $placeholders = implode(',', array_fill(0, count($ids), '?'));

        // 1. Đọc dữ liệu giá và tồn kho thực tế từ CSDL MySQL (chống can thiệp giá từ client)
        $st = $db->prepare("SELECT id, ten, gia, ton_kho FROM giao_an WHERE id IN ($placeholders)");
        $st->execute($ids);
        $dsGiaoAn = $st->fetchAll();

        if (count($dsGiaoAn) !== count($ids)) {
            throw new Exception("Một số giáo án trong giỏ không còn tồn tại trên hệ thống.");
        }

        $mapGiaoAn = [];
        foreach ($dsGiaoAn as $ga) {
            $mapGiaoAn[(int)$ga['id']] = $ga;
        }

        // 2. Tính tổng tiền và kiểm tra sơ bộ tồn kho
        $tongTien = 0;
        foreach ($matHang as $id => $sl) {
            $ga = $mapGiaoAn[$id];
            if ($ga['ton_kho'] < $sl) {
                throw new Exception("Giáo án '{$ga['ten']}' không đủ số lượng tồn kho (hiện chỉ còn {$ga['ton_kho']} bản).");
            }
            $tongTien += (int)$ga['gia'] * $sl;
        }

        // 3. Tạo bản ghi đơn hàng mới vào bảng don_hang
        $stDon = $db->prepare("
            INSERT INTO don_hang (nguoi_dung_id, tong_tien, trang_thai, ghi_chu)
            VALUES (?, ?, 'cho_duyet', ?)
        ");
        $ghiChuChuan = $ghiChu !== '' ? mb_substr(trim($ghiChu), 0, 500) : null;
        $stDon->execute([$nguoiDungId, $tongTien, $ghiChuChuan]);
        $donHangId = (int)$db->lastInsertId();

        // 4. Trừ tồn kho và lưu chi tiết từng giáo án vào chi_tiet_don (dùng tham số vị trí ? để tương thích MySQL Native Prepared Statements khi EMULATE_PREPARES false)
        $stTruKho = $db->prepare("
            UPDATE giao_an 
            SET ton_kho = ton_kho - ? 
            WHERE id = ? AND ton_kho >= ?
        ");

        $stChiTiet = $db->prepare("
            INSERT INTO chi_tiet_don (don_hang_id, giao_an_id, so_luong, don_gia)
            VALUES (?, ?, ?, ?)
        ");

        foreach ($matHang as $id => $sl) {
            $ga = $mapGiaoAn[$id];

            // Trừ tồn kho có điều kiện WHERE ton_kho >= ? (chống Race condition khi 2 người mua cùng lúc - Ca 10)
            $stTruKho->execute([$sl, $id, $sl]);
            if ($stTruKho->rowCount() === 0) {
                // Không cập nhật được dòng nào -> chứng tỏ đã bị người khác mua trước và hết hàng
                throw new Exception("Giáo án '{$ga['ten']}' vừa hết hàng. Giao dịch đã được hủy.");
            }

            // Lưu chi_tiet_don với đơn giá tại thời điểm đặt (phi chuẩn hóa có chủ đích để bảo toàn hóa đơn lịch sử)
            $stChiTiet->execute([$donHangId, $id, $sl, (int)$ga['gia']]);
        }

        // Xác nhận giao dịch thành công
        $db->commit();

        return $donHangId;
    } catch (Throwable $e) {
        // Rollback hoàn tác toàn bộ thay đổi nếu có bất kỳ lỗi nào xảy ra
        if ($db->inTransaction()) {
            $db->rollBack();
        }
        throw $e;
    }
}

