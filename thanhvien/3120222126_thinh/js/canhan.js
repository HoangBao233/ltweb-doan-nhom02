/**
 * js/canhan.js - Kịch bản cá nhân cho trang giới thiệu của Nguyễn Tiến Thịnh (MSSV: 3120222126)
 * 
 * Chứa 2 tương tác (Phần C):
 * 1. Sao chép địa chỉ Email vào bộ nhớ tạm (navigator.clipboard.writeText) kèm thông báo Toast nổi tự ẩn sau 3s.
 * 2. Đánh giá 5 sao cho hồ sơ cá nhân (Interactive 5-Star Rating) có ghi nhớ lựa chọn vào localStorage.
 * 
 * Cách thử:
 * - Tương tác 1: Click hoặc dùng bàn phím (Tab + Enter/Space) nhấn nút "Sao chép Email" để chép email và xem thông báo Toast.
 * - Tương tác 2: Nhấp chuột hoặc dùng phím (Tab + Enter/Space) chọn các ngôi sao (1-5 sao) để đánh giá hồ sơ. Nạp lại trang (F5) để xác nhận số sao được lưu.
 */

document.addEventListener('DOMContentLoaded', () => {
    // =========================================================================
    // TƯƠNG TÁC 1: Sao chép Email vào Bộ nhớ tạm (navigator.clipboard) + Toast
    // =========================================================================
    const btnCopyEmail = document.getElementById('btn-saochep-email');
    const toastElement = document.getElementById('toast-thong-bao');
    let toastTimeoutId = null;

    if (btnCopyEmail && toastElement) {
        const emailContent = '3120222126@student.sgu.edu.vn';

        const hienThiToast = (noiDung) => {
            // Cập nhật nội dung thông báo an toàn bằng textContent
            toastElement.textContent = noiDung;
            toastElement.classList.add('toast-thong-bao--hien');
            toastElement.setAttribute('aria-hidden', 'false');

            // Xóa bộ đếm thời gian cũ nếu người dùng bấm liên tục
            if (toastTimeoutId) {
                clearTimeout(toastTimeoutId);
            }

            // Tự động ẩn thông báo Toast sau 3 giây
            toastTimeoutId = setTimeout(() => {
                toastElement.classList.remove('toast-thong-bao--hien');
                toastElement.setAttribute('aria-hidden', 'true');
            }, 3000);
        };

        const thucHienSaoChep = () => {
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(emailContent)
                    .then(() => {
                        hienThiToast(`✅ Đã sao chép email (${emailContent}) vào bộ nhớ tạm!`);
                    })
                    .catch(() => {
                        hienThiToast(`📧 Email của Thịnh: ${emailContent}`);
                    });
            } else {
                // Phương án dự phòng cho trình duyệt không hỗ trợ Clipboard API
                hienThiToast(`📧 Email của Thịnh: ${emailContent}`);
            }
        };

        // Kích hoạt khi click chuột
        btnCopyEmail.addEventListener('click', thucHienSaoChep);

        // Kích hoạt khi dùng bàn phím (Phím Enter hoặc Phím Space)
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
        // Đọc điểm đánh giá đã lưu từ localStorage
        const savedRating = localStorage.getItem(LOCAL_STORAGE_KEY);
        let currentRating = savedRating ? parseInt(savedRating, 10) : 0;
        let isLocked = false;

        // Hàm tô màu các ngôi sao theo giá trị số nguyên
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

        // Hàm khóa tất cả các nút đánh giá sao (Hướng 1: Khóa cố định sau khi chọn)
        const khoaoGiaoDienSao = () => {
            isLocked = true;
            danhSachSao.forEach((saoBtn) => {
                saoBtn.disabled = true;
                saoBtn.classList.add('nut-danh-gia-sao--da-khoa');
                saoBtn.setAttribute('aria-disabled', 'true');
            });
        };

        // Hàm cập nhật văn bản thông báo điểm đánh giá
        const capNhatThongBao = (ratingValue) => {
            if (ratingValue > 0) {
                thongBaoDanhGia.textContent = `⭐ Bạn đã đánh giá ${ratingValue}/5 sao cho hồ sơ của Thịnh (Đã ghi nhận). Cảm ơn bạn!`;
                thongBaoDanhGia.classList.add('thong-bao-danh-gia--da-danh-gia');
            } else {
                thongBaoDanhGia.textContent = 'Bạn chưa đánh giá. Hãy chọn số sao để gửi đánh giá nhé!';
                thongBaoDanhGia.classList.remove('thong-bao-danh-gia--da-danh-gia');
            }
        };

        // Khôi phục giao diện ban đầu khi vừa tải trang
        if (currentRating > 0) {
            capNhatGiaoDienSao(currentRating);
            capNhatThongBao(currentRating);
            khoaoGiaoDienSao();
        }

        // Lắng nghe sự kiện cho từng ngôi sao
        danhSachSao.forEach((saoBtn) => {
            const saoVal = parseInt(saoBtn.getAttribute('data-val'), 10);

            // Xử lý khi chọn điểm (Click chuột)
            const chonDanhGia = () => {
                if (isLocked) return; // Nếu đã khóa thì không cho chọn tiếp
                currentRating = saoVal;
                localStorage.setItem(LOCAL_STORAGE_KEY, currentRating.toString());
                capNhatGiaoDienSao(currentRating);
                capNhatThongBao(currentRating);
                khoaoGiaoDienSao(); // Khóa dải sao ngay sau khi chọn
            };

            saoBtn.addEventListener('click', chonDanhGia);

            // Xử lý khi chọn bằng bàn phím (Phím Enter hoặc Phím Space)
            saoBtn.addEventListener('keydown', (e) => {
                if (isLocked) return;
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    chonDanhGia();
                }
            });

            // Hiệu ứng xem trước (Hover chuột / Focus bàn phím) - Chỉ chạy khi chưa khóa
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
});
