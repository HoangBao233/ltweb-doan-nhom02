/**
 * js/trang-danh-sach.js
 * Chức năng 2 & 6: Render danh sách giáo án từ JSON, tìm kiếm, lọc, sắp xếp.
 * Tích hợp nút Thêm/Bỏ yêu thích (Lưu 🔖).
 */

import { taiDuLieuJSON } from './api.js';
import { themHoacXoaYeuThich, layDanhSachYeuThich } from './yeu-thich.js';

const vungDanhSach = document.getElementById('vung-chua-danh-sach');
const trangThaiTai = document.getElementById('trang-thai-tai');
const oTimKiem = document.getElementById('o-tim-kiem');
const locCapHoc = document.getElementById('loc-cap-hoc');
const sapXep = document.getElementById('sap-xep');

let danhSachGoc = [];

// Hàm loại bỏ dấu tiếng Việt để tìm kiếm không dấu
function boDauTiengViet(str) {
    return str.normalize("NFD").replace(/[\u0300-\u036f]/g, "").toLowerCase();
}

// 1. Hàm render danh sách ra HTML
function renderDanhSach(danhSach) {
    vungDanhSach.innerHTML = ''; // Cố tình clear vùng rỗng trước khi render (chấp nhận innerHTML='' hoặc textContent='')
    
    if (danhSach.length === 0) {
        trangThaiTai.textContent = 'Không tìm thấy giáo án nào phù hợp.';
        trangThaiTai.style.display = 'block';
        return;
    }
    
    trangThaiTai.style.display = 'none';
    const dsYeuThich = layDanhSachYeuThich();

    danhSach.forEach(item => {
        // Tránh dùng innerHTML cho dữ liệu người dùng/API theo đúng quy tắc an toàn.
        // Dùng createElement
        const article = document.createElement('article');
        article.className = 'the-hoc-lieu';

        // Ảnh
        const img = document.createElement('img');
        img.src = item.hinh_anh;
        img.alt = item.ten;
        img.className = 'the-hoc-lieu__anh';
        img.width = 300;
        img.height = 180;
        img.loading = 'lazy';
        article.appendChild(img);

        // Nội dung
        const divNoiDung = document.createElement('div');
        divNoiDung.className = 'the-hoc-lieu__noi-dung';

        const spanCapHoc = document.createElement('span');
        spanCapHoc.className = 'nhan-nho ' + (item.cap_hoc === 'THPT' ? 'nhan-nho--tim' : 'nhan-nho--xanh');
        spanCapHoc.textContent = item.cap_hoc;
        divNoiDung.appendChild(spanCapHoc);

        const h3 = document.createElement('h3');
        h3.className = 'the-hoc-lieu__tieu-de';
        h3.textContent = item.ten;
        divNoiDung.appendChild(h3);

        const pMoTa = document.createElement('p');
        pMoTa.className = 'the-hoc-lieu__mo-ta';
        pMoTa.textContent = item.mo_ta;
        divNoiDung.appendChild(pMoTa);

        const pGia = document.createElement('p');
        pGia.style.fontWeight = 'bold';
        pGia.style.color = 'var(--mau-nhan, #ff5722)';
        pGia.textContent = item.gia === 0 ? 'Miễn phí' : item.gia.toLocaleString('vi-VN') + ' đ';
        divNoiDung.appendChild(pGia);

        // Nút bấm container
        const divNut = document.createElement('div');
        divNut.style.display = 'flex';
        divNut.style.gap = '0.5rem';
        divNut.style.marginTop = '1rem';

        const aChiTiet = document.createElement('a');
        aChiTiet.href = `chi-tiet-giao-an.html?id=${item.id}`;
        aChiTiet.className = 'nut-thao-tac nut-thao-tac--xem';
        aChiTiet.textContent = 'Xem chi tiết';
        divNut.appendChild(aChiTiet);

        // NÚT LƯU YÊU THÍCH (Chức năng 6)
        const daLuu = dsYeuThich.includes(item.id.toString());
        const btnLuu = document.createElement('button');
        btnLuu.type = 'button';
        btnLuu.className = 'nut-thao-tac btn-luu';
        btnLuu.dataset.id = item.id; // Lưu id vào data-attribute để ủy quyền sự kiện
        // Đổi màu nếu đã lưu
        btnLuu.style.backgroundColor = daLuu ? '#28a745' : '';
        btnLuu.style.color = daLuu ? '#fff' : '';
        btnLuu.textContent = daLuu ? '🔖 Đã lưu' : '🔖 Lưu';
        divNut.appendChild(btnLuu);

        divNoiDung.appendChild(divNut);
        article.appendChild(divNoiDung);
        
        vungDanhSach.appendChild(article);
    });
}

// 2. Hàm xử lý logic Tìm kiếm, Lọc, Sắp xếp
function xuLyDuLieu() {
    let ketQua = [...danhSachGoc];

    // Lọc theo cấp học
    const capHocChon = locCapHoc.value;
    if (capHocChon !== 'Tất cả') {
        ketQua = ketQua.filter(item => item.cap_hoc === capHocChon);
    }

    // Tìm kiếm (không dấu)
    const tuKhoa = boDauTiengViet(oTimKiem.value.trim());
    if (tuKhoa !== '') {
        ketQua = ketQua.filter(item => boDauTiengViet(item.ten).includes(tuKhoa));
    }

    // Sắp xếp
    const kieuSapXep = sapXep.value;
    if (kieuSapXep === 'ten-az') {
        ketQua.sort((a, b) => a.ten.localeCompare(b.ten));
    } else if (kieuSapXep === 'gia-tang') {
        ketQua.sort((a, b) => a.gia - b.gia);
    } else if (kieuSapXep === 'gia-giam') {
        ketQua.sort((a, b) => b.gia - a.gia);
    }

    renderDanhSach(ketQua);
}

// 3. Khởi tạo: Fetch dữ liệu
async function khoiTao() {
    try {
        const data = await taiDuLieuJSON('data/giao-an.json');
        danhSachGoc = data;
        renderDanhSach(danhSachGoc);
    } catch (error) {
        console.error(error);
        trangThaiTai.textContent = 'Lỗi kết nối. Không thể tải danh sách giáo án.';
    }
}

// 4. Lắng nghe sự kiện
document.addEventListener('DOMContentLoaded', () => {
    khoiTao();

    // Gắn sự kiện cho các ô input (Tìm, Lọc, Sắp xếp)
    oTimKiem.addEventListener('input', xuLyDuLieu);
    locCapHoc.addEventListener('change', xuLyDuLieu);
    sapXep.addEventListener('change', xuLyDuLieu);

    // ỦY QUYỀN SỰ KIỆN (Event Delegation) cho nút Lưu 🔖
    vungDanhSach.addEventListener('click', (e) => {
        // Kiểm tra xem phần tử bị click có phải là nút Lưu không
        if (e.target.classList.contains('btn-luu')) {
            const id = e.target.dataset.id;
            // Gọi hàm thêm/xóa trong yeu-thich.js
            themHoacXoaYeuThich(id);
            
            // Render lại danh sách để cập nhật màu sắc của nút
            xuLyDuLieu();

            // Phát sự kiện toàn cục để file main.js cập nhật lại số đếm trên Header
            window.dispatchEvent(new Event('capNhatYeuThich'));
        }
    });
});
