<?php
/**
 * tai-khoan.php — Tài khoản admin (password đã băm bằng password_hash)
 *
 * Để tạo hash mới cho mật khẩu khác, chạy lệnh PHP:
 *   echo password_hash('MatKhauMoi', PASSWORD_DEFAULT);
 *
 * Tài khoản mặc định:
 *   username : admin
 *   password : Admin@123
 */
return [
    'admin' => [
        'ten_hien_thi' => 'Quản trị viên',
        // Hash của: Admin@123
        'mat_khau_bam' => '$2y$12$hAZVyUsPfUXoovH6GnwsTe0M2ZUZMqKEWj7PBilrIH/Q7mlSNGpWe',
    ],
];
