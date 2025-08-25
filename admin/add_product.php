<?php
session_start();
if (!isset($_SESSION['currentUser']) || strtolower($_SESSION['currentUser']['role']) !== 'admin') {
    header("Location: ../login.php");
    exit();
}

include '../db.php';
$msg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $price = floatval($_POST['price']);
    $description = trim($_POST['description']); // ✅ Thêm mô tả
    $image = "";

    // Kiểm tra upload ảnh
    if (!empty($_FILES['image']['name'])) {
        $targetDir = "../uploads/";
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $fileName = time() . '_' . basename($_FILES['image']['name']);
        $targetFile = $targetDir . $fileName;
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($imageFileType, $allowedTypes)) {
            $msg = "❌ Chỉ chấp nhận ảnh JPG, JPEG, PNG, GIF.";
        } elseif ($_FILES['image']['size'] > 5 * 1024 * 1024) {
            $msg = "❌ Ảnh không được lớn hơn 5MB.";
        } else {
            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
                $image = $fileName;
            } else {
                $msg = "❌ Lỗi khi upload ảnh.";
            }
        }
    }

    // Lưu DB
    if ($msg === "") {
        $stmt = $conn->prepare("INSERT INTO products (name, price, image, description) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sdss", $name, $price, $image, $description);

        if ($stmt->execute()) {
            $msg = "✅ Thêm sản phẩm thành công!";
        } else {
            $msg = "❌ Lỗi DB: " . $conn->error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Thêm sản phẩm - Admin</title>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 font-sans">
<nav class="bg-blue-700 text-white p-4 flex justify-between items-center shadow">
    <h1 class="text-xl font-bold">➕ Thêm sản phẩm</h1>
    <a href="products.php" class="bg-blue-500 px-4 py-2 rounded hover:bg-blue-600">← Danh sách sản phẩm</a>
</nav>

<div class="max-w-2xl mx-auto mt-6 bg-white shadow rounded-lg p-6">
    <?php if($msg): ?>
        <p class="text-center mb-4 font-semibold <?= strpos($msg, '✅') !== false ? 'text-green-600' : 'text-red-600' ?>">
            <?= htmlspecialchars($msg) ?>
        </p>
    <?php endif; ?>
    <form method="POST" enctype="multipart/form-data" class="space-y-6">
        <div>
            <label class="block font-semibold mb-1">Tên sản phẩm</label>
            <input type="text" name="name" required class="w-full border px-4 py-3 rounded-lg">
        </div>
        <div>
            <label class="block font-semibold mb-1">Giá sản phẩm</label>
            <input type="number" step="0.01" name="price" required class="w-full border px-4 py-3 rounded-lg">
        </div>
        <div>
            <label class="block font-semibold mb-1">Mô tả sản phẩm</label>
            <textarea name="description" rows="4" class="w-full border px-4 py-3 rounded-lg" placeholder="Nhập thông tin chi tiết..."></textarea>
        </div>
        <div>
            <label class="block font-semibold mb-1">Hình ảnh</label>
            <input type="file" name="image" accept="image/*" required class="w-full">
        </div>
        <button type="submit" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 font-semibold">
            <i class="fas fa-save"></i> Lưu sản phẩm
        </button>
    </form>
</div>
</body>
</html>
