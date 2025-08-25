<?php
// Bắt đầu session nếu chưa có
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Kiểm tra đăng nhập
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: ../login.php"); // Quay về login ngoài admin
    exit();
}

// Lấy tên admin nếu có
$fullName = isset($_SESSION['fullName']) ? $_SESSION['fullName'] : 'Admin';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - 3T Furniture</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-100 flex">

    <!-- Sidebar -->
    <aside class="w-64 bg-blue-900 text-white min-h-screen p-5 fixed">
        <h1 class="text-2xl font-bold mb-10 text-center">3T Admin</h1>
        <nav class="space-y-4">
            <a href="dashboard.php" class="flex items-center gap-2 px-4 py-2 hover:bg-blue-700 rounded transition">
                📊 <span>Bảng điều khiển</span>
            </a>
            <a href="products.php" class="flex items-center gap-2 px-4 py-2 hover:bg-blue-700 rounded transition">
                📦 <span>Sản phẩm</span>
            </a>
            <a href="orders.php" class="flex items-center gap-2 px-4 py-2 hover:bg-blue-700 rounded transition">
                🛒 <span>Đơn hàng</span>
            </a>
            <a href="users.php" class="flex items-center gap-2 px-4 py-2 hover:bg-blue-700 rounded transition">
                👤 <span>Người dùng</span>
            </a>
            <a href="chat.php" class="flex items-center gap-2 px-4 py-2 hover:bg-blue-700 rounded transition">
                💬 <span>Chat</span>
            </a>
            <a href="report.php" class="flex items-center gap-2 px-4 py-2 hover:bg-blue-700 rounded transition">
                📈 <span>Xuất doanh thu</span>
            </a>
        </nav>
    </aside>

    <!-- Nội dung chính -->
    <div class="flex-1 flex flex-col ml-64">
        <!-- Header -->
        <header class="bg-white shadow-md px-6 py-4 flex justify-between items-center">
            <h2 class="text-lg font-semibold">Xin chào, <?= htmlspecialchars($fullName) ?></h2>
            <div class="flex items-center gap-4">
                <div class="bg-gray-200 text-gray-700 px-3 py-1 rounded-lg">
                    <span>Admin Panel</span>
                </div>
                <a href="logout.php" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 transition">Đăng xuất</a>
            </div>
        </header>

        <!-- Main Content -->
        <main class="p-6">
            <!-- Trang con sẽ bắt đầu nội dung tại đây -->
                     </main>
    </div>
</body>
</html>

