<?php
session_start();
if (!isset($_SESSION['currentUser']) || strtolower($_SESSION['currentUser']['role']) !== 'admin') {
    header("Location: ../login.php");
    exit();
}
include '../db.php';

// Lấy dữ liệu thống kê
$totalUsers = $conn->query("SELECT COUNT(*) AS total FROM users")->fetch_assoc()['total'] ?? 0;
$totalProducts = $conn->query("SELECT COUNT(*) AS total FROM products")->fetch_assoc()['total'] ?? 0;
$totalOrders = $conn->query("SELECT COUNT(*) AS total FROM orders")->fetch_assoc()['total'] ?? 0;
$totalRevenue = $conn->query("SELECT SUM(total_price) AS revenue FROM orders")->fetch_assoc()['revenue'] ?? 0;
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Dashboard</title>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
    .card-hover:hover { transform: translateY(-6px); box-shadow: 0 10px 25px rgba(0,0,0,0.15); }
</style>
</head>
<body class="bg-gray-100 font-sans min-h-screen">

<!-- Navbar -->
<nav class="bg-blue-700 text-white shadow-md">
    <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
        <h1 class="text-2xl font-bold">📊 Admin Dashboard</h1>
        <div class="flex items-center space-x-6">
            <a href="../index.php" class="hover:underline">Trang chủ</a>
            <a href="../logout.php" class="bg-blue-500 px-4 py-2 rounded hover:bg-blue-600">Đăng xuất</a>
        </div>
    </div>
</nav>

<!-- Nội dung chính -->
<main class="max-w-7xl mx-auto p-8">
    <!-- Thống kê -->
    <h2 class="text-4xl font-extrabold mb-8 text-blue-700">Tổng quan hệ thống</h2>
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <!-- Người dùng -->
        <div class="bg-blue-600 text-white p-6 rounded-xl shadow-lg card-hover">
            <div class="flex justify-between items-center">
                <div><p>Người dùng</p><p class="text-4xl font-bold"><?= $totalUsers ?></p></div>
                <i class="fas fa-users text-4xl opacity-70"></i>
            </div>
        </div>
        <!-- Sản phẩm -->
        <div class="bg-blue-500 text-white p-6 rounded-xl shadow-lg card-hover">
            <div class="flex justify-between items-center">
                <div><p>Sản phẩm</p><p class="text-4xl font-bold"><?= $totalProducts ?></p></div>
                <i class="fas fa-box-open text-4xl opacity-70"></i>
            </div>
        </div>
        <!-- Đơn hàng -->
        <div class="bg-blue-400 text-white p-6 rounded-xl shadow-lg card-hover">
            <div class="flex justify-between items-center">
                <div><p>Đơn hàng</p><p class="text-4xl font-bold"><?= $totalOrders ?></p></div>
                <i class="fas fa-shopping-cart text-4xl opacity-70"></i>
            </div>
        </div>
        <!-- Doanh thu -->
        <div class="bg-blue-300 text-white p-6 rounded-xl shadow-lg card-hover">
            <div class="flex justify-between items-center">
                <div><p>Doanh thu</p><p class="text-2xl font-bold"><?= number_format($totalRevenue, 0, ',', '.') ?>₫</p></div>
                <i class="fas fa-dollar-sign text-4xl opacity-70"></i>
            </div>
        </div>
    </div>

    <!-- Chức năng quản lý -->
    <div class="mt-14">
        <h3 class="text-2xl font-semibold mb-6 text-blue-700">Chức năng quản lý</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <a href="products.php" class="bg-white border border-blue-200 p-8 rounded-xl shadow text-center font-semibold text-blue-700 hover:bg-blue-50 card-hover">
                <i class="fas fa-box-open text-3xl mb-3"></i><p>Quản lý sản phẩm</p>
            </a>
            <a href="orders.php" class="bg-white border border-blue-200 p-8 rounded-xl shadow text-center font-semibold text-blue-700 hover:bg-blue-50 card-hover">
                <i class="fas fa-receipt text-3xl mb-3"></i><p>Quản lý đơn hàng</p>
            </a>
            <a href="users.php" class="bg-white border border-blue-200 p-8 rounded-xl shadow text-center font-semibold text-blue-700 hover:bg-blue-50 card-hover">
                <i class="fas fa-user-cog text-3xl mb-3"></i><p>Quản lý người dùng</p>
            </a>
        </div>
    </div>

    <!-- Các nút bổ sung -->
    <div class="mt-14 flex flex-wrap gap-4">
        <a href="add_product.php" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 font-semibold">
            <i class="fas fa-plus-circle"></i> Thêm sản phẩm
        </a>
        <a href="export_revenue.php" class="bg-blue-500 text-white px-6 py-3 rounded-lg hover:bg-blue-600 font-semibold">
            <i class="fas fa-file-export"></i> Xuất báo cáo doanh thu
        </a>
        <a href="chat.php" class="bg-blue-400 text-white px-6 py-3 rounded-lg hover:bg-blue-500 font-semibold">
            <i class="fas fa-comments"></i> Chat hỗ trợ
        </a>
    </div>
</main>
</body>
</html>
