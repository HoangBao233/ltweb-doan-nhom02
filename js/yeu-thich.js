// js/yeu-thich.js
// Module xử lý chức năng lưu trữ yêu thích (localStorage)

const KHOA_LUU_TRU = 'danh_sach_yeu_thich';

// Lấy danh sách từ localStorage
export function layDanhSachYeuThich() {
    const duLieu = localStorage.getItem(KHOA_LUU_TRU);
    return duLieu ? JSON.parse(duLieu) : [];
}

// Lưu danh sách vào localStorage
function luuDanhSachYeuThich(danhSach) {
    localStorage.setItem(KHOA_LUU_TRU, JSON.stringify(danhSach));
}

// Thêm hoặc xóa một ID khỏi danh sách
export function themHoacXoaYeuThich(id) {
    let danhSach = layDanhSachYeuThich();
    const viTri = danhSach.indexOf(id);
    if (viTri > -1) {
        danhSach.splice(viTri, 1); // Đã có -> Xóa
    } else {
        danhSach.push(id); // Chưa có -> Thêm
    }
    luuDanhSachYeuThich(danhSach);
    return danhSach;
}

// Hàm hiển thị / cập nhật số đếm trên Header
export function capNhatSoDemYeuThich() {
    const danhSach = layDanhSachYeuThich();
    let theDem = document.getElementById('dem-yeu-thich');
    
    // Nếu chưa có thẻ đếm, tạo mới tự động và chèn vào header
    if (!theDem) {
        const header = document.querySelector('.trang__dau');
        if (header) {
            theDem = document.createElement('span');
            theDem.id = 'dem-yeu-thich';
            
            // Một chút CSS nội tuyến để nổi bật (có thể chuyển vào file CSS)
            theDem.style.marginLeft = '1rem';
            theDem.style.fontWeight = 'bold';
            theDem.style.backgroundColor = '#ffc107';
            theDem.style.color = '#000';
            theDem.style.padding = '0.2rem 0.5rem';
            theDem.style.borderRadius = '4px';
            
            header.appendChild(theDem);
        }
    }
    
    // Cập nhật nội dung số đếm
    if (theDem) {
        theDem.textContent = `❤️ Đã lưu: ${danhSach.length}`;
    }
}