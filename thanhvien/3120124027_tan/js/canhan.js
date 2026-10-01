/**
 * Tương tác JS cho trang cá nhân của Tấn - canhan.js
 * Tương tác 1: Chỉnh cỡ chữ (Thu nhỏ, Mặc định, Phóng to) cho thẻ body.
 * Tương tác 2: Đồng hồ đếm ngược đến ngày báo cáo (sử dụng setInterval).
 * Cách test: Nhấn nút chỉnh cỡ chữ, sau đó F5 để kiểm tra trạng thái lưu trong localStorage. Xem đồng hồ tự nhảy số từng giây.
 */

document.addEventListener('DOMContentLoaded', () => {
    // ==========================================
    // TƯƠNG TÁC 1: CHỈNH CỠ CHỮ
    // ==========================================
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
    
    if (btnGiam) {
        btnGiam.addEventListener('click', () => {
            if (currentFontSize > 60) {
                currentFontSize -= 10;
                applyFontSize(currentFontSize);
            }
        });
    }
    
    if (btnMacDinh) {
        btnMacDinh.addEventListener('click', () => {
            currentFontSize = 100;
            applyFontSize(currentFontSize);
        });
    }
    
    if (btnTang) {
        btnTang.addEventListener('click', () => {
            if (currentFontSize < 160) {
                currentFontSize += 10;
                applyFontSize(currentFontSize);
            }
        });
    }

    // ==========================================
    // TƯƠNG TÁC 2: ĐỒNG HỒ ĐẾM NGƯỢC
    // ==========================================
    const dongHo = document.getElementById('dong-ho-thi');
    if (dongHo) {
        // Ngày thi giả định: 31/12/2026 07:00:00
        const ngayThi = new Date('2026-12-31T07:00:00').getTime();

        const capNhatDongHo = () => {
            const bayGio = new Date().getTime();
            const khoangCach = ngayThi - bayGio;

            if (khoangCach < 0) {
                dongHo.textContent = "🎉 Đã đến ngày thi! Chúc bạn thi tốt!";
                return;
            }

            // Tính toán ngày, giờ, phút, giây
            const ngay = Math.floor(khoangCach / (1000 * 60 * 60 * 24));
            const gio = Math.floor((khoangCach % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const phut = Math.floor((khoangCach % (1000 * 60 * 60)) / (1000 * 60));
            const giay = Math.floor((khoangCach % (1000 * 60)) / 1000);

            // Cập nhật DOM (Dùng tạo DOM an toàn, không dùng innerHTML trực tiếp ghép chuỗi thiếu kiểm soát)
            dongHo.textContent = ''; // Xóa rỗng an toàn
            
            const arrThoiGian = [
                { giaTri: ngay, nhan: 'Ngày' },
                { giaTri: gio, nhan: 'Giờ' },
                { giaTri: phut, nhan: 'Phút' },
                { giaTri: giay, nhan: 'Giây' }
            ];

            arrThoiGian.forEach(item => {
                const span = document.createElement('span');
                span.className = 'thoi-gian';
                // Đảm bảo số luôn có 2 chữ số
                const so = item.giaTri < 10 ? '0' + item.giaTri : item.giaTri;
                span.textContent = `${so} ${item.nhan}`;
                dongHo.appendChild(span);
            });
        };

        // Chạy hàm tính toán ngay khi load
        capNhatDongHo();
        // Sau đó lặp lại mỗi giây
        setInterval(capNhatDongHo, 1000);
    }
});
