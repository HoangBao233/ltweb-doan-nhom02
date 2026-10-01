// js/yeu-thich.js
// Module quản lý danh sách học liệu yêu thích trong localStorage.
// Cung cấp các hàm đọc, lưu, thêm/bỏ mục yêu thích và đếm số lượng.

const KHOA_LUU_TRU = 'danhSachYeuThich';

export function docDanhSachYeuThich() {
    try {
        const chuoiDuLieu = localStorage.getItem(KHOA_LUU_TRU);
        if (chuoiDuLieu !== null) {
            const danhSach = JSON.parse(chuoiDuLieu);
            if (Array.isArray(danhSach) === true) {
                return danhSach;
            }
        }
    } catch (loi) {
        console.error('Lỗi đọc localStorage:', loi);
    }
    return [];
}

export function luuDanhSachYeuThich(danhSach) {
    try {
        localStorage.setItem(KHOA_LUU_TRU, JSON.stringify(danhSach));
        window.dispatchEvent(new Event('capNhatYeuThich'));
    } catch (loi) {
        console.error('Lỗi lưu localStorage:', loi);
    }
}

export function chuyenDoiYeuThich(idMuc) {
    const idSo = Number(idMuc);
    const danhSach = docDanhSachYeuThich();
    const viTri = danhSach.indexOf(idSo);
    if (viTri === -1) {
        danhSach.push(idSo);
    } else {
        danhSach.splice(viTri, 1);
    }
    luuDanhSachYeuThich(danhSach);
    return viTri === -1;
}