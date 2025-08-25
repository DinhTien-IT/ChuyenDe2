<?php
include 'db.php';

$newPassword = password_hash('123456', PASSWORD_BCRYPT);

$sql = "UPDATE users SET password='$newPassword' WHERE username='admin'";
if ($conn->query($sql)) {
    echo "Cập nhật mật khẩu admin thành công!";
} else {
    echo "Lỗi: " . $conn->error;
}
