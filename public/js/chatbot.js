if (!document.querySelector("#chatbot-toggler")) {
    // Trang này không có chatbot
} else {

const BASE_URL = window.BASE_URL || '';
const chatBody = document.querySelector(".chat-body");
const messageInput = document.querySelector(".message-input");
const sendMessageButton = document.querySelector("#send-message");
const fileInput = document.querySelector("#file-input");
const fileUploadWrapper = document.querySelector(".file-upload-wrapper");
const fileCancelButton = document.querySelector("#file-cancel");
const chatbotToggler = document.querySelector("#chatbot-toggler");
const closeChatbot = document.querySelector("#close-chatbot");

const API_KEY = "AIzaSyDuuOPxJEFNPkwsg1zecPbWbj48qJA7U0I";
const GEMINI_URL = `https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=${API_KEY}`;

const userData = {
    message: null,
    file: { data: null, mime_type: null }
};

// Cache sản phẩm từ API
let productCache = [];

// Lấy danh sách sản phẩm từ API
async function fetchProducts() {
    if (productCache.length > 0) return productCache;
    try {
        const res = await fetch(BASE_URL + '/Api/products?limit=50');
        const json = await res.json();
        if (json.success) {
            productCache = json.data.products || [];
        }
    } catch (e) {
        console.warn('Không thể tải sản phẩm:', e);
    }
    return productCache;
}

// Tìm sản phẩm theo keyword
async function searchProducts(keyword) {
    try {
        const res = await fetch(BASE_URL + '/Api/search?keyword=' + encodeURIComponent(keyword));
        const json = await res.json();
        if (json.success) return json.data.results || [];
    } catch (e) {}
    return [];
}

// Tạo system prompt với danh sách sản phẩm
async function buildSystemPrompt() {
    const products = await fetchProducts();
    let productInfo = '';
    if (products.length > 0) {
        productInfo = '\n\nDanh sách sản phẩm hiện có:\n';
        products.slice(0, 30).forEach(p => {
            productInfo += `- ${p.name} (ID: ${p.id}): ${Number(p.price).toLocaleString('vi-VN')}đ`;
            if (p.description) productInfo += ` - ${String(p.description).substring(0, 80)}`;
            productInfo += '\n';
        });
    }

    return `Bạn là trợ lý tư vấn mỹ phẩm của cửa hàng "The Boys" - chuyên bán mỹ phẩm chất lượng cao.
Nhiệm vụ:
- Tư vấn sản phẩm mỹ phẩm phù hợp với nhu cầu khách hàng (dưỡng da, trang điểm, chăm sóc tóc...).
- Giới thiệu sản phẩm cụ thể từ danh sách bên dưới khi phù hợp.
- Trả lời câu hỏi về thành phần, cách dùng, loại da phù hợp.
- Không tự xưng là AI hay chatbot, xưng là "em" với khách.
- Nếu hỏi ngoài chủ đề mỹ phẩm, khéo léo chuyển về tư vấn sản phẩm.
- Trả lời ngắn gọn, thân thiện, dùng emoji phù hợp.
- Khi đề xuất sản phẩm, chỉ đề xuất sản phẩm có trong danh sách.

Thông tin cửa hàng:
- Tên: The Boys Cosmetics
- Địa chỉ: TP.Hồ Chí Minh
- Hotline: 0123 456 789
- Chính sách: Miễn phí ship đơn trên 300k, đổi trả trong 7 ngày.
${productInfo}`;
}

const chatHistory = [];
let systemPrompt = '';

// Khởi tạo system prompt
buildSystemPrompt().then(prompt => {
    systemPrompt = prompt;
    chatHistory.push({
        role: "model",
        parts: [{ text: systemPrompt }]
    });
});

const initialInputHeight = messageInput.scrollHeight;

const createMessageElement = (content, ...classes) => {
    const div = document.createElement("div");
    div.classList.add("message", ...classes);
    div.innerHTML = content;
    return div;
};

// Tìm sản phẩm liên quan trong câu trả lời và tạo link
async function buildProductLinks(userMsg, botResponse) {
    const combined = (userMsg + ' ' + botResponse).toLowerCase();
    const products = await fetchProducts();
    const links = [];
    const seen = new Set();

    for (const p of products) {
        const name = p.name.toLowerCase();
        if (combined.includes(name) && !seen.has(p.id)) {
            links.push(`<a href="${BASE_URL}/Product/show/${p.id}" target="_blank" style="color:#e91e8c;">🛍️ ${p.name} - ${Number(p.price).toLocaleString('vi-VN')}đ</a>`);
            seen.add(p.id);
            if (links.length >= 3) break;
        }
    }
    return links;
}

const generateBotResponse = async (incomingMessageDiv) => {
    const messageElement = incomingMessageDiv.querySelector(".message-text");

    chatHistory.push({
        role: "user",
        parts: [
            { text: userData.message },
            ...(userData.file.data ? [{ inline_data: userData.file }] : [])
        ],
    });

    try {
        const response = await fetch(GEMINI_URL, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ contents: chatHistory })
        });

        const data = await response.json();
        if (!response.ok) throw new Error(data.error?.message || 'Lỗi API');

        const rawText = data.candidates[0].content.parts[0].text
            .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
            .trim();

        chatHistory.push({ role: "model", parts: [{ text: rawText }] });

        // Tìm link sản phẩm liên quan
        const productLinks = await buildProductLinks(userData.message, rawText);

        let displayHtml = rawText.replace(/\n/g, '<br>');
        if (productLinks.length > 0) {
            displayHtml += '<br><br><div style="font-size:13px;border-top:1px solid #f0c;padding-top:8px;margin-top:4px;">'
                + '✨ Sản phẩm gợi ý:<br>'
                + productLinks.join('<br>')
                + '</div>';
        }

        messageElement.innerHTML = displayHtml;

    } catch (error) {
        messageElement.innerHTML = `<span style="color:#ff4444">Xin lỗi, em đang gặp sự cố. Vui lòng thử lại sau! 🙏</span>`;
        console.error(error);
    } finally {
        userData.file = { data: null, mime_type: null };
        incomingMessageDiv.classList.remove("thinking");
        chatBody.scrollTo({ behavior: "smooth", top: chatBody.scrollHeight });
    }
};

