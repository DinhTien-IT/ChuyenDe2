<?php
ob_start(); // Bắt đầu buffer để tránh lỗi header
session_start();
include 'db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $stmt = $conn->prepare("SELECT * FROM users WHERE username=? LIMIT 1");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            $_SESSION['currentUser'] = $user;

            // Kiểm tra quyền và redirect
            if (strtolower($user['role']) === 'admin') {
                header("Location: ./admin/dashboard.php");
                exit();
            } else {
                header("Location: index.php");
                exit();
            }
        } else {
            $error = "Sai mật khẩu!";
        }
    } else {
        $error = "Tên đăng nhập không tồn tại!";
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Đăng nhập - 3T Furniture</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex justify-center items-center min-h-screen">
<div class="bg-white p-8 rounded-lg shadow-xl w-96">
<h1 class="text-3xl font-bold text-center text-blue-600 mb-6">Đăng nhập</h1>
<?php if($error): ?>
<p class="text-red-500 text-center mb-4"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>
<form method="post" class="space-y-5">
    <div>
        <input type="text" name="username" placeholder="Tên đăng nhập" required
            class="w-full border border-gray-300 px-4 py-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
    </div>
    <div class="relative">
        <input type="password" name="password" id="password" placeholder="Mật khẩu" required
            class="w-full border border-gray-300 px-4 py-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
        <span onclick="togglePassword()" class="absolute right-3 top-3 cursor-pointer text-gray-500 hover:text-blue-600">
            <svg xmlns="http://www.w3.org/2000/svg" id="eyeIcon" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="24" height="24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
            </svg>
        </span>
    </div>
    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white w-full py-3 rounded-lg text-lg font-semibold transition">Đăng nhập</button>
    <p class="text-center mt-4 text-gray-600">Chưa có tài khoản? 
        <a href="register.php" class="text-blue-600 font-semibold hover:underline">Đăng ký</a>
    </p>
</form>
</div>
<script>
function togglePassword() {
    const field = document.getElementById('password');
    const icon = document.getElementById('eyeIcon');
    field.type = (field.type === 'password') ? 'text' : 'password';
}
</script>
</body>
</html>
