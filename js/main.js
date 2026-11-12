import { layDanhSachYeuThich } from './yeu-thich.js';

/**
 * Tệp js/main.js
 * Chức năng: Xử lý Menu Mobile đóng/mở và Số đếm yêu thích toàn cục.
 * Đánh dấu HTML đang có JS để làm Graceful Degradation (Gợi ý 3).
 */

document.documentElement.classList.add('js');

function khoiTaoMain() {
    // === 1. CHỨC NĂNG MENU MOBILE ===
    window.batTatMenuMobile = function() {
        const menu = document.getElementById('menu-chinh');
        const nut = document.querySelector('.nut-menu');
        if (!menu || !nut) return;
        const dangMo = menu.classList.toggle('mo');
        nut.setAttribute('aria-expanded', dangMo ? 'true' : 'false');
        nut.textContent = dangMo ? '✕ Đóng' : '☰ Menu';
    };

    const nutMenu = document.querySelector('.nut-menu');
    if (nutMenu) {
        nutMenu.onclick = window.batTatMenuMobile;
    }

    document.addEventListener('keydown', (e) => {
        const menuChinh = document.getElementById('menu-chinh');
        const nut = document.querySelector('.nut-menu');
        if (e.key === 'Escape' && menuChinh && menuChinh.classList.contains('mo')) {
            menuChinh.classList.remove('mo');
            if (nut) {
                nut.setAttribute('aria-expanded', 'false');
                nut.textContent = '☰ Menu';
                nut.focus();
            }
        }
    });

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
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', khoiTaoMain);
} else {
    khoiTaoMain();
}
