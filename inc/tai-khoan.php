<?php
/**
 * tai-khoan.php — Danh sách tài khoản thử nghiệm
 * KHÔNG đưa mật khẩu thật vào đây.
 */

// Chạy dòng dưới đây 1 lần để lấy mã băm, sau đó dán vào mảng TAI_KHOAN:
// echo password_hash('nhom02@ltweb', PASSWORD_DEFAULT);

const TAI_KHOAN = [
    // Tên đăng nhập là 'admin', Mật khẩu là 'nhom02@ltweb'
    // Mã băm dưới đây được tạo sẵn bằng hàm password_hash()
    'admin' => '$2y$10$oCPFzO4aqMDgkDa.LxZrDOjORhQBa2heosWWdX7wZaOP0H6zar9Am'
];

