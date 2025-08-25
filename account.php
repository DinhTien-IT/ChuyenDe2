<?php
session_start();
if (!isset($_SESSION['currentUser'])) {
    header("Location: login.php");
    exit();
}
$user = $_SESSION['currentUser'];

// Gán giá trị mặc định nếu thiếu
$fullName = isset($user['fullName']) ? $user['fullName'] : '';
$email = isset($user['email']) ? $user['email'] : '';
$role = isset($user['role']) ? $user['role'] : 'user';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Tài khoản - 3T Furniture</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans min-h-screen flex flex-col">

<!-- Navbar -->
<nav class="bg-white shadow-md sticky top-0 z-50">
  <div class="max-w-7xl mx-auto px-4 py-3 flex justify-between items-center">
    <a href="index.php" class="text-2xl font-bold text-blue-600">3T Furniture</a>
    <div class="relative">
      <img src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png" class="w-8 h-8 rounded-full cursor-pointer" onclick="toggleMenu()">
      <div id="menu" class="hidden absolute right-0 mt-2 w-56 bg-white border rounded shadow-lg">
        <a href="account.php" class="block px-4 py-2 hover:bg-gray-100">Thông tin tài khoản</a>
        <a href="address.php" class="block px-4 py-2 hover:bg-gray-100">Sổ địa chỉ</a>
        <a href="orders.php" class="block px-4 py-2 hover:bg-gray-100">Đơn hàng</a>
        <?php if($role === 'admin'): ?>
          <a href="admin/dashboard.php" class="block px-4 py-2 hover:bg-gray-100">Quản trị</a>
        <?php endif; ?>
        <a href="logout.php" class="block px-4 py-2 text-red-600 hover:bg-gray-100">Đăng xuất</a>
      </div>
    </div>
  </div>
</nav>

<!-- Main Content -->
<main class="max-w-5xl mx-auto px-6 py-10 bg-white mt-6 shadow rounded-lg flex-1">
  <!-- Header Info -->
  <div class="flex items-center space-x-6 border-b pb-6 mb-6">
    <img src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png" class="w-20 h-20 rounded-full border shadow">
    <div>
      <h1 class="text-3xl font-bold text-gray-800">Xin chào, <?php echo htmlspecialchars($fullName ?: $user['username']); ?></h1>
      <p class="text-gray-500 capitalize"><?php echo htmlspecialchars($role); ?></p>
    </div>
  </div>

  <!-- Account Form -->
  <section class="bg-gray-50 p-6 rounded-lg shadow-inner border">
    <h2 class="text-2xl font-semibold mb-6 border-b pb-3">Chỉnh sửa thông tin</h2>
    <form method="post" action="update_account.php" class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <div>
        <label class="block font-medium mb-1">Họ tên</label>
        <input type="text" name="fullName" value="<?php echo htmlspecialchars($fullName); ?>" 
          placeholder="Nhập họ tên" class="w-full border px-5 py-3 rounded-lg focus:ring-2 focus:ring-blue-500">
      </div>
      <div>
        <label class="block font-medium mb-1">Email</label>
        <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" 
          placeholder="Nhập email" class="w-full border px-5 py-3 rounded-lg focus:ring-2 focus:ring-blue-500">
      </div>
      <div class="md:col-span-2 text-right">
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold">Cập nhật</button>
      </div>
    </form>
  </section>
</main>

<script>
function toggleMenu() {
    document.getElementById('menu').classList.toggle('hidden');
}
</script>
</body>
</html>

<?php include 'includes/footer.php'; ?>
