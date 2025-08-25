<?php
session_start();
if (!isset($_SESSION['currentUser']) || strtolower($_SESSION['currentUser']['role']) !== 'admin') {
    header("Location: ../login.php");
    exit();
}
include '../db.php';

// Nếu có cập nhật
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_order'])) {
    $id = intval($_POST['order_id']);
    $full_name = trim($_POST['full_name']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    $status = trim($_POST['status']);

    $stmt = $conn->prepare("UPDATE orders SET full_name=?, phone=?, address=?, status=? WHERE id=?");
    $stmt->bind_param("ssssi", $full_name, $phone, $address, $status, $id);
    if ($stmt->execute()) {
        $msg = "✅ Cập nhật đơn hàng #$id thành công!";
    } else {
        $msg = "❌ Lỗi cập nhật: " . $conn->error;
    }
}

$orders = $conn->query("SELECT * FROM orders ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý đơn hàng</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100 font-sans">
    <nav class="bg-blue-700 text-white p-4 flex justify-between items-center shadow">
        <h1 class="text-xl font-bold">Quản lý đơn hàng</h1>
        <a href="dashboard.php" class="bg-blue-500 px-4 py-2 rounded hover:bg-blue-600">← Quay lại</a>
    </nav>

    <div class="max-w-7xl mx-auto p-6">
        <?php if (!empty($msg)): ?>
            <p class="mb-4 text-center font-semibold <?= strpos($msg, '✅') !== false ? 'text-green-600' : 'text-red-600' ?>">
                <?= $msg ?>
            </p>
        <?php endif; ?>

        <h2 class="text-2xl font-bold mb-6 text-blue-700">Danh sách đơn hàng</h2>
        <div class="bg-white rounded-lg shadow overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-blue-100">
                    <tr>
                        <th class="p-3">ID</th>
                        <th class="p-3">Khách hàng</th>
                        <th class="p-3">SĐT</th>
                        <th class="p-3">Địa chỉ</th>
                        <th class="p-3">Tổng tiền</th>
                        <th class="p-3">Trạng thái</th>
                        <th class="p-3">Ngày tạo</th>
                        <th class="p-3">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $orders->fetch_assoc()): ?>
                        <tr class="border-b hover:bg-blue-50">
                            <td class="p-3"><?= $row['id'] ?></td>
                            <td class="p-3"><?= htmlspecialchars($row['full_name']) ?></td>
                            <td class="p-3"><?= htmlspecialchars($row['phone']) ?></td>
                            <td class="p-3"><?= htmlspecialchars($row['address']) ?></td>
                            <td class="p-3 font-bold"><?= number_format($row['total_price'], 0, ',', '.') ?>₫</td>
                            <td class="p-3">
                                <span class="px-3 py-1 rounded text-white 
                            <?= $row['status'] == 'pending' ? 'bg-yellow-500' : ($row['status'] == 'completed' ? 'bg-green-600' : 'bg-gray-400') ?>">
                                    <?= ucfirst($row['status']) ?>
                                </span>
                            </td>
                            <td class="p-3"><?= $row['created_at'] ?></td>
                            <td class="p-3">
                                <button onclick="openEditModal(<?= htmlspecialchars(json_encode($row)) ?>)"
                                    class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600">
                                    ✏️ Sửa
                                </button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal -->
    <div id="editModal" class="fixed inset-0 hidden bg-black bg-opacity-50 flex items-center justify-center">
        <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-lg">
            <h2 class="text-xl font-bold mb-4">Cập nhật đơn hàng</h2>
            <form method="POST" class="space-y-4">
                <input type="hidden" name="order_id" id="order_id">
                <div>
                    <label class="block font-semibold mb-1">Họ tên</label>
                    <input type="text" name="full_name" id="full_name" class="w-full border px-3 py-2 rounded" required>
                </div>
                <div>
                    <label class="block font-semibold mb-1">Số điện thoại</label>
                    <input type="text" name="phone" id="phone" class="w-full border px-3 py-2 rounded" required>
                </div>
                <div>
                    <label class="block font-semibold mb-1">Địa chỉ</label>
                    <textarea name="address" id="address" class="w-full border px-3 py-2 rounded" required></textarea>
                </div>
                <div>
                    <label class="block font-semibold mb-1">Trạng thái</label>
                    <select name="status" id="status" class="w-full border px-3 py-2 rounded">
                        <option value="Đang chờ duyệt">Đang chờ duyệt</option>
                        <option value="Đã duyệt">Đã duyệt</option>
                        <option value="Đã huỷ">Đã huỷ</option>
                    </select>
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" onclick="closeEditModal()" class="bg-gray-400 text-white px-4 py-2 rounded hover:bg-gray-500">Hủy</button>
                    <button type="submit" name="update_order" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Lưu</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openEditModal(order) {
            document.getElementById('order_id').value = order.id;
            document.getElementById('full_name').value = order.full_name;
            document.getElementById('phone').value = order.phone;
            document.getElementById('address').value = order.address;
            document.getElementById('status').value = order.status;
            document.getElementById('editModal').classList.remove('hidden');
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.add('hidden');
        }
    </script>

</body>

</html>