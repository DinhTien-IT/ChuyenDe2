<?php
ob_start();
session_start();
include 'db.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $confirmPassword = trim($_POST['confirm_password']);

    if (!empty($username) && !empty($password) && !empty($confirmPassword)) {
        if ($password !== $confirmPassword) {
            $error = "Mật khẩu nhập lại không khớp!";
        } else {
            // Kiểm tra username đã tồn tại chưa
            $stmt = $conn->prepare("SELECT id FROM users WHERE username=?");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $error = "Tên đăng nhập đã tồn tại!";
            } else {
                // Mã hóa mật khẩu
                $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
                $role = 'user'; // mặc định là user

                $insert = $conn->prepare("INSERT INTO users (username,password,role) VALUES (?,?,?)");
                $insert->bind_param("sss", $username, $hashedPassword, $role);
                if ($insert->execute()) {
                    $success = "Đăng ký thành công! <a href='login.php' class='text-blue-600 font-semibold'>Đăng nhập ngay</a>";
                } else {
                    $error = "Có lỗi xảy ra, vui lòng thử lại!";
                }
            }
        }
    } else {
        $error = "Vui lòng nhập đủ thông tin!";
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Đăng ký - 3T Furniture</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex justify-center items-center min-h-screen">
<div class="bg-white p-8 rounded-lg shadow-xl w-96">
<h1 class="text-3xl font-bold text-center text-blue-600 mb-6">Đăng ký</h1>

<?php if($error): ?>
<p class="text-red-500 text-center mb-4"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<?php if($success): ?>
<p class="text-green-500 text-center mb-4"><?= $success ?></p>
<?php endif; ?>

<form method="post" class="space-y-5">
    <!-- Username -->
    <div>
        <input type="text" name="username" placeholder="Tên đăng nhập" required
            class="w-full border border-gray-300 px-4 py-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
    </div>

    <!-- Password -->
    <div class="relative">
        <input type="password" name="password" id="password" placeholder="Mật khẩu" required
            class="w-full border border-gray-300 px-4 py-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
        <span onclick="togglePassword('password','eyeIcon1')" 
            class="absolute right-3 top-3 cursor-pointer text-gray-500 hover:text-blue-600">
            <svg xmlns="http://www.w3.org/2000/svg" id="eyeIcon1" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="24" height="24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
            </svg>
        </span>
    </div>

    <!-- Confirm Password -->
    <div class="relative">
        <input type="password" name="confirm_password" id="confirm_password" placeholder="Nhập lại mật khẩu" required
            class="w-full border border-gray-300 px-4 py-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
        <span onclick="togglePassword('confirm_password','eyeIcon2')" 
            class="absolute right-3 top-3 cursor-pointer text-gray-500 hover:text-blue-600">
            <svg xmlns="http://www.w3.org/2000/svg" id="eyeIcon2" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="24" height="24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
            </svg>
        </span>
    </div>

    <!-- Button -->
    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white w-full py-3 rounded-lg text-lg font-semibold transition">Đăng ký</button>
    
    <p class="text-center mt-4 text-gray-600">Đã có tài khoản? 
        <a href="login.php" class="text-blue-600 font-semibold hover:underline">Đăng nhập</a>
    </p>
</form>
</div>

<script>
function togglePassword(fieldId, iconId) {
    const field = document.getElementById(fieldId);
    const icon = document.getElementById(iconId);
    if (field.type === 'password') {
        field.type = 'text';
        icon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.477 0-8.268-2.943-9.542-7a10.05 10.05 0 011.992-3.584M15 12a3 3 0 11-6 0 3 3 0 016 0z" />`;
    } else {
        field.type = 'password';
        icon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />`;
    }
}
</script>
</body>
</html>
