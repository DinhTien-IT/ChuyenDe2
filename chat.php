<?php include 'includes/header.php'; ?>

<div class="flex flex-col min-h-screen bg-gray-100">
  <!-- Thanh tiêu đề -->
  <header class="bg-blue-600 text-white text-center py-4 shadow-lg">
    <h1 class="text-xl font-bold">💬 Trò chuyện cùng 3T Furniture</h1>
  </header>

  <!-- Khung chat -->
  <main class="flex-grow max-w-3xl mx-auto w-full bg-white shadow-md rounded-lg mt-6 mb-6 flex flex-col">
    <!-- Khu vực hiển thị tin nhắn -->
    <div id="chat-box" class="flex-1 overflow-y-auto p-4 space-y-3 bg-gray-50 rounded-t-lg">
      <!-- Tin nhắn sẽ hiển thị ở đây -->
    </div>

    <!-- Form nhập tin nhắn -->
    <div class="p-4 border-t flex items-center gap-3">
      <input id="chat-input" type="text" placeholder="Nhập tin nhắn..." 
             class="flex-grow border border-gray-300 rounded-full px-4 py-2 outline-none focus:ring-2 focus:ring-blue-400">
      <button onclick="sendMessage()" 
              class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-full transition">
        Gửi
      </button>
    </div>
  </main>
</div>

<?php include 'includes/footer.php'; ?>

<!-- Script xử lý chat -->
<script>
const chatBox = document.getElementById('chat-box');
const chatInput = document.getElementById('chat-input');

// Load tin nhắn từ localStorage khi mở trang
let messages = JSON.parse(localStorage.getItem('chatMessages')) || [];

// Hiển thị lại tin nhắn
function displayMessages() {
  chatBox.innerHTML = '';
  messages.forEach(msg => {
    const messageDiv = document.createElement('div');
    messageDiv.className = msg.sender === 'user' 
      ? 'text-right'
      : 'text-left';
    messageDiv.innerHTML = `
      <div class="inline-block px-4 py-2 rounded-lg text-sm ${
        msg.sender === 'user' 
          ? 'bg-blue-600 text-white' 
          : 'bg-gray-200 text-gray-800'
      } max-w-xs">
        ${msg.text}
      </div>
    `;
    chatBox.appendChild(messageDiv);
  });
  chatBox.scrollTop = chatBox.scrollHeight;
}

// Gửi tin nhắn
function sendMessage() {
  const text = chatInput.value.trim();
  if (!text) return;
  
  // Lưu tin nhắn người dùng
  messages.push({ sender: 'user', text });
  chatInput.value = '';

  // Hiển thị lại
  displayMessages();
  localStorage.setItem('chatMessages', JSON.stringify(messages));

  // Trả lời tự động (giả lập)
  setTimeout(() => {
    const reply = getAutoReply(text);
    messages.push({ sender: 'bot', text: reply });
    displayMessages();
    localStorage.setItem('chatMessages', JSON.stringify(messages));
  }, 800);
}

// Tạo trả lời tự động đơn giản
function getAutoReply(userText) {
  userText = userText.toLowerCase();
  if (userText.includes('giá')) return 'Bạn vui lòng cho biết tên sản phẩm để chúng tôi báo giá!';
  if (userText.includes('xin chào') || userText.includes('hello')) return 'Xin chào! Chúng tôi có thể giúp gì cho bạn?';
  if (userText.includes('địa chỉ')) return 'Địa chỉ của chúng tôi: Dị Nậu, Thạch Thất, Hà Nội.';
  return 'Cảm ơn bạn! Nhân viên sẽ phản hồi trong thời gian sớm nhất.';
}

// Hiển thị ngay khi tải trang
displayMessages();

// Gửi khi nhấn Enter
chatInput.addEventListener('keypress', (e) => {
  if (e.key === 'Enter') {
    sendMessage();
  }
});
</script>
