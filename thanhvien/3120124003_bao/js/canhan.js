/**
 * js/canhan.js - Kịch bản cá nhân cho trang giới thiệu của Lê Hoàng Bảo
 * 
 * Chứa 2 tương tác (Phần C):
 * 1. Chuyển đổi giao diện Sáng/Tối (Dark Mode) có lưu vào localStorage. Thử bằng cách bấm nút Sáng/Tối trên thanh menu.
 * 2. Trình xem ảnh toàn màn hình (Image Lightbox). Thử bằng cách click vào ảnh đại diện.
 */

document.addEventListener('DOMContentLoaded', () => {
    // ==========================================
    // TƯƠNG TÁC 1: Chế độ Sáng / Tối (Dark Mode)
    // ==========================================
    const body = document.body;
    
    // Tạo nút toggle dark mode động bằng createElement
    const darkModeBtn = document.createElement('button');
    darkModeBtn.id = 'btn-dark-mode';
    darkModeBtn.className = 'nut-thao-tac nut-thao-tac--xem'; // Dùng class có sẵn của dự án
    darkModeBtn.style.cursor = 'pointer';
    darkModeBtn.style.marginLeft = '10px';
    
    // Đọc trạng thái từ localStorage
    const isDarkMode = localStorage.getItem('bao_dark_mode') === 'true';
    if (isDarkMode) {
        body.classList.add('dark-mode');
        darkModeBtn.textContent = 'Giao diện: Tối';
    } else {
        darkModeBtn.textContent = 'Giao diện: Sáng';
    }

    // Chèn nút vào thanh điều hướng (menu) bằng cách tạo thẻ <li>
    const menuUl = document.querySelector('.menu-chinh');
    if (menuUl) {
        const li = document.createElement('li');
        li.appendChild(darkModeBtn);
        menuUl.appendChild(li);
    }

    // Xử lý sự kiện click
    darkModeBtn.addEventListener('click', () => {
        const isDark = body.classList.toggle('dark-mode');
        // Thay đổi nội dung chữ (textContent)
        darkModeBtn.textContent = isDark ? 'Giao diện: Tối' : 'Giao diện: Sáng';
        // Lưu lựa chọn
        localStorage.setItem('bao_dark_mode', isDark);
    });


    // ==========================================
    // TƯƠNG TÁC 2: Trình xem ảnh toàn màn hình (Lightbox)
    // ==========================================
    const avatarImg = document.querySelector('.anh-chan-dung');
    
    if (avatarImg) {
        // Cấp quyền cho phím Tab nhảy vào ảnh (chuẩn A11y)
        avatarImg.tabIndex = 0;
        avatarImg.setAttribute('role', 'button');
        avatarImg.setAttribute('aria-label', 'Xem ảnh phóng to');

        const openLightbox = () => {
            // Tạo thẻ div bao bọc (Modal màng đen)
            const lightboxModal = document.createElement('div');
            lightboxModal.className = 'lightbox-modal';
            
            // Tạo thẻ img bên trong modal
            const lightboxImg = document.createElement('img');
            lightboxImg.src = avatarImg.src;
            lightboxImg.alt = avatarImg.alt + ' (Phóng to)';
            
            // Tạo nút đóng [X]
            const closeBtn = document.createElement('span');
            closeBtn.className = 'lightbox-close';
            closeBtn.textContent = '×';
            closeBtn.setAttribute('aria-label', 'Đóng ảnh');
            closeBtn.setAttribute('role', 'button');
            closeBtn.tabIndex = 0; // Hỗ trợ A11y (phím Tab)
            
            // Gắn vào DOM
            lightboxModal.appendChild(closeBtn);
            lightboxModal.appendChild(lightboxImg);
            document.body.appendChild(lightboxModal);
            
            // Focus vào nút đóng ngay lập tức để người dùng có thể nhấn Enter đóng luôn (chuẩn A11y)
            closeBtn.focus();
            
            // Hàm đóng modal
            const closeModal = () => {
                lightboxModal.remove();
                avatarImg.focus(); // Trả lại focus về ảnh gốc
            };
            
            // Lắng nghe sự kiện đóng (Click nút X, Click ra ngoài ảnh, hoặc nhấn Esc)
            closeBtn.addEventListener('click', closeModal);
            
            lightboxModal.addEventListener('click', (e) => {
                // Chỉ đóng nếu click vào vùng nền mờ, không đóng khi click trực tiếp vào ảnh to
                if (e.target === lightboxModal) {
                    closeModal();
                }
            });
            
            // Lắng nghe phím Enter trên nút đóng hoặc phím Esc
            lightboxModal.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' || (e.key === 'Enter' && e.target === closeBtn)) {
                    closeModal();
                }
            });
        };

        // Kích hoạt khi click chuột
        avatarImg.addEventListener('click', openLightbox);
        
        // Kích hoạt khi nhấn phím Enter (Hỗ trợ bàn phím)
        avatarImg.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                openLightbox();
            }
        });
    }
});
