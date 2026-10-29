<?php
/**
 * inc/kiem-tra.php — Kiểm tra dữ liệu giáo án cho cả trang thêm và sửa
 * 
 * Phụ trách: Lê Thị Xuân Châu (MSSV: 3120124006) — Phần A, Chức năng 3
 * Kỹ thuật: Whitelist, filter_var, trả về [$du, $loi] theo chuẩn Gợi ý 4
 */

function kiemTraGiaoAn(array $vao, array $idDanhMuc): array
{
    $du = [
        'ten'         => trim($vao['ten'] ?? ''),
        'danh_muc_id' => filter_var($vao['danh_muc_id'] ?? '', FILTER_VALIDATE_INT),
        'cap_hoc'     => trim($vao['cap_hoc'] ?? 'THCS'),
        'gia'         => filter_var($vao['gia'] ?? '', FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]]),
        'ton_kho'     => filter_var($vao['ton_kho'] ?? '0', FILTER_VALIDATE_INT, ['options' => ['min_range' => 0, 'max_range' => 9999]]),
        'video_url'   => trim($vao['video_url'] ?? ''),
        'mo_ta'       => trim($vao['mo_ta'] ?? ''),
    ];

    $loi = [];

    $n = mb_strlen($du['ten'], 'UTF-8');
    if ($n < 2 || $n > 200) {
        $loi['ten'] = 'Tên giáo án phải có độ dài từ 2 đến 200 ký tự.';
    }

    if ($du['danh_muc_id'] === false || !in_array($du['danh_muc_id'], $idDanhMuc, true)) {
        $loi['danh_muc_id'] = 'Vui lòng chọn một danh mục học liệu hợp lệ.';
    }

    $capHocHopLe = ['THCS', 'THPT', 'Tiểu học'];
    if (!in_array($du['cap_hoc'], $capHocHopLe, true)) {
        $loi['cap_hoc'] = 'Vui lòng chọn cấp học hợp lệ (THCS, THPT hoặc Tiểu học).';
    }

    if ($du['gia'] === false) {
        $loi['gia'] = 'Giá tài liệu phải là số nguyên không âm (từ 0 đ).';
    }

    if ($du['ton_kho'] === false) {
        $loi['ton_kho'] = 'Số lượng tồn kho phải là số nguyên từ 0 đến 9999.';
    }

    if (mb_strlen($du['mo_ta'], 'UTF-8') > 3000) {
        $loi['mo_ta'] = 'Mô tả giáo án không được vượt quá 3000 ký tự.';
    }

    return [$du, $loi];
}

