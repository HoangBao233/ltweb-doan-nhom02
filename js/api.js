// js/api.js
// Module xử lý gọi API chung cho toàn website

export async function taiDuLieuJSON(url) {
    const res = await fetch(url);
    if (!res.ok) {
        throw new Error(`Lỗi tải dữ liệu: HTTP ${res.status}`);
    }
    return res.json();
}
