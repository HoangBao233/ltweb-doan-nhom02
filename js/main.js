// js/main.js
document.addEventListener('DOMContentLoaded', () => {
    const nutMenu = document.querySelector('.nut-menu');
    const menuChinh = document.getElementById('menu-chinh');

    // 1. Chức năng mở/đóng Menu thu gọn (A11y chuẩn)
    if (nutMenu && menuChinh) {
        nutMenu.addEventListener('click', () => {
            const dangMo = nutMenu.getAttribute('aria-expanded') === 'true';
            nutMenu.setAttribute('aria-expanded', !dangMo);
            menuChinh.classList.toggle('mo');
        });

        // 2. Chức năng bấm phím ESC để đóng Menu
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && nutMenu.getAttribute('aria-expanded') === 'true') {
                nutMenu.setAttribute('aria-expanded', 'false');
                menuChinh.classList.remove('mo');
                nutMenu.focus();
            }
        });
    }
});