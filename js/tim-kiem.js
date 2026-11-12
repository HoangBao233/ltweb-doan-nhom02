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

function chuanHoaDuongDanAnh(duongDan) {
    const macDinh = 'images/banner-trang-chu.jpg';
    if (typeof duongDan !== 'string') return macDinh;
    const sach = duongDan.trim().replace(/^\/+/, '');
    if (sach === '' || sach.includes('..') || /^javascript:/i.test(sach)) {
        return macDinh;
    }
    if (sach.startsWith('images/') || sach.startsWith('uploads/')) {
        return sach;
    }
    return 'uploads/' + sach;
}

function taoMucGoiY(ga) {
    const theA = document.createElement('a');
    const id = parseInt(ga.id, 10) || 0;
    theA.href = `chi-tiet-giao-an.php?id=${id}`;
    theA.style.cssText = `
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.75rem 1rem;
        border-bottom: 1px solid #f0f0f0;
        text-decoration: none;
        color: inherit;
        transition: background 0.15s;
    `;
    theA.addEventListener('mouseenter', () => {
        theA.style.background = '#f8f9fa';
    });
    theA.addEventListener('mouseleave', () => {
        theA.style.background = 'transparent';
    });

    const anh = document.createElement('img');
    anh.src = chuanHoaDuongDanAnh(ga.hinh_anh);
    anh.alt = typeof ga.ten === 'string' ? ga.ten : '';
    anh.style.cssText = 'width: 50px; height: 35px; object-fit: cover; border-radius: 4px; flex-shrink: 0;';

    const khoiNoiDung = document.createElement('div');
    khoiNoiDung.style.cssText = 'flex: 1; min-width: 0;';

    const tenEl = document.createElement('div');
    tenEl.style.cssText = 'font-weight: 600; font-size: 0.9rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;';
    tenEl.textContent = ga.ten ?? '';

    const thongTinEl = document.createElement('div');
    thongTinEl.style.cssText = 'font-size: 0.8rem; color: #888;';

    const capHoc = typeof ga.cap_hoc === 'string' ? ga.cap_hoc : '';
    const giaSo = Number(ga.gia) || 0;
    const giaDinhDang = giaSo === 0 ? 'Miễn phí' : giaSo.toLocaleString('vi-VN') + ' đ';

    thongTinEl.appendChild(document.createTextNode(`${capHoc} • `));
    const giaEl = document.createElement('strong');
    giaEl.style.color = '#007bff';
    giaEl.textContent = giaDinhDang;
    thongTinEl.appendChild(giaEl);

    khoiNoiDung.appendChild(tenEl);
    khoiNoiDung.appendChild(thongTinEl);

    theA.appendChild(anh);
    theA.appendChild(khoiNoiDung);
    return theA;
}

function taoThongBaoRong(tuKhoa) {
    const hop = document.createElement('div');
    hop.style.cssText = 'padding: 1rem; color: #666; text-align: center; font-style: italic;';
    hop.appendChild(document.createTextNode('Không tìm thấy giáo án nào phù hợp với "'));
    const tuKhoaEl = document.createElement('strong');
    tuKhoaEl.textContent = tuKhoa;
    hop.appendChild(tuKhoaEl);
    hop.appendChild(document.createTextNode('"'));
    return hop;
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
            hopGoiY.replaceChildren();
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
            const danhSach = Array.isArray(ketQua.du_lieu) ? ketQua.du_lieu : [];

            if (danhSach.length === 0) {
                hopGoiY.replaceChildren(taoThongBaoRong(tuKhoa));
            } else {
                const phanTu = danhSach.map(taoMucGoiY);
                hopGoiY.replaceChildren(...phanTu);
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

