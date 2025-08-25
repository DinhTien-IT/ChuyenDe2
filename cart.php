<?php
session_start();
include 'db.php';

// Khởi tạo giỏ hàng nếu chưa có
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Xóa sản phẩm
if (isset($_GET['remove'])) {
    $id = $_GET['remove'];
    unset($_SESSION['cart'][$id]);
    header("Location: cart.php");
    exit();
}

// Cập nhật số lượng
if (isset($_POST['update_cart'])) {
    foreach ($_POST['qty'] as $id => $qty) {
        if ($qty > 0) {
            $_SESSION['cart'][$id]['quantity'] = $qty;
        }
    }
    header("Location: cart.php");
    exit();
}

// Lấy sản phẩm trong giỏ
$cartItems = $_SESSION['cart'];
$total = 0;
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Giỏ hàng - 3T Furniture</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex flex-col min-h-screen">

<!-- Header -->
<?php include 'includes/header.php'; ?>

<div class="max-w-7xl mx-auto p-4 flex-grow">
    <h1 class="text-3xl font-bold mb-6">🛒 Giỏ hàng của bạn</h1>

    <?php if (empty($cartItems)) : ?>
        <div class="bg-white p-6 rounded-lg shadow text-center">
            <p class="text-lg">Giỏ hàng trống.</p>
            <a href="index.php" class="mt-4 inline-block px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                Tiếp tục mua sắm
            </a>
        </div>
    <?php else : ?>
        <form method="POST">
            <div class="bg-white p-6 rounded-lg shadow overflow-x-auto">
                <table class="w-full border-collapse min-w-[600px]">
                    <thead>
                        <tr class="bg-gray-200 text-left">
                            <th class="p-3">Sản phẩm</th>
                            <th class="p-3">Giá</th>
                            <th class="p-3">Số lượng</th>
                            <th class="p-3">Tổng</th>
                            <th class="p-3">Xóa</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cartItems as $id => $item) :
                            $subtotal = $item['price'] * $item['quantity'];
                            $total += $subtotal;
                        ?>
                        <tr class="border-b">
                            <td class="p-3 flex items-center gap-4">
                                <img src="uploads/<?php echo htmlspecialchars($item['image']); ?>" 
                                     class="w-16 h-16 object-cover rounded border">
                                <span class="font-medium"><?php echo htmlspecialchars($item['name']); ?></span>
                            </td>
                            <td class="p-3"><?php echo number_format($item['price'], 0, ',', '.'); ?>₫</td>
                            <td class="p-3">
                                <input type="number" name="qty[<?php echo $id; ?>]" value="<?php echo $item['quantity']; ?>" 
                                    min="1" class="w-16 p-1 border rounded text-center">
                            </td>
                            <td class="p-3 font-semibold"><?php echo number_format($subtotal, 0, ',', '.'); ?>₫</td>
                            <td class="p-3">
                                <a href="cart.php?remove=<?php echo $id; ?>" 
                                    class="text-red-500 hover:text-red-700 font-bold">X</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <div class="flex justify-between items-center mt-6">
                    <button type="submit" name="update_cart" 
                        class="px-6 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                        Cập nhật giỏ hàng
                    </button>
                    <div class="text-xl font-bold">
                        Tổng cộng: <span class="text-red-600"><?php echo number_format($total, 0, ',', '.'); ?>₫</span>
                    </div>
                </div>
            </div>
        </form>

        <div class="mt-6 flex justify-end gap-4">
            <a href="index.php" class="px-6 py-2 bg-gray-300 rounded hover:bg-gray-400">← Tiếp tục mua</a>
            <a href="checkout.php" class="px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Thanh toán</a>
        </div>
    <?php endif; ?>
</div>

<!-- Footer -->
<?php include 'includes/footer.php'; ?>

</body>
</html>