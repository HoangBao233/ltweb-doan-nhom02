/**
 * js/trang-chi-tiet.js
 * 
 * [BƯỚC 5 — KIỂM TRA JAVASCRIPT CÒN GIỮ GÌ, BỎ GÌ (HuongDan_BaiTap5_PHP.md)]
 * Yêu cầu: "Xoá / vô hiệu hoá: Toàn bộ js/trang-chi-tiet.js — PHP đã thay thế"
 * 
 * LƯU Ý: Toàn bộ logic hiển thị chi tiết giáo án dựa theo ID từ URL trước đây trong
 * file này đã được chuyển giao hoàn toàn sang PHP xử lý Server-Side Rendering (SSR)
 * tại chi-tiet-giao-an.php (Chức năng 3).
 * 
 * Toàn bộ mã nguồn phía dưới đã được comment out (vô hiệu hóa) để đảm bảo an toàn,
 * tránh xung đột logic và giữ lại để tham khảo khi cần thiết.
 * Bản chất hiện tại: Website KHÔNG còn sử dụng file này nữa.
 */

// import { taiDuLieuJSON } from './api.js';
// import { themHoacXoaYeuThich, layDanhSachYeuThich } from './yeu-thich.js';

// const vungChiTiet = document.getElementById('vung-chi-tiet');
// const thongBaoLoi = document.getElementById('thong-bao-loi');

// const ctTieuDe = document.getElementById('ct-tieu-de');
// const ctMoTa = document.getElementById('ct-mo-ta');
// const ctAnh = document.getElementById('ct-anh');
// const ctCapHoc = document.getElementById('ct-cap-hoc');
// const ctGia = document.getElementById('ct-gia');
// const btnLuu = document.getElementById('btn-luu-chi-tiet');

// // 1. Đọc ID từ thanh địa chỉ (URL)
// const urlParams = new URLSearchParams(window.location.search);
// const idThamSo = Number(urlParams.get('id'));

// // Hàm hiển thị giao diện nút Yêu thích
// function capNhatNutYeuThich(idGiaoAn) {
//     const dsYeuThich = layDanhSachYeuThich();
//     const daLuu = dsYeuThich.includes(idGiaoAn.toString());
//     
//     btnLuu.style.backgroundColor = daLuu ? '#28a745' : '';
//     btnLuu.style.color = daLuu ? '#fff' : '';
//     btnLuu.textContent = daLuu ? '🔖 Đã lưu' : '🔖 Lưu bài học';
// }

// async function hienThiChiTiet() {
//     try {
//         // 2. Fetch toàn bộ dữ liệu
//         const danhSach = await taiDuLieuJSON('data/giao-an.json');
//         
//         // 3. Tìm kiếm theo ID (hàm find)
//         const baiHoc = danhSach.find(item => item.id === idThamSo);

//         if (!baiHoc) {
//             // Trường hợp không tìm thấy (ID sai hoặc không truyền ID)
//             vungChiTiet.style.display = 'none';
//             thongBaoLoi.textContent = 'Lỗi 404: Không tìm thấy bài học này trong hệ thống!';
//             thongBaoLoi.style.display = 'block';
//             document.title = 'Không tìm thấy - ITeduShare';
//             return;
//         }

//         // Trường hợp tìm thấy: Cập nhật DOM (An toàn với textContent)
//         document.title = `${baiHoc.ten} - ITeduShare`; // Gợi ý 2: Đổi title
//         
//         ctTieuDe.textContent = baiHoc.ten;
//         ctMoTa.textContent = baiHoc.mo_ta;
//         ctCapHoc.textContent = baiHoc.cap_hoc;
//         ctGia.textContent = baiHoc.gia === 0 ? 'Miễn phí' : baiHoc.gia.toLocaleString('vi-VN') + ' VNĐ';
//         
//         ctAnh.src = baiHoc.hinh_anh;
//         ctAnh.alt = baiHoc.ten;
//         ctAnh.style.display = 'block';

//         // Xử lý Video
//         const khungVideo = document.getElementById('khung-video');
//         const ctVideo = document.getElementById('ct-video');
//         if (baiHoc.video_url && khungVideo && ctVideo) {
//             ctVideo.src = baiHoc.video_url;
//             khungVideo.style.display = 'block';
//         }

//         // Xử lý nút Lưu Yêu thích
//         btnLuu.style.display = 'inline-block';
//         capNhatNutYeuThich(baiHoc.id);

//         btnLuu.addEventListener('click', () => {
//             // Thêm/Xóa vào localStorage
//             themHoacXoaYeuThich(baiHoc.id.toString());
//             
//             // Cập nhật lại giao diện nút
//             capNhatNutYeuThich(baiHoc.id);
//             
//             // Phát sự kiện để báo cho thanh Header (main.js) nhảy số đếm
//             window.dispatchEvent(new Event('capNhatYeuThich'));
//         });

//     } catch (error) {
//         console.error('Lỗi khi tải chi tiết:', error);
//         vungChiTiet.style.display = 'none';
//         thongBaoLoi.textContent = 'Lỗi mạng: Không thể tải dữ liệu. Vui lòng thử lại sau.';
//         thongBaoLoi.style.display = 'block';
//     }
// }

// document.addEventListener('DOMContentLoaded', hienThiChiTiet);

/*
 * --- KẾT THÚC VÔ HIỆU HÓA js/trang-chi-tiet.js ---
 * File này được comment out toàn bộ để đảm bảo an toàn cho hệ thống (bản chất là không dùng nữa).
 */

