// js/trang-chi-tiet.js
// Module điều khiển riêng cho trang chi tiết giáo án

import { taiDuLieuJSON } from './api.js';
import { layDanhSachYeuThich, themHoacXoaYeuThich, capNhatSoDemYeuThich } from './yeu-thich.js';

document.addEventListener('DOMContentLoaded', async () => {
    // 1. Cập nhật số đếm yêu thích ngay khi mở trang
    capNhatSoDemYeuThich();

    const mainEl = document.querySelector('.trang__chinh');
    if (!mainEl) return;

    // Đọc tham số ID từ URL (VD: ?id=1)
    const params = new URLSearchParams(window.location.search);
    const idParam = params.get('id');
    const id = idParam ? Number(idParam) : null;

    if (!id) {
        mainEl.textContent = 'Vui lòng cung cấp ID giáo án trên URL (ví dụ: ?id=1).';
        return;
    }

    // --- TRẠNG THÁI: ĐANG TẢI ---
    const loadingMsg = document.createElement('p');
    loadingMsg.textContent = 'Đang tải chi tiết giáo án...';
    
    // Tạm ẩn nội dung tĩnh cũ đi để hiển thị loading
    const originalNodes = Array.from(mainEl.children);
    originalNodes.forEach(el => el.style.display = 'none');
    
    mainEl.appendChild(loadingMsg);

    try {
        // Gọi API tải file dữ liệu JSON
        const danhSachGiaoAn = await taiDuLieuJSON('data/giao-an.json');
        
        // Tìm giáo án tương ứng ID
        const giaoAn = danhSachGiaoAn.find(item => item.id === id);

        // Xóa dòng thông báo "Đang tải..."
        mainEl.removeChild(loadingMsg);

        if (!giaoAn) {
            // --- TRẠNG THÁI: RỖNG (Không tìm thấy) ---
            const emptyMsg = document.createElement('p');
            emptyMsg.textContent = 'Không tìm thấy giáo án này.';
            mainEl.appendChild(emptyMsg);
            return;
        }

        // --- TRẠNG THÁI: TÌM THẤY & HIỂN THỊ DỮ LIỆU ---
        
        // Khôi phục lại khối nội dung
        originalNodes.forEach(el => el.style.display = '');

        // YÊU CẦU: Cập nhật title của document
        document.title = `${giaoAn.ten} | ITeduShare`;

        // YÊU CẦU: Cập nhật DOM (Dùng textContent, src, alt - Tuyệt đối KHÔNG dùng innerHTML)
        const tieuDeEl = mainEl.querySelector('.chi-tiet-giao-an__tieu-de');
        if (tieuDeEl) tieuDeEl.textContent = `Chi tiết giáo án: ${giaoAn.ten}`;

        const mucTieuEl = mainEl.querySelector('.chi-tiet-giao-an__noi-dung p');
        if (mucTieuEl) mucTieuEl.textContent = giaoAn.muc_tieu;

        const imgEl = mainEl.querySelector('figure img');
        if (imgEl) {
            imgEl.src = giaoAn.hinh_anh;
            imgEl.alt = giaoAn.mo_ta_anh;
        }

        const figcapEl = mainEl.querySelector('figure figcaption');
        if (figcapEl) figcapEl.textContent = giaoAn.chu_thich_anh;

        const iframeEl = mainEl.querySelector('iframe');
        if (iframeEl) iframeEl.src = giaoAn.video_url;

        // Bảng thông số
        const doiTuongEl = mainEl.querySelector('tbody tr:nth-child(1) td');
        if (doiTuongEl) doiTuongEl.textContent = giaoAn.doi_tuong;

        const dungLuongEl = mainEl.querySelector('tbody tr:nth-child(2) td');
        if (dungLuongEl) dungLuongEl.textContent = giaoAn.dung_luong;

        // --- CÀI ĐẶT CHỨC NĂNG YÊU THÍCH ---
        
        // Tạo nút động
        const btnYeuThich = document.createElement('button');
        btnYeuThich.classList.add('btn-yeu-thich');
        btnYeuThich.dataset.id = id; // Lưu id vào data attribute để thao tác dễ dàng
        
        // Style cơ bản cho nút bằng JS để không phải sửa CSS
        btnYeuThich.style.padding = '0.75rem 1.5rem';
        btnYeuThich.style.marginBottom = '1.5rem';
        btnYeuThich.style.cursor = 'pointer';
        btnYeuThich.style.border = '1px solid #dee2e6';
        btnYeuThich.style.borderRadius = '8px';
        btnYeuThich.style.fontWeight = 'bold';
        
        // Khôi phục trạng thái nút từ localStorage
        const danhSachHienTai = layDanhSachYeuThich();
        if (danhSachHienTai.includes(id)) {
            btnYeuThich.textContent = '❤️ Đã yêu thích';
            btnYeuThich.style.backgroundColor = '#ffebee';
            btnYeuThich.style.color = '#c62828';
        } else {
            btnYeuThich.textContent = '🤍 Thêm vào yêu thích';
            btnYeuThich.style.backgroundColor = '#ffffff';
            btnYeuThich.style.color = '#333333';
        }

        // Chèn nút dưới tiêu đề h1
        if (tieuDeEl) {
            tieuDeEl.insertAdjacentElement('afterend', btnYeuThich);
        }

        // Kỹ thuật Event Delegation: Gắn sự kiện vào phần tử cha (mainEl)
        mainEl.addEventListener('click', (e) => {
            // Kiểm tra click có rơi vào nút yêu thích không
            const btn = e.target.closest('.btn-yeu-thich');
            if (!btn) return;

            const itemId = Number(btn.dataset.id);
            // Xử lý thêm / xóa vào LocalStorage
            const dsMoi = themHoacXoaYeuThich(itemId);

            // Cập nhật lại UI của nút
            if (dsMoi.includes(itemId)) {
                btn.textContent = '❤️ Đã yêu thích';
                btn.style.backgroundColor = '#ffebee';
                btn.style.color = '#c62828';
            } else {
                btn.textContent = '🤍 Thêm vào yêu thích';
                btn.style.backgroundColor = '#ffffff';
                btn.style.color = '#333333';
            }

            // Gọi hàm cập nhật số đếm trên Header
            capNhatSoDemYeuThich();
        });

    } catch (error) {
        // --- TRẠNG THÁI: BÁO LỖI (Mất mạng / Sai đường dẫn) ---
        mainEl.removeChild(loadingMsg);
        
        const errorMsg = document.createElement('p');
        errorMsg.textContent = 'Lỗi tải dữ liệu. Vui lòng kiểm tra đường mạng hoặc thử lại sau.';
        errorMsg.style.color = 'red';
        errorMsg.style.fontWeight = 'bold';
        
        mainEl.appendChild(errorMsg);
        
        // Không cố ý throw Uncaught error để console xanh sạch đẹp
        console.warn('Đã xử lý an toàn lỗi API:', error.message);
    }
});
