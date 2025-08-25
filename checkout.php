<?php
session_start();
include 'db.php';

// Nếu chưa đăng nhập, chuyển hướng
if (!isset($_SESSION['currentUser'])) {
    header("Location: login.php");
    exit();
}

$user = $_SESSION['currentUser'];

// Xử lý khi submit form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cart = json_decode($_POST['cart_data'] ?? '[]', true);
    $address = trim($_POST['address'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $payment = trim($_POST['payment'] ?? 'COD');

    if (!$cart || empty($address) || empty($phone)) {
        $error = "Vui lòng nhập đầy đủ thông tin.";
    } else {
        $total_price = 0;
        foreach ($cart as $item) {
            $total_price += $item['price'] * $item['quantity'];
        }

        $conn->begin_transaction();
        try {
            $stmt = $conn->prepare("INSERT INTO orders (user_id, address, phone, payment_method, total_price) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("isssd", $user['id'], $address, $phone, $payment, $total_price);
            $stmt->execute();
            $order_id = $stmt->insert_id;

            $stmt_item = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
            foreach ($cart as $item) {
                $stmt_item->bind_param("iiid", $order_id, $item['id'], $item['quantity'], $item['price']);
                $stmt_item->execute();
            }

            $conn->commit();
            $success = "🎉 Đặt hàng thành công! Mã đơn hàng: #" . $order_id;
        } catch (Exception $e) {
            $conn->rollback();
            $error = "Lỗi khi đặt hàng: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Thanh toán - 3T Furniture</title>
<script src="https://cdn.tailwindcss.com"></script>
<script>
function loadCart() {
    let cart = JSON.parse(localStorage.getItem('cart') || '[]');
    let list = document.getElementById('cart-items');
    let total = 0;
    let cartDataInput = document.getElementById('cart_data');
    list.innerHTML = '';
    cart.forEach(item => {
        total += item.price * item.quantity;
        list.innerHTML += `
            <div class="flex justify-between border-b py-3 text-gray-700">
                <span class="font-medium">${item.name} <span class="text-sm text-gray-500">(x${item.quantity})</span></span>
                <span class="text-blue-600 font-semibold">${(item.price * item.quantity).toLocaleString()}đ</span>
            </div>
        `;
    });
    document.getElementById('total-price').innerText = total.toLocaleString() + 'đ';
    cartDataInput.value = JSON.stringify(cart);
}
window.onload = loadCart;
</script>
</head>
<body class="bg-gradient-to-b from-blue-50 to-blue-100 min-h-screen">

<div class="max-w-5xl mx-auto mt-10 p-8 bg-white rounded-2xl shadow-2xl border border-blue-200">
    <h1 class="text-3xl font-bold mb-8 text-blue-700 flex items-center gap-2">
        🛒 Thanh toán đơn hàng
    </h1>

    <?php if (!empty($error)): ?>
        <div class="bg-red-100 text-red-700 p-4 mb-6 rounded-lg shadow"><?= $error ?></div>
    <?php endif; ?>

    <?php if (!empty($success)): ?>
        <div class="bg-green-100 text-green-700 p-4 mb-6 rounded-lg shadow text-lg"><?= $success ?></div>
        <script>localStorage.removeItem('cart');</script>
    <?php else: ?>
    <form method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- Giỏ hàng -->
        <div class="bg-blue-50 border border-blue-200 p-6 rounded-xl shadow-md">
            <h2 class="text-xl font-semibold mb-4 text-blue-800">🛍 Sản phẩm</h2>
            <div id="cart-items" class="bg-white rounded-lg p-4 shadow-inner"></div>
            <div class="mt-4 font-bold text-xl text-right text-blue-700">
                Tổng: <span id="total-price">0đ</span>
            </div>
            <input type="hidden" name="cart_data" id="cart_data">
        </div>

        <!-- Thông tin nhận hàng -->
        <div class="bg-blue-50 border border-blue-200 p-6 rounded-xl shadow-md">
            <h2 class="text-xl font-semibold mb-4 text-blue-800">📦 Thông tin nhận hàng</h2>

            <label class="block mb-2 text-gray-700">Địa chỉ</label>
            <input type="text" name="address" class="w-full p-3 border border-gray-300 rounded-lg mb-4 focus:ring-2 focus:ring-blue-400" required>

            <label class="block mb-2 text-gray-700">Số điện thoại</label>
            <input type="text" name="phone" class="w-full p-3 border border-gray-300 rounded-lg mb-4 focus:ring-2 focus:ring-blue-400" required>

            <label class="block mb-2 text-gray-700">Phương thức thanh toán</label>
            <select name="payment" class="w-full p-3 border border-gray-300 rounded-lg mb-6 focus:ring-2 focus:ring-blue-400">
                <option value="COD">💵 Thanh toán khi nhận hàng (COD)</option>
                <option value="Bank">🏦 Chuyển khoản ngân hàng</option>
                <option value="Momo">📱 Ví Momo</option>
            </select>

            <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-lg font-semibold hover:bg-blue-700 hover:scale-[1.02] transition">
                ✅ Xác nhận đặt hàng
            </button>
        </div>
    </form>
    <?php endif; ?>
</div>

</body>
</html>
