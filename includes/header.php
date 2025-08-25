<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$user = $_SESSION['currentUser'] ?? null;
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>3T Furniture</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" href="assets/images/logo.png" type="image/png">
</head>
<body class="bg-gray-100 font-sans min-h-screen flex flex-col">
<!-- HEADER -->
<nav class="bg-blue-600 text-white shadow-md sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 py-3 flex justify-between items-center">
        <a href="index.php" class="text-2xl font-bold">3T Furniture</a>
        <div>
            <?php if ($user): ?>
                <div class="relative inline-block">
                    <img src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png" 
                         class="w-8 h-8 rounded-full cursor-pointer border border-white" 
                         onclick="toggleMenu()">
                    <div id="menu" class="hidden absolute right-0 mt-2 w-48 bg-white text-gray-700 border rounded shadow text-sm">
                        <a href="account.php" class="block px-4 py-2 hover:bg-gray-100">Thông tin tài khoản</a>
                        <a href="address.php" class="block px-4 py-2 hover:bg-gray-100">Sổ địa chỉ</a>
                        <a href="orders.php" class="block px-4 py-2 hover:bg-gray-100">Đơn hàng</a>
                        <?php if ($user['role'] === 'admin'): ?>
                            <a href="admin/dashboard.php" class="block px-4 py-2 hover:bg-gray-100">Quản trị</a>
                        <?php endif; ?>
                        <a href="logout.php" class="block px-4 py-2 text-red-600 hover:bg-gray-100">Đăng xuất</a>
                    </div>
                </div>
            <?php else: ?>
                <a href="login.php" class="text-white hover:text-gray-200 font-semibold">Đăng nhập</a>
            <?php endif; ?>
        </div>
    </div>
</nav>
<script>
function toggleMenu(){
    document.getElementById('menu').classList.toggle('hidden');
}
</script>
