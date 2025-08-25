<?php
session_start();
include 'includes/header.php';
include 'db.php';

$user = $_SESSION['currentUser'] ?? null;
if (!$user) {
    header("Location: login.php");
    exit;
}

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $address = trim($_POST['address']);
    $phone = trim($_POST['phone']);

    if (!empty($address) && !empty($phone)) {
        if (!preg_match('/^[0-9]{9,11}$/', $phone)) {
            $message = "<p class='text-red-500 mb-4'>Số điện thoại không hợp lệ!</p>";
        } else {
            $stmt = $conn->prepare("INSERT INTO addresses (user_id, address, phone) VALUES (?, ?, ?)");
            $stmt->bind_param("iss", $user['id'], $address, $phone);
            if ($stmt->execute()) {
                header("Location: address.php?success=1");
                exit;
            } else {
                $message = "<p class='text-red-500 mb-4'>Lỗi khi thêm địa chỉ!</p>";
            }
        }
    }
}

// Xóa địa chỉ
if (isset($_GET['delete'])) {
    $deleteId = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM addresses WHERE id=? AND user_id=?");
    $stmt->bind_param("ii", $deleteId, $user['id']);
    if ($stmt->execute()) {
        header("Location: address.php?deleted=1");
        exit;
    }
}

// Lấy danh sách địa chỉ
$stmt = $conn->prepare("SELECT * FROM addresses WHERE user_id=? ORDER BY id DESC");
$stmt->bind_param("i", $user['id']);
$stmt->execute();
$result = $stmt->get_result();
?>

<body class="bg-gray-50 flex flex-col min-h-screen">
<main class="flex-grow max-w-4xl mx-auto px-6 py-10 bg-white mt-8 shadow-lg rounded-lg">
    <h1 class="text-3xl font-bold mb-6 text-gray-800">📍 Sổ địa chỉ</h1>

    <?php if (isset($_GET['success'])): ?>
        <p class="text-green-500 mb-4">Đã thêm địa chỉ thành công!</p>
    <?php elseif (isset($_GET['deleted'])): ?>
        <p class="text-green-500 mb-4">Đã xóa địa chỉ!</p>
    <?php else: ?>
        <?= $message ?>
    <?php endif; ?>

    <!-- Form thêm địa chỉ -->
    <form method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <div>
            <input type="text" name="address" placeholder="Địa chỉ nhận hàng" required
                   class="w-full border px-4 py-3 rounded-lg focus:ring focus:ring-blue-300">
        </div>
        <div>
            <input type="text" name="phone" placeholder="Số điện thoại" required
                   class="w-full border px-4 py-3 rounded-lg focus:ring focus:ring-blue-300">
        </div>
        <div class="md:col-span-2 text-right">
            <button class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700">
                ➕ Thêm địa chỉ
            </button>
        </div>
    </form>

    <!-- Danh sách địa chỉ -->
    <div class="overflow-x-auto mt-6">
        <table class="w-full border rounded-lg shadow-sm">
            <thead>
            <tr class="bg-gray-100 text-gray-700">
                <th class="p-4 text-left">Địa chỉ</th>
                <th class="p-4 text-center">Số điện thoại</th>
                <th class="p-4 text-center">Hành động</th>
            </tr>
            </thead>
            <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr class="border-b hover:bg-gray-50">
                        <td class="p-4"><?= htmlspecialchars($row['address']) ?></td>
                        <td class="p-4 text-center"><?= htmlspecialchars($row['phone']) ?></td>
                        <td class="p-4 text-center">
                            <a href="?delete=<?= $row['id'] ?>"
                               onclick="return confirm('Bạn có chắc muốn xóa địa chỉ này?')"
                               class="text-red-500 hover:text-red-700">Xóa</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="3" class="text-center text-gray-500 p-4">Chưa có địa chỉ nào!</td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</main>

<?php include 'includes/footer.php'; ?>
</body>
