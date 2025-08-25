<?php
$host = "localhost";
$user = "root"; // tên user MySQL
$pass = "";     // mật khẩu MySQL (XAMPP mặc định để trống)
$dbname = "furniture";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}
?>
