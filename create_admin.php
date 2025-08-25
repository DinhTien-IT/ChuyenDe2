<?php
include 'db.php';

$username = 'admin';
$password = 'admin123';
$email = 'admin@example.com';
$hashedPassword = password_hash($password, PASSWORD_BCRYPT);

// Kiểm tra nếu admin đã tồn tại
$stmt = $conn->prepare("SELECT * FROM users WHERE username=?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Cập nhật mật khẩu nếu admin đã tồn tại
    $stmt = $conn->prepare("UPDATE users SET password=?, role='admin', email=? WHERE username=?");
    $stmt->bind_param("sss", $hashedPassword, $email, $username);
    if ($stmt->execute()) {
        echo "✅ Admin account updated successfully!";
    } else {
        echo "❌ Failed to update admin account!";
    }
} else {
    // Tạo mới nếu chưa tồn tại
    $stmt = $conn->prepare("INSERT INTO users (username, password, email, role) VALUES (?, ?, ?, 'admin')");
    $stmt->bind_param("sss", $username, $hashedPassword, $email);
    if ($stmt->execute()) {
        echo "✅ Admin account created successfully!";
    } else {
        echo "❌ Failed to create admin account!";
    }
}
?>
