<?php
session_start();
include 'db.php';

// Lấy ID sản phẩm từ URL
$productId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Lấy thông tin sản phẩm từ CSDL
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param("i", $productId);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();

// Nếu sản phẩm không tồn tại
if (!$product) {
    echo "<p class='text-center text-red-500 mt-10'>Sản phẩm không tồn tại!</p>";
    exit();
}

// Xử lý thêm vào giỏ hàng
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $cartItem = [
        'id' => $product['id'],
        'name' => $product['name'],
        'price' => $product['price'],
        'image' => $product['image'],
        'quantity' => (int)$_POST['quantity']
    ];

    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // Nếu sản phẩm đã có trong giỏ hàng thì cộng số lượng
    if (isset($_SESSION['cart'][$product['id']])) {
        $_SESSION['cart'][$product['id']]['quantity'] += (int)$_POST['quantity'];
    } else {
        $_SESSION['cart'][$product['id']] = [
            'id' => $product['id'],
            'name' => $product['name'],
            'price' => $product['price'],
            'image' => $product['image'],
            'quantity' => (int)$_POST['quantity']
        ];
    }

    header("Location: cart.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($product['name']) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex flex-col min-h-screen">

<!-- Header -->
<?php include 'includes/header.php'; ?>

<!-- Nội dung sản phẩm -->
<div class="max-w-6xl mx-auto mt-10 bg-white p-6 rounded-lg shadow-md grid grid-cols-1 md:grid-cols-2 gap-8 flex-grow">
    <!-- Hình ảnh sản phẩm -->
    <div class="flex justify-center items-center">
        <img src="uploads/<?= htmlspecialchars($product['image']) ?>" 
             alt="<?= htmlspecialchars($product['name']) ?>" 
             class="rounded-lg shadow-md max-h-96 object-contain">
    </div>

    <!-- Thông tin sản phẩm -->
    <div>
        <h1 class="text-3xl font-bold mb-4"><?= htmlspecialchars($product['name']) ?></h1>
        <p class="text-gray-600 mb-4">
            <?= !empty($product['description']) ? htmlspecialchars($product['description']) : "Chưa có mô tả cho sản phẩm này." ?>
        </p>
        <p class="text-2xl font-bold text-red-500 mb-6">
            <?= number_format($product['price'], 0, ',', '.') ?>₫
        </p>

        <!-- Form thêm vào giỏ hàng -->
        <form method="post" class="flex gap-4 items-center">
            <input type="number" name="quantity" value="1" min="1" 
                   class="border border-gray-300 rounded p-2 w-20 text-center">
            <button type="submit" name="add_to_cart" 
                    class="bg-blue-500 text-white px-6 py-2 rounded-lg hover:bg-blue-600">
                ➕ Thêm vào giỏ
            </button>
        </form>
    </div>
</div>

<!-- Footer -->
<?php include 'includes/footer.php'; ?>

</body>
</html>
