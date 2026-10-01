/**
 * Bộ công cụ Hỗ trợ đọc (Accessibility Toolkit) - canhan.js
 * Tương tác 1: Chỉnh cỡ chữ (Thu nhỏ, Mặc định, Phóng to) cho thẻ body.
 * Tương tác 2: Chế độ đọc sách (Sepia mode) đổi màu nền thẻ body.
 * Cách test: Nhấn nút trên giao diện, sau đó F5 để kiểm tra trạng thái lưu trong localStorage.
 */

document.addEventListener('DOMContentLoaded', () => {
    // --- TƯƠNG TÁC 1: CHỈNH CỠ CHỮ ---
    const btnGiam = document.getElementById('btn-giam-chu');
    const btnMacDinh = document.getElementById('btn-mac-dinh');
    const btnTang = document.getElementById('btn-tang-chu');
    const bodyTag = document.body;
    
    // Đọc mức cỡ chữ (tính theo %) từ localStorage
    const savedFontSize = localStorage.getItem('tan_fontSize');
    let currentFontSize = savedFontSize ? parseInt(savedFontSize) : 100;
    
    const applyFontSize = (size) => {
        bodyTag.style.fontSize = `${size}%`;
        localStorage.setItem('tan_fontSize', size);
    };
    
    // Khôi phục cỡ chữ khi load trang
    if (savedFontSize) {
        applyFontSize(currentFontSize);
    }
    
    btnGiam.addEventListener('click', () => {
        if (currentFontSize > 60) {
            currentFontSize -= 10;
            applyFontSize(currentFontSize);
        }
    });
    
    btnMacDinh.addEventListener('click', () => {
        currentFontSize = 100;
        applyFontSize(currentFontSize);
    });
    
    btnTang.addEventListener('click', () => {
        if (currentFontSize < 160) {
            currentFontSize += 10;
            applyFontSize(currentFontSize);
        }
    });

    // --- TƯƠNG TÁC 2: CHẾ ĐỘ ĐỌC SÁCH ---
    const btnCheDoDoc = document.getElementById('btn-che-do-doc');
    
    const savedReadingMode = localStorage.getItem('tan_readingMode');
    let isReadingMode = savedReadingMode === 'true';
    
    const updateReadingModeUI = () => {
        if (isReadingMode) {
            bodyTag.classList.add('che-do-doc-sach');
            btnCheDoDoc.textContent = 'Tắt Chế độ đọc sách';
        } else {
            bodyTag.classList.remove('che-do-doc-sach');
            btnCheDoDoc.textContent = 'Bật Chế độ đọc sách';
        }
    };
    
    // Khôi phục chế độ đọc sách khi load trang
    updateReadingModeUI();
    
    btnCheDoDoc.addEventListener('click', () => {
        isReadingMode = !isReadingMode;
        localStorage.setItem('tan_readingMode', isReadingMode);
        updateReadingModeUI();
    });
});

