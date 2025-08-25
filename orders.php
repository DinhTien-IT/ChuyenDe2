<?php
session_start();
if (!isset($_SESSION['currentUser'])) {
    header("Location: login.php");
    exit();
}
include 'db.php';

$userId = $_SESSION['currentUser']['id'] ?? 0;

// Lấy danh sách đơn hàng của user
$stmt = $conn->prepare("SELECT id, created_at, total_price, address, payment_method FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
?>

<?php include 'includes/header.php'; ?>

<body class="bg-gray-50 flex flex-col min-h-screen">
<main class="flex-grow max-w-6xl mx-auto px-6 py-10 bg-white mt-8 shadow-lg rounded-lg">
    <h1 class="text-3xl font-bold mb-6 text-gray-800">📦 Đơn hàng của bạn</h1>

    <?php if ($result->num_rows > 0): ?>
        <div class="space-y-6">
            <?php while ($order = $result->fetch_assoc()): ?>
                <div class="bg-gray-50 rounded-lg shadow p-6 border hover:shadow-lg transition">
                    <p class="text-gray-700 mb-2">
                        <strong>Mã đơn:</strong> <?= htmlspecialchars($order['id']) ?>
                    </p>
                    <p class="text-gray-700 mb-2">
                        <strong>Ngày đặt:</strong> <?= htmlspecialchars($order['created_at']) ?>
                    </p>
                    <p class="text-gray-700 mb-2">
                        <strong>Tổng tiền:</strong>
                        <span class="text-red-600 font-semibold">
                            <?= number_format($order['total_price'], 0, ',', '.') ?>₫
                        </span>
                    </p>
                    <p class="text-gray-700 mb-2">
                        <strong>Địa chỉ:</strong> <?= htmlspecialchars($order['address']) ?>
                    </p>
                    <p class="text-gray-700">
                        <strong>Thanh toán:</strong> <?= htmlspecialchars($order['payment_method']) ?>
                    </p>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <p class="text-gray-500 text-center py-10">Bạn chưa có đơn hàng nào.</p>
    <?php endif; ?>
</main>

<?php include 'includes/footer.php'; ?>
</body>
