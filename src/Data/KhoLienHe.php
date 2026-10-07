<?php
/**
 * KhoLienHe.php — Đọc/Ghi dữ liệu liên hệ vào storage/lien-he.jsonl
 */
class KhoLienHe
{
    private string $file;

    public function __construct(string $file = '')
    {
        $this->file = $file ?: STORAGE_DIR . 'lien-he.jsonl';
    }

    /**
     * Lưu một submission liên hệ vào file .jsonl (mỗi dòng 1 JSON)
     */
    public function luu(array $data): bool
    {
        // Thêm timestamp
        $data['thoi_gian'] = date('Y-m-d H:i:s');
        $dong = json_encode($data, JSON_UNESCAPED_UNICODE) . "\n";

        // Tạo thư mục nếu chưa có
        $thu_muc = dirname($this->file);
        if (!is_dir($thu_muc)) {
            mkdir($thu_muc, 0755, true);
        }

        return file_put_contents($this->file, $dong, FILE_APPEND | LOCK_EX) !== false;
    }

    /**
     * Đọc toàn bộ danh sách liên hệ (dùng cho quản trị)
     * @return array[]
     */
    public function tatCa(): array
    {
        if (!file_exists($this->file)) return [];

        $ket_qua = [];
        $cac_dong = file($this->file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($cac_dong as $dong) {
            $obj = json_decode($dong, true);
            if (is_array($obj)) {
                $ket_qua[] = $obj;
            }
        }

        // Sắp xếp mới nhất trước
        return array_reverse($ket_qua);
    }
}
