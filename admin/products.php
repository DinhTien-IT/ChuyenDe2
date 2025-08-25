<?php
session_start();
if (!isset($_SESSION['currentUser']) || strtolower($_SESSION['currentUser']['role']) !== 'admin') {
    header("Location: ../login.php");
    exit();
}
include '../db.php';

$products = $conn->query("SELECT * FROM products ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Quản lý sản phẩm</title>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 font-sans">
<nav class="bg-blue-700 text-white p-4 flex justify-between items-center shadow">
    <h1 class="text-xl font-bold">Quản lý sản phẩm</h1>
    <a href="dashboard.php" class="bg-blue-500 px-4 py-2 rounded hover:bg-blue-600">← Quay lại</a>
</nav>

<div class="max-w-7xl mx-auto p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-blue-700">Danh sách sản phẩm</h2>
        <a href="add_product.php" class="bg-blue-600 text-white px-5 py-2 rounded hover:bg-blue-700">
            <i class="fas fa-plus"></i> Thêm sản phẩm
        </a>
    </div>

    <div class="bg-white rounded-lg shadow overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead class="bg-blue-100">
                <tr>
                    <th class="p-3">ID</th>
                    <th class="p-3">Tên sản phẩm</th>
                    <th class="p-3">Giá</th>
                    <th class="p-3">Hình ảnh</th>
                    <th class="p-3">Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $products->fetch_assoc()): ?>
                <tr class="border-b hover:bg-blue-50">
                    <td class="p-3"><?= $row['id'] ?></td>
                    <td class="p-3"><?= htmlspecialchars($row['name']) ?></td>
                    <td class="p-3"><?= number_format($row['price'], 0, ',', '.') ?>₫</td>
                    <td class="p-3">
                        <img src="../uploads/<?= $row['image'] ?>" alt="" class="w-16 h-16 object-cover rounded">
                    </td>
                    <td class="p-3">
                        <a href="edit_product.php?id=<?= $row['id'] ?>" class="text-blue-600 hover:underline mr-3"><i class="fas fa-edit"></i></a>
                        <a href="delete_product.php?id=<?= $row['id'] ?>" onclick="return confirm('Xóa sản phẩm này?')" class="text-red-600 hover:underline"><i class="fas fa-trash"></i></a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
