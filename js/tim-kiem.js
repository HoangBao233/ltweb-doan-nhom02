/**
 * js/tim-kiem.js — Tìm kiếm tức thời (Live Search) với kỹ thuật Debounce 300ms
 * Phụ trách: Tấn (MSSV 3120124027) — Chức năng 6 Bảng 1
 * Đảm bảo Graceful Degradation: Tắt JS form tìm kiếm vẫn hoạt động bình thường!
 */

function debounce(ham, thoiGian = 300) {
    let boDem = null;
    return function (...thamSo) {
        clearTimeout(boDem);
        boDem = setTimeout(() => ham.apply(this, thamSo), thoiGian);
    };
}

function khoiTaoTimKiemTucThoi() {
    const oNhap = document.getElementById('o-tim-kiem');
    if (!oNhap) return;

    // Tạo hộp gợi ý kết quả hiển thị bên dưới ô tìm kiếm
    let hopGoiY = document.getElementById('hop-goi-y-tim-kiem');
    if (!hopGoiY) {
        hopGoiY = document.createElement('div');
        hopGoiY.id = 'hop-goi-y-tim-kiem';
        hopGoiY.setAttribute('role', 'listbox');
        hopGoiY.setAttribute('aria-label', 'Gợi ý kết quả tìm kiếm');
        hopGoiY.style.cssText = `
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: #ffffff;
            border: 1px solid #ddd;
            border-radius: 6px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            max-height: 380px;
            overflow-y: auto;
            z-index: 1000;
            margin-top: 4px;
        `;
        // Đặt cha của ô nhập thành position relative nếu chưa có
        if (oNhap.parentElement) {
            oNhap.parentElement.style.position = 'relative';
            oNhap.parentElement.appendChild(hopGoiY);
        }
    }

    let boDieuKhienHuy = null;

    const thucHienTimKiem = debounce(async (tuKhoa) => {
        tuKhoa = tuKhoa.trim();
        if (tuKhoa === '') {
            hopGoiY.style.display = 'none';
            hopGoiY.innerHTML = '';
            return;
        }

        // Hủy request trước đó nếu người dùng tiếp tục gõ
        if (boDieuKhienHuy) {
            boDieuKhienHuy.abort();
        }
        boDieuKhienHuy = new AbortController();

        try {
            const phanHoi = await fetch(`api/giao-an.php?q=${encodeURIComponent(tuKhoa)}`, {
                signal: boDieuKhienHuy.signal
            });

            if (!phanHoi.ok) throw new Error('Lỗi máy chủ khi tìm kiếm');
            const ketQua = await phanHoi.json();
            const danhSach = ketQua.du_lieu || [];

            if (danhSach.length === 0) {
                hopGoiY.innerHTML = `
                    <div style="padding: 1rem; color: #666; text-align: center; font-style: italic;">
                        Không tìm thấy giáo án nào phù hợp với "<strong>${tuKhoa}</strong>"
                    </div>
                `;
            } else {
                hopGoiY.innerHTML = danhSach.map(ga => {
                    const giaDinhDang = ga.gia == 0 ? 'Miễn phí' : Number(ga.gia).toLocaleString('vi-VN') + ' đ';
                    return `
                        <a href="chi-tiet-giao-an.php?id=${ga.id}" style="
                            display: flex;
                            align-items: center;
                            gap: 0.75rem;
                            padding: 0.75rem 1rem;
                            border-bottom: 1px solid #f0f0f0;
                            text-decoration: none;
                            color: inherit;
                            transition: background 0.15s;
                        " onmouseover="this.style.background='#f8f9fa'" onmouseout="this.style.background='transparent'">
                            <img src="${ga.hinh_anh || 'images/banner-trang-chu.jpg'}" alt="${ga.ten}" 
                                 style="width: 50px; height: 35px; object-fit: cover; border-radius: 4px; flex-shrink: 0;">
                            <div style="flex: 1; min-width: 0;">
                                <div style="font-weight: 600; font-size: 0.9rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                    ${ga.ten}
                                </div>
                                <div style="font-size: 0.8rem; color: #888;">
                                    ${ga.cap_hoc || ''} • <strong style="color: #007bff;">${giaDinhDang}</strong>
                                </div>
                            </div>
                        </a>
                    `;
                }).join('');
            }
            hopGoiY.style.display = 'block';
        } catch (loi) {
            if (loi.name !== 'AbortError') {
                hopGoiY.style.display = 'none';
            }
        }
    }, 300);

    oNhap.addEventListener('input', (e) => {
        thucHienTimKiem(e.target.value);
    });

    // Đóng hộp gợi ý khi click ra ngoài
    document.addEventListener('click', (e) => {
        if (!oNhap.contains(e.target) && !hopGoiY.contains(e.target)) {
            hopGoiY.style.display = 'none';
        }
    });

    // Đóng khi nhấn phím Escape
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            hopGoiY.style.display = 'none';
        }
    });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', khoiTaoTimKiemTucThoi);
} else {
    khoiTaoTimKiemTucThoi();
}

