import { layDanhSachYeuThich } from './yeu-thich.js';

/**
 * Tệp js/main.js
 * Chức năng: Xử lý Menu Mobile đóng/mở và Số đếm yêu thích toàn cục.
 * Đánh dấu HTML đang có JS để làm Graceful Degradation (Gợi ý 3).
 */

document.documentElement.classList.add('js');

document.addEventListener('DOMContentLoaded', () => {
    // === 1. CHỨC NĂNG MENU MOBILE ===
    const nutMenu = document.querySelector('.nut-menu');
    const menuChinh = document.querySelector('.menu');

    if (nutMenu && menuChinh) {
        nutMenu.addEventListener('click', () => {
            const dangMo = menuChinh.classList.contains('mo');
            if (dangMo) {
                menuChinh.classList.remove('mo');
                nutMenu.setAttribute('aria-expanded', 'false');
                nutMenu.textContent = '☰ Menu';
            } else {
                menuChinh.classList.add('mo');
                nutMenu.setAttribute('aria-expanded', 'true');
                nutMenu.textContent = '✕ Đóng';
            }
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && menuChinh.classList.contains('mo')) {
                menuChinh.classList.remove('mo');
                nutMenu.setAttribute('aria-expanded', 'false');
                nutMenu.textContent = '☰ Menu';
                nutMenu.focus();
            }
        });
    }

    // === 2. CHỨC NĂNG SỐ ĐẾM YÊU THÍCH TỔNG ===
    const theDemYeuThich = document.getElementById('dem-yeu-thich');
    
    function capNhatHienThiYeuThich() {
        if (theDemYeuThich) {
            const danhSach = layDanhSachYeuThich();
            theDemYeuThich.textContent = danhSach.length;
        }
    }

    // Khởi chạy ngay khi load trang
    capNhatHienThiYeuThich();

    // Lắng nghe sự kiện để cập nhật con số theo thời gian thực 
    // khi người dùng bấm nút Thêm/Xóa ở các trang khác
    window.addEventListener('capNhatYeuThich', capNhatHienThiYeuThich);
});
