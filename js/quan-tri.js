/**
 * js/quan-tri.js — Xử lý tương tác trang quản trị (chống CSP, loại bỏ inline onsubmit)
 * Phụ trách: Lê Thị Xuân Châu (MSSV: 3120124006)
 */
document.addEventListener('DOMContentLoaded', () => {
    document.addEventListener('submit', (e) => {
        const form = e.target;
        if (form && form.matches('form')) {
            const msg = form.getAttribute('data-confirm');
            if (msg && !window.confirm(msg)) {
                e.preventDefault();
            }
        }
    });
});
