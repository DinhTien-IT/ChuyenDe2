<?php
session_start();
if (!isset($_SESSION['currentUser']) || strtolower($_SESSION['currentUser']['role']) !== 'admin') {
    header("Location: ../login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Chat hỗ trợ - Admin</title>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<style>
    .chat-box { height: 500px; overflow-y: scroll; }
    .message { max-width: 70%; padding: 10px 15px; border-radius: 12px; margin-bottom: 12px; }
</style>
</head>
<body class="bg-gray-100 font-sans">
<nav class="bg-blue-700 text-white p-4 flex justify-between items-center shadow">
    <h1 class="text-xl font-bold">💬 Chat hỗ trợ khách hàng</h1>
    <a href="dashboard.php" class="bg-blue-500 px-4 py-2 rounded hover:bg-blue-600">← Quay lại</a>
</nav>

<div class="max-w-3xl mx-auto mt-6 bg-white shadow rounded-lg p-6">
    <div class="chat-box bg-gray-50 rounded-lg p-4 mb-4" id="chatBox">
        <div class="message bg-blue-100 text-blue-700 self-start">Xin chào! Tôi có thể giúp gì cho bạn?</div>
    </div>
    <form id="chatForm" class="flex">
        <input type="text" id="messageInput" placeholder="Nhập tin nhắn..." 
               class="flex-1 border border-gray-300 px-4 py-2 rounded-l-lg focus:outline-none">
        <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-r-lg hover:bg-blue-700">
            Gửi
        </button>
    </form>
</div>

<script>
const chatForm = document.getElementById('chatForm');
const chatBox = document.getElementById('chatBox');
chatForm.addEventListener('submit', function(e){
    e.preventDefault();
    const msg = document.getElementById('messageInput').value.trim();
    if(msg){
        const div = document.createElement('div');
        div.className = 'message bg-blue-600 text-white ml-auto';
        div.textContent = msg;
        chatBox.appendChild(div);
        chatBox.scrollTop = chatBox.scrollHeight;
        document.getElementById('messageInput').value = '';
    }
});
</script>
</body>
</html>
