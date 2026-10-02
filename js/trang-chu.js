/**
 * js/trang-chu.js - Kịch bản riêng cho Trang chủ
 * 
 * Chức năng 5 (Bảng 1): Fetch API dữ liệu thời tiết Đà Nẵng
 * từ open-meteo.com. Xử lý 3 trạng thái: Đang tải, Lỗi, Thành công.
 */

import { taiDuLieuJSON } from './api.js';

const loiThoiTiet = document.getElementById('loi-thoi-tiet');
const nutTaiLai = document.getElementById('nut-tai-lai-thoi-tiet');

// API Thời tiết mở (Meteo) cho Đà Nẵng
const thamSo = new URLSearchParams({ 
    latitude: 16.05, 
    longitude: 108.2, 
    current: 'temperature_2m', 
    timezone: 'auto' 
});
const API_WEATHER = 'https://api.open-meteo.com/v1/forecast?' + thamSo;

async function hienThiThoiTiet() {
    // 1. Trạng thái Đang tải
    loiThoiTiet.textContent = 'Đang đồng bộ dữ liệu...';
    nutTaiLai.style.display = 'none';

    try {
        // 2. Fetch API an toàn
        const data = await taiDuLieuJSON(API_WEATHER);
        
        // 3. Trạng thái Thành công
        if (data && data.current && data.current.temperature_2m) {
            const nhietDo = data.current.temperature_2m;
            loiThoiTiet.textContent = `Nhiệt độ: ${nhietDo}°C - Thời tiết đẹp để học tập!`;
        } else {
            loiThoiTiet.textContent = 'Chưa có thông tin thời tiết lúc này.';
        }
        
    } catch (error) {
        // 4. Trạng thái Lỗi
        console.error('Lỗi khi tải thời tiết:', error);
        loiThoiTiet.textContent = 'Lỗi kết nối mạng. Không thể tải thời tiết.';
        nutTaiLai.style.display = 'inline-block';
    }
}

// Chạy lần đầu khi load trang
hienThiThoiTiet();

if (nutTaiLai) {
    nutTaiLai.addEventListener('click', hienThiThoiTiet);
}

