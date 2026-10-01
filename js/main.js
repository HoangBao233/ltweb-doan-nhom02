/**
 * Tệp js/main.js
 * Chức năng: Xử lý Menu Mobile đóng/mở và các chức năng dùng chung cho toàn website.
 * Đánh dấu HTML đang có JS để làm Graceful Degradation (Gợi ý 3).
 */

// Đánh dấu HTML đang chạy JS để áp dụng CSS ẩn Menu trên Mobile
document.documentElement.classList.add('js');

document.addEventListener('DOMContentLoaded', () => {
    const nutMenu = document.querySelector('.nut-menu');
    const menuChinh = document.querySelector('.menu');

    if (nutMenu && menuChinh) {
        // Toggle mở/đóng menu khi bấm nút
        nutMenu.addEventListener('click', () => {
            const dangMo = menuChinh.classList.contains('mo');
            
            if (dangMo) {
                menuChinh.classList.remove('mo');
                nutMenu.setAttribute('aria-expanded', 'false');
            } else {
                menuChinh.classList.add('mo');
                nutMenu.setAttribute('aria-expanded', 'true');
            }
        });

        // Bấm Esc để đóng menu theo chuẩn A11y
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && menuChinh.classList.contains('mo')) {
                menuChinh.classList.remove('mo');
                nutMenu.setAttribute('aria-expanded', 'false');
                nutMenu.focus(); // Trả lại focus cho nút menu
            }
        });
    }
});