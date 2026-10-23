/**
 * js/gio-hang.js — Thêm vào giỏ / lưu giáo án không tải lại trang qua Fetch API
 * Phụ trách: Tấn & Châu — Chức năng 6 Bảng 1
 * Đảm bảo Graceful Degradation: Tắt JS các form lưu giáo án vẫn gửi POST bình thường!
 */

function capNhatHuyHieu(soLuong) {
    const huyHieus = document.querySelectorAll('.huy-hieu-so-luong');
    huyHieus.forEach(el => {
        el.textContent = soLuong;
        // Hiệu ứng nhấp nháy nhẹ để người dùng nhận biết đã cập nhật
        el.style.transform = 'scale(1.25)';
        el.style.transition = 'transform 0.2s';
        setTimeout(() => {
            el.style.transform = 'scale(1)';
        }, 200);
    });
}

function hienThongBao(noiDung, loai = 'thanh-cong') {
    let thongBao = document.getElementById('thong-bao-noi');
    if (!thongBao) {
        thongBao = document.createElement('div');
        thongBao.id = 'thong-bao-noi';
        thongBao.style.cssText = `
            position: fixed;
            bottom: 24px;
            right: 24px;
            padding: 12px 20px;
            border-radius: 8px;
            color: #ffffff;
            font-weight: 500;
            box-shadow: 0 4px 16px rgba(0,0,0,0.2);
            z-index: 9999;
            transition: opacity 0.3s, transform 0.3s;
            opacity: 0;
            transform: translateY(20px);
        `;
        document.body.appendChild(thongBao);
    }

    thongBao.style.background = loai === 'thanh-cong' ? '#28a745' : '#dc3545';
    thongBao.textContent = noiDung;
    thongBao.style.opacity = '1';
    thongBao.style.transform = 'translateY(0)';

    setTimeout(() => {
        thongBao.style.opacity = '0';
        thongBao.style.transform = 'translateY(20px)';
    }, 2500);
}

function khoiTaoGioHangFetch() {
    // 1. Bắt sự kiện trên các form lưu giáo án (nếu có trên trang)
    document.addEventListener('submit', async (e) => {
        const form = e.target.closest('.form-luu-giao-an');
        if (!form) return;

        e.preventDefault();
        const oId = form.querySelector('input[name="id"]');
        const oSl = form.querySelector('input[name="sl"]');
        const id = oId ? parseInt(oId.value, 10) : 0;
        const sl = oSl ? parseInt(oSl.value, 10) : 1;

        if (id <= 0) return;

        try {
            const res = await fetch('api/gio-hang.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id, sl })
            });
            const data = await res.json();
            if (res.ok && data.thanh_cong) {
                capNhatHuyHieu(data.so_mon);
                hienThongBao(data.thong_bao || 'Đã lưu giáo án thành công!');
            } else {
                hienThongBao(data.loi || 'Không thể lưu giáo án', 'loi');
            }
        } catch (err) {
            form.submit(); // Fallback gửi form thường nếu mạng lỗi
        }
    });

    // 2. Bắt sự kiện trên các nút bấm nhanh có class .nut-luu-giao-an
    document.addEventListener('click', async (e) => {
        const nut = e.target.closest('.nut-luu-giao-an');
        if (!nut) return;

        e.preventDefault();
        const id = parseInt(nut.dataset.id || nut.getAttribute('data-id'), 10);
        if (!id || id <= 0) return;

        try {
            const res = await fetch('api/gio-hang.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id, sl: 1 })
            });
            const data = await res.json();
            if (res.ok && data.thanh_cong) {
                capNhatHuyHieu(data.so_mon);
                hienThongBao(data.thong_bao || 'Đã lưu giáo án vào giỏ!');
                nut.classList.add('da-thich');
                nut.style.color = '#e74c3c';
            } else {
                hienThongBao(data.loi || 'Không thể lưu giáo án', 'loi');
            }
        } catch (err) {
            hienThongBao('Lỗi kết nối khi lưu giáo án', 'loi');
        }
    });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', khoiTaoGioHangFetch);
} else {
    khoiTaoGioHangFetch();
}

