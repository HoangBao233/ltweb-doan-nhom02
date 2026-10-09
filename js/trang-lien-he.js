/**
 * Tệp js/trang-lien-he.js
 * Chức năng: Kiểm tra biểu mẫu (Validation) phía client trước khi submit PHP.
 */

document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('form-lien-he');
    if (!form) return;

    // Hàm kiểm tra một ô nhập liệu và in lỗi
    const kiemTraO = (oNhap) => {
        let theLoi = oNhap.nextElementSibling;
        if (!theLoi || !theLoi.classList.contains('bieu-mau__loi')) {
            theLoi = oNhap.parentElement ? oNhap.parentElement.querySelector('.bieu-mau__loi') : null;
        }

        // Đặt lại lỗi tùy chỉnh trước khi kiểm tra (reset)
        oNhap.setCustomValidity('');

        if (oNhap.validity.valueMissing) {
            oNhap.setCustomValidity('Vui lòng không được bỏ trống ô này.');
        } else if (oNhap.validity.typeMismatch && oNhap.type === 'email') {
            oNhap.setCustomValidity('Vui lòng nhập đúng định dạng email (VD: abc@gmail.com).');
        } else if (oNhap.validity.patternMismatch && oNhap.name === 'sdt') {
            oNhap.setCustomValidity('Vui lòng nhập số điện thoại hợp lệ (9-11 chữ số).');
        } else if (oNhap.validity.tooShort) {
            oNhap.setCustomValidity(`Vui lòng nhập ít nhất ${oNhap.minLength} ký tự.`);
        }

        // Hiển thị chữ màu đỏ nếu có lỗi
        if (!oNhap.validity.valid) {
            if (theLoi) theLoi.textContent = oNhap.validationMessage;
            oNhap.style.borderColor = 'red';
            return false; // Có lỗi
        } else {
            if (theLoi) theLoi.textContent = '';
            oNhap.style.borderColor = 'green';
            return true; // Hợp lệ
        }
    };

    // Sự kiện 1: Kiểm tra lỗi ngay khi người dùng click chuột ra khỏi ô nhập (blur)
    const cacONhap = form.querySelectorAll('input:not([type="file"]), textarea');
    cacONhap.forEach(oNhap => {
        oNhap.addEventListener('blur', () => {
            kiemTraO(oNhap);
        });
        oNhap.addEventListener('input', () => {
            if (oNhap.style.borderColor === 'red') {
                kiemTraO(oNhap);
            }
        });
    });

    // Sự kiện 2: Kiểm tra toàn bộ Form khi bấm nút Gửi
    form.addEventListener('submit', (e) => {
        let formHopLe = true;
        cacONhap.forEach(oNhap => {
            if (!kiemTraO(oNhap)) {
                formHopLe = false;
            }
        });

        // Nếu Form có lỗi, chặn gửi
        if (!formHopLe) {
            e.preventDefault();
        }
    });
});

