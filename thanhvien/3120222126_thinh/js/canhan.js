/**
 * js/canhan.js - Kịch bản cá nhân cho trang giới thiệu của Nguyễn Tiến Thịnh (MSSV: 3120222126)
 * 
 * Chứa 3 tương tác:
 * 1. Sao chép địa chỉ Email vào bộ nhớ tạm (navigator.clipboard.writeText) kèm thông báo Toast tự ẩn sau 3s.
 * 2. Đánh giá 5 sao cho hồ sơ cá nhân (Interactive 5-Star Rating) có ghi nhớ lựa chọn vào localStorage.
 * 3. Bắt sự kiện submit form có thuộc tính data-confirm để hiển thị hộp thoại xác nhận (thay thế onsubmit nội tuyến phục vụ CSP).
 */

document.addEventListener('DOMContentLoaded', () => {
    // =========================================================================
    // TƯƠNG TÁC 1: Sao chép Email vào Bộ nhớ tạm (navigator.clipboard) + Toast
    // =========================================================================
    const btnCopyEmail = document.getElementById('btn-saochep-email');
    const toastElement = document.getElementById('toast-thong-bao');
    let toastTimeoutId = null;

    if (btnCopyEmail && toastElement) {
        const emailContent = 'thinh@ued.vn';

        const hienThiToast = (noiDung) => {
            toastElement.textContent = noiDung;
            toastElement.classList.add('toast-thong-bao--hien');
            toastElement.setAttribute('aria-hidden', 'false');

            if (toastTimeoutId) {
                clearTimeout(toastTimeoutId);
            }

            toastTimeoutId = setTimeout(() => {
                toastElement.classList.remove('toast-thong-bao--hien');
                toastElement.setAttribute('aria-hidden', 'true');
            }, 3000);
        };

        const thucHienSaoChep = () => {
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(emailContent)
                    .then(() => {
                        hienThiToast(`Đã sao chép email (${emailContent}) vào bộ nhớ tạm!`);
                    })
                    .catch(() => {
                        hienThiToast(`Email của Thịnh: ${emailContent}`);
                    });
            } else {
                hienThiToast(`Email của Thịnh: ${emailContent}`);
            }
        };

        btnCopyEmail.addEventListener('click', thucHienSaoChep);

        btnCopyEmail.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                thucHienSaoChep();
            }
        });
    }

    // =========================================================================
    // TƯƠNG TÁC 2: Đánh giá 5 sao cho Hồ sơ cá nhân (localStorage)
    // =========================================================================
    const danhSachSao = document.querySelectorAll('.nut-danh-gia-sao');
    const thongBaoDanhGia = document.getElementById('thong-bao-danh-gia');
    const LOCAL_STORAGE_KEY = 'thinh_profile_rating';

    if (danhSachSao.length > 0 && thongBaoDanhGia) {
        const savedRating = localStorage.getItem(LOCAL_STORAGE_KEY);
        let currentRating = savedRating ? parseInt(savedRating, 10) : 0;
        let isLocked = false;

        const capNhatGiaoDienSao = (ratingValue) => {
            danhSachSao.forEach((saoBtn) => {
                const saoVal = parseInt(saoBtn.getAttribute('data-val'), 10);
                if (saoVal <= ratingValue) {
                    saoBtn.classList.add('nut-danh-gia-sao--tich-cuc');
                    saoBtn.setAttribute('aria-checked', 'true');
                } else {
                    saoBtn.classList.remove('nut-danh-gia-sao--tich-cuc');
                    saoBtn.setAttribute('aria-checked', 'false');
                }
            });
        };

        const khoaoGiaoDienSao = () => {
            isLocked = true;
            danhSachSao.forEach((saoBtn) => {
                saoBtn.disabled = true;
                saoBtn.classList.add('nut-danh-gia-sao--da-khoa');
                saoBtn.setAttribute('aria-disabled', 'true');
            });
        };

        const capNhatThongBao = (ratingValue) => {
            if (ratingValue > 0) {
                thongBaoDanhGia.textContent = `Bạn đã đánh giá ${ratingValue}/5 sao cho hồ sơ của Thịnh (Đã ghi nhận). Cảm ơn bạn!`;
                thongBaoDanhGia.classList.add('thong-bao-danh-gia--da-danh-gia');
            } else {
                thongBaoDanhGia.textContent = 'Bạn chưa đánh giá. Hãy chọn số sao để gửi đánh giá nhé!';
                thongBaoDanhGia.classList.remove('thong-bao-danh-gia--da-danh-gia');
            }
        };

        if (currentRating > 0) {
            capNhatGiaoDienSao(currentRating);
            capNhatThongBao(currentRating);
            khoaoGiaoDienSao();
        }

        danhSachSao.forEach((saoBtn) => {
            const saoVal = parseInt(saoBtn.getAttribute('data-val'), 10);

            const chonDanhGia = () => {
                if (isLocked) return;
                currentRating = saoVal;
                localStorage.setItem(LOCAL_STORAGE_KEY, currentRating.toString());
                capNhatGiaoDienSao(currentRating);
                capNhatThongBao(currentRating);
                khoaoGiaoDienSao();
            };

            saoBtn.addEventListener('click', chonDanhGia);

            saoBtn.addEventListener('keydown', (e) => {
                if (isLocked) return;
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    chonDanhGia();
                }
            });

            saoBtn.addEventListener('mouseenter', () => {
                if (!isLocked) capNhatGiaoDienSao(saoVal);
            });

            saoBtn.addEventListener('mouseleave', () => {
                if (!isLocked) capNhatGiaoDienSao(currentRating);
            });

            saoBtn.addEventListener('focus', () => {
                if (!isLocked) capNhatGiaoDienSao(saoVal);
            });

            saoBtn.addEventListener('blur', () => {
                if (!isLocked) capNhatGiaoDienSao(currentRating);
            });
        });
    }

    // =========================================================================
    // TƯƠNG TÁC 3: Xử lý data-confirm cho biểu mẫu (Chống inline onsubmit phục vụ CSP)
    // =========================================================================
    document.addEventListener('submit', (e) => {
        const form = e.target;
        if (form && typeof form.getAttribute === 'function') {
            const thongDiep = form.getAttribute('data-confirm');
            if (thongDiep && !window.confirm(thongDiep)) {
                e.preventDefault();
            }
        }
    });
});
