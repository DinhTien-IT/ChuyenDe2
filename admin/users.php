<?php
session_start();
if (!isset($_SESSION['currentUser']) || strtolower($_SESSION['currentUser']['role']) !== 'admin') {
    header("Location: ../login.php");
    exit();
}
include '../db.php';

$users = $conn->query("SELECT * FROM users ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Quản lý người dùng</title>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 font-sans">
<nav class="bg-blue-700 text-white p-4 flex justify-between items-center shadow">
    <h1 class="text-xl font-bold">Quản lý người dùng</h1>
    <a href="dashboard.php" class="bg-blue-500 px-4 py-2 rounded hover:bg-blue-600">← Quay lại</a>
</nav>

<div class="max-w-7xl mx-auto p-6">
    <h2 class="text-2xl font-bold mb-6 text-blue-700">Danh sách người dùng</h2>
    <div class="bg-white rounded-lg shadow overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead class="bg-blue-100">
                <tr>
                    <th class="p-3">ID</th>
                    <th class="p-3">Tên đăng nhập</th>
                    <th class="p-3">Email</th>
                    <th class="p-3">Vai trò</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $users->fetch_assoc()): ?>
                <tr class="border-b hover:bg-blue-50">
                    <td class="p-3"><?= $row['id'] ?></td>
                    <td class="p-3"><?= htmlspecialchars($row['username']) ?></td>
                    <td class="p-3"><?= htmlspecialchars($row['email']) ?></td>
                    <td class="p-3">
                        <span class="px-3 py-1 rounded text-white 
                            <?= strtolower($row['role'])=='admin'?'bg-blue-600':'bg-blue-300' ?>">
                            <?= ucfirst($row['role']) ?>
                        </span>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