const handleOutgoingMessage = (e) => {
    e.preventDefault();
    userData.message = messageInput.value.trim();
    if (!userData.message) return;

    messageInput.value = "";
    fileUploadWrapper.classList.remove("file-uploaded");
    messageInput.dispatchEvent(new Event("input"));

    const outgoingDiv = createMessageElement(
        `<div class="message-text"></div>${userData.file.data ? `<img src="data:${userData.file.mime_type};base64,${userData.file.data}" class="attachment" />` : ""}`,
        "user-message"
    );
    outgoingDiv.querySelector(".message-text").innerText = userData.message;
    chatBody.appendChild(outgoingDiv);
    chatBody.scrollTop = chatBody.scrollHeight;

    setTimeout(() => {
        const incomingDiv = createMessageElement(
            `<img class="bot-avatar" src="${BASE_URL}/public/images/CSKH.png" />
            <div class="message-text">
                <div class="thinking-indicator">
                    <div class="dot"></div><div class="dot"></div><div class="dot"></div>
                </div>
            </div>`,
            "bot-message", "thinking"
        );
        chatBody.appendChild(incomingDiv);
        chatBody.scrollTo({ behavior: "smooth", top: chatBody.scrollHeight });
        generateBotResponse(incomingDiv);
    }, 600);
};

// Events
messageInput.addEventListener("keydown", (e) => {
    if (e.key === "Enter" && messageInput.value.trim() && !e.shiftKey && window.innerWidth > 768) {
        handleOutgoingMessage(e);
    }
});

messageInput.addEventListener("input", () => {
    messageInput.style.height = `${initialInputHeight}px`;
    messageInput.style.height = `${messageInput.scrollHeight}px`;
});

fileInput.addEventListener("change", (e) => {
    const file = e.target.files[0];
    if (!file) return;
    const validTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    if (!validTypes.includes(file.type)) {
        alert('Chỉ chấp nhận file ảnh (JPEG, PNG, GIF, WEBP)');
        fileInput.value = "";
        return;
    }
    const reader = new FileReader();
    reader.onload = (ev) => {
        fileUploadWrapper.querySelector("img").src = ev.target.result;
        fileUploadWrapper.classList.add("file-uploaded");
        userData.file = {
            data: ev.target.result.split(",")[1],
            mime_type: file.type
        };
        fileInput.value = "";
    };
    reader.readAsDataURL(file);
});

fileCancelButton.addEventListener("click", () => {
    userData.file = { data: null, mime_type: null };
    fileUploadWrapper.classList.remove("file-uploaded");
});

sendMessageButton.addEventListener("click", handleOutgoingMessage);
document.querySelector("#file-upload")?.addEventListener("click", () => fileInput.click());
chatbotToggler.addEventListener("click", () => document.body.classList.toggle("show-chatbot"));
closeChatbot.addEventListener("click", () => document.body.classList.remove("show-chatbot"));

// Emoji picker
try {
    const picker = new EmojiMart.Picker({
        theme: "light",
        showSkinTones: "none",
        previewPosition: "none",
        onEmojiSelect: (emoji) => {
            const { selectionStart: start, selectionEnd: end } = messageInput;
            messageInput.setRangeText(emoji.native, start, end, "end");
            messageInput.focus();
        },
        onClickOutside: (ev) => {
            if (ev.target.id === "emoji-picker") {
                document.body.classList.toggle("show-emoji-picker");
            } else {
                document.body.classList.remove("show-emoji-picker");
            }
        },
    });
    document.querySelector(".chat-form").appendChild(picker);
} catch(e) {}

} // end chatbot guard
