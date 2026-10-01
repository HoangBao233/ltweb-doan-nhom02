/**
 * Tệp canhan.js - Kịch bản tương tác động cho trang cá nhân Lê Thị Xuân Châu.
 * 1. Thu gọn/mở rộng (Accordion) cho danh sách kỹ năng nổi bật bằng DOM.
 * 2. Nút tạo câu châm ngôn truyền cảm hứng ngẫu nhiên (Random Quote Generator).
 * Cách kiểm tra: Bấm vào tiêu đề "Danh sách kỹ năng..." để thu gọn/mở rộng. Nhấp vào nút "💡 Tặng bạn một câu nói hay" để xem châm ngôn.
 */

document.addEventListener('DOMContentLoaded', () => {
    // ==========================================
    // Tương tác 1: Hiệu ứng Thu gọn / Mở rộng (Accordion) cho Kỹ năng
    // ==========================================
    
    // Tìm thẻ tiêu đề chứa chữ "Danh sách kỹ năng"
    const headings = document.querySelectorAll('h1, h2, h3, h4');
    let skillHeading = null;
    headings.forEach(h => {
        if (h.textContent.toLowerCase().includes('kỹ năng')) {
            skillHeading = h;
        }
    });

    if (skillHeading) {
        // Tìm danh sách (thẻ ul hoặc ol) nằm ngay sau tiêu đề đó
        const skillList = skillHeading.nextElementSibling;
        
        if (skillList && (skillList.tagName === 'UL' || skillList.tagName === 'OL')) {
            // Lấy lại text cũ
            const headingText = skillHeading.textContent;
            skillHeading.textContent = ''; // Xóa chữ tĩnh cũ
            
            // Tạo thẻ button bọc ngoài chữ để chuẩn A11y tuyệt đối
            const accordionBtn = document.createElement('button');
            accordionBtn.textContent = headingText;
            
            // Chuyển các style sang cho button và thừa kế giao diện của h3
            accordionBtn.style.cursor = 'pointer';
            accordionBtn.style.display = 'inline-flex';
            accordionBtn.style.alignItems = 'center';
            accordionBtn.style.gap = '8px';
            accordionBtn.style.userSelect = 'none';
            accordionBtn.style.background = 'none';
            accordionBtn.style.border = 'none';
            accordionBtn.style.padding = '0';
            accordionBtn.style.font = 'inherit'; // Kế thừa font h3
            accordionBtn.style.color = 'inherit';
            
            accordionBtn.setAttribute('aria-expanded', 'true');
            accordionBtn.setAttribute('title', 'Nhấn để thu gọn/mở rộng');
            
            // Thêm icon mũi tên
            const arrowIcon = document.createElement('span');
            arrowIcon.textContent = '▼';
            arrowIcon.style.fontSize = '0.7em';
            arrowIcon.style.transition = 'transform 0.3s ease';
            accordionBtn.appendChild(arrowIcon);
            
            // Đưa button vào lại trong thẻ h3
            skillHeading.appendChild(accordionBtn);

            // Set CSS transition cho danh sách
            skillList.style.transition = 'max-height 0.3s ease, opacity 0.3s ease';
            skillList.style.overflow = 'hidden';
            skillList.style.maxHeight = '500px'; 
            skillList.style.opacity = '1';

            // Gắn Sự kiện click Accordion vào thẻ BUTTON thay vì thẻ H3
            accordionBtn.addEventListener('click', () => {
                const isExpanded = accordionBtn.getAttribute('aria-expanded') === 'true';
                
                if (isExpanded) {
                    skillList.style.maxHeight = '0px';
                    skillList.style.opacity = '0';
                    arrowIcon.style.transform = 'rotate(-90deg)';
                    accordionBtn.setAttribute('aria-expanded', 'false');
                } else {
                    skillList.style.maxHeight = '500px';
                    skillList.style.opacity = '1';
                    arrowIcon.style.transform = 'rotate(0deg)';
                    accordionBtn.setAttribute('aria-expanded', 'true');
                }
            });
        }
    }

    // ==========================================
    // Tương tác 2: Trình tạo câu châm ngôn ngẫu nhiên
    // ==========================================
    
    // Kho dữ liệu châm ngôn 
    const quotes = [
        "Học, học nữa, học mãi. – V.I. Lenin",
        "Giáo dục là vũ khí mạnh nhất mà bạn có thể dùng để thay đổi thế giới. – Nelson Mandela",
        "Code sạch là code dễ đọc, dễ hiểu và dễ bảo trì. – Robert C. Martin",
        "Không có áp lực, không có kim cương. – Thomas Carlyle",
        "Bạn không cần phải hoàn hảo để bắt đầu, nhưng bạn phải bắt đầu để trở nên hoàn hảo.",
        "Cách tốt nhất để dự đoán tương lai là hãy tạo ra nó. – Abraham Lincoln",
        "Đừng chỉ học code, hãy học cách giải quyết vấn đề.",
        "Mỗi ngày mới là một cơ hội mới để thay đổi cuộc đời bạn."

    ];

    // Tạo khu vực chứa nút và câu châm ngôn
    const quoteSection = document.createElement('div');
    quoteSection.style.marginTop = '20px';
    quoteSection.style.padding = '15px';
    quoteSection.style.backgroundColor = '#f0f8ff'; // Màu nền xanh nhạt dịu mắt
    quoteSection.style.borderLeft = '4px solid #004085';
    quoteSection.style.borderRadius = '4px';
    quoteSection.style.maxWidth = '600px';

    // Tạo nút bốc thăm
    const quoteBtn = document.createElement('button');
    quoteBtn.textContent = '💡 Tặng bạn một câu nói hay';
    quoteBtn.setAttribute('aria-label', 'Nhấn để tạo câu châm ngôn ngẫu nhiên');
    
    // Style cho nút
    quoteBtn.style.padding = '8px 16px';
    quoteBtn.style.backgroundColor = '#004085';
    quoteBtn.style.color = '#fff';
    quoteBtn.style.border = 'none';
    quoteBtn.style.borderRadius = '4px';
    quoteBtn.style.cursor = 'pointer';
    quoteBtn.style.fontWeight = 'bold';
    quoteBtn.style.marginBottom = '10px';
    quoteBtn.style.transition = 'background-color 0.2s';
    
    quoteBtn.addEventListener('mouseover', () => quoteBtn.style.backgroundColor = '#002752');
    quoteBtn.addEventListener('mouseout', () => quoteBtn.style.backgroundColor = '#004085');

    // Thẻ p để hiển thị nội dung châm ngôn
    const quoteDisplay = document.createElement('p');
    quoteDisplay.textContent = 'Hãy nhấn nút phía trên để nhận một thông điệp nhé!';
    quoteDisplay.style.fontStyle = 'italic';
    quoteDisplay.style.color = '#333';
    quoteDisplay.style.margin = '0';
    quoteDisplay.style.minHeight = '24px';
    
    // A11y: Báo cho trình đọc màn hình biết khi nội dung thay đổi
    quoteDisplay.setAttribute('aria-live', 'polite'); 

    // Lắp ráp các phần tử
    quoteSection.appendChild(quoteBtn);
    quoteSection.appendChild(quoteDisplay);

    // Vị trí chèn: Dưới đoạn văn giới thiệu bản thân
    const introPara = document.querySelector('main p'); 
    if (introPara) {
        introPara.insertAdjacentElement('afterend', quoteSection);
    } else {
        const mainEl = document.querySelector('main');
        if (mainEl) mainEl.appendChild(quoteSection);
    }

    // Xử lý logic khi click nút
    quoteBtn.addEventListener('click', () => {
        // Tạo hiệu ứng mờ nhạt trước khi đổi chữ
        quoteDisplay.style.opacity = '0';
        
        setTimeout(() => {
            // Chọn ngẫu nhiên 1 câu
            const randomIndex = Math.floor(Math.random() * quotes.length);
            quoteDisplay.textContent = `"${quotes[randomIndex]}"`;
            
            // Hiện chữ lên lại
            quoteDisplay.style.transition = 'opacity 0.4s ease';
            quoteDisplay.style.opacity = '1';
        }, 200); // Đợi 200ms cho chữ chìm hẳn rồi mới hiện chữ mới
    });
});