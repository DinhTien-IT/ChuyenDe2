<?php
session_start();
if (!isset($_SESSION['currentUser']) || strtolower($_SESSION['currentUser']['role']) !== 'admin') {
    header("Location: ../login.php");
    exit();
}

include '../db.php';
$msg = "";

if (!isset($_GET['id'])) {
    header("Location: products.php");
    exit();
}

$id = intval($_GET['id']);

// Lấy thông tin sản phẩm
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $price = floatval($_POST['price']);
    $description = trim($_POST['description']);
    $image = $product['image']; // giữ ảnh cũ nếu không upload mới

    // Xử lý upload ảnh mới
    if (!empty($_FILES['image']['name'])) {
        $targetDir = "../uploads/";
        $fileName = time() . '_' . basename($_FILES['image']['name']);
        $targetFile = $targetDir . $fileName;
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($imageFileType, $allowedTypes)) {
            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
                if ($image && file_exists("../uploads/" . $image)) {
                    unlink("../uploads/" . $image); // xóa ảnh cũ
                }
                $image = $fileName;
            }
        }
    }

    // Cập nhật DB
    $stmt = $conn->prepare("UPDATE products SET name=?, price=?, description=?, image=? WHERE id=?");
    $stmt->bind_param("sdssi", $name, $price, $description, $image, $id);
    if ($stmt->execute()) {
        $msg = "✅ Cập nhật thành công!";
        // Cập nhật dữ liệu hiển thị
        $product['name'] = $name;
        $product['price'] = $price;
        $product['description'] = $description;
        $product['image'] = $image;
    } else {
        $msg = "❌ Lỗi: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Sửa sản phẩm</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans">
<nav class="bg-blue-700 text-white p-4 flex justify-between items-center shadow">
    <h1 class="text-xl font-bold">✏️ Sửa sản phẩm</h1>
    <a href="products.php" class="bg-blue-500 px-4 py-2 rounded hover:bg-blue-600">← Quay lại</a>
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
            <input type="text" name="name" value="<?= htmlspecialchars($product['name']) ?>" required class="w-full border px-4 py-3 rounded-lg">
        </div>
        <div>
            <label class="block font-semibold mb-1">Giá sản phẩm</label>
            <input type="number" step="0.01" name="price" value="<?= $product['price'] ?>" required class="w-full border px-4 py-3 rounded-lg">
        </div>
        <div>
            <label class="block font-semibold mb-1">Chi tiết sản phẩm</label>
            <textarea name="description" rows="5" class="w-full border px-4 py-3 rounded-lg" placeholder="Nhập mô tả sản phẩm..."><?= htmlspecialchars($product['description'] ?? '') ?></textarea>
        </div>
        <div>
            <label class="block font-semibold mb-1">Hình ảnh hiện tại</label>
            <img src="../uploads/<?= htmlspecialchars($product['image']) ?>" class="w-32 h-32 object-cover mb-3">
            <input type="file" name="image" accept="image/*" class="w-full">
        </div>
        <button type="submit" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 font-semibold">
            Cập nhật
        </button>
    </form>
</div>
</body>
</html>
