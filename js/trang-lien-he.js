/**
 * Tệp js/trang-lien-he.js
 * Chức năng: Kiểm tra biểu mẫu (Validation) phía client và gửi bằng Fetch (không tải lại trang).
 */

document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('form-lien-he');
    if (!form) return;

    const btnGui = document.getElementById('nut-gui');
    const divKetQua = document.getElementById('ket-qua-gui');

    // Hàm kiểm tra một ô nhập liệu và in lỗi
    const kiemTraO = (oNhap) => {
        const theLoi = oNhap.nextElementSibling; // Thẻ span báo lỗi nằm ngay dưới
        
        // Đặt lại lỗi tùy chỉnh trước khi kiểm tra (reset)
        oNhap.setCustomValidity('');

        if (oNhap.validity.valueMissing) {
            oNhap.setCustomValidity('Vui lòng không được bỏ trống ô này.');
        } else if (oNhap.validity.typeMismatch && oNhap.type === 'email') {
            oNhap.setCustomValidity('Vui lòng nhập đúng định dạng email (VD: abc@gmail.com).');
        } else if (oNhap.validity.tooShort) {
            oNhap.setCustomValidity(`Vui lòng nhập ít nhất ${oNhap.minLength} ký tự.`);
        }

        // Hiển thị chữ màu đỏ nếu có lỗi
        if (!oNhap.validity.valid) {
            theLoi.textContent = oNhap.validationMessage;
            oNhap.style.borderColor = 'red';
            return false; // Có lỗi
        } else {
            theLoi.textContent = '';
            oNhap.style.borderColor = 'green';
            return true; // Hợp lệ
        }
    };

    // Sự kiện 1: Kiểm tra lỗi ngay khi người dùng click chuột ra khỏi ô nhập (blur)
    const cacONhap = form.querySelectorAll('input, textarea');
    cacONhap.forEach(oNhap => {
        oNhap.addEventListener('blur', () => {
            kiemTraO(oNhap);
        });
        // Tùy chọn thêm: Xóa lỗi khi người dùng bắt đầu gõ lại
        oNhap.addEventListener('input', () => {
            if (oNhap.style.borderColor === 'red') {
                kiemTraO(oNhap);
            }
        });
    });

    // Sự kiện 2: Kiểm tra toàn bộ Form khi bấm nút Gửi
    form.addEventListener('submit', async (e) => {
        // Chặn trình duyệt tải lại trang
        e.preventDefault();

        let formHopLe = true;
        cacONhap.forEach(oNhap => {
            if (!kiemTraO(oNhap)) {
                formHopLe = false;
            }
        });

        // Nếu Form có lỗi, không làm gì tiếp
        if (!formHopLe) {
            divKetQua.textContent = 'Vui lòng điền đầy đủ và đúng thông tin trước khi gửi.';
            divKetQua.style.color = 'red';
            return;
        }

        // BƯỚC 3: GỬI FETCH (POST)
        const API_LIEN_HE = 'https://jsonplaceholder.typicode.com/posts';
        divKetQua.textContent = 'Đang gửi thông tin, vui lòng đợi...';
        divKetQua.style.color = 'blue';
        btnGui.disabled = true;
        btnGui.textContent = 'Đang xử lý...';
        
        // Lấy dữ liệu từ biểu mẫu
        const duLieu = {
            hoTen: document.getElementById('ho-ten').value,
            email: document.getElementById('email').value,
            tinNhan: document.getElementById('tin-nhan').value
        };

        // Bọc try/catch để bắt lỗi
        try {
            const res = await fetch(API_LIEN_HE, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(duLieu)
            });

            if (!res.ok) throw new Error(`HTTP ${res.status}`);

            const ketQua = await res.json();
            console.log('Kết quả từ API giả lập:', ketQua);

            // Báo thành công, xóa form
            divKetQua.textContent = 'Gửi thành công! Cảm ơn bạn đã đóng góp.';
            divKetQua.style.color = 'green';
            form.reset();
            cacONhap.forEach(o => o.style.borderColor = '#ccc');

        } catch (loi) {
            console.error(loi);
            divKetQua.textContent = 'Không gửi được, vui lòng kiểm tra mạng và thử lại sau.';
            divKetQua.style.color = 'red';
        } finally {
            // Luôn mở khóa nút
            btnGui.disabled = false;
            btnGui.textContent = 'Gửi tin nhắn';
        }
    });
});
