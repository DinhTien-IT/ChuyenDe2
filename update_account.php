<?php
session_start();
include 'db.php';

if (!isset($_SESSION['currentUser'])) {
    header("Location: login.php");
    exit();
}

$user = $_SESSION['currentUser'];

// Chỉ xử lý khi có POST data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = trim($_POST['fullName'] ?? '');
    $email = trim($_POST['email'] ?? '');

    // Kiểm tra dữ liệu hợp lệ
    if ($fullName === '' || $email === '') {
        $_SESSION['update_error'] = "Vui lòng nhập đầy đủ thông tin!";
        header("Location: account.php");
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['update_error'] = "Email không hợp lệ!";
        header("Location: account.php");
        exit();
    }

    // Cập nhật DB
    $sql = $conn->prepare("UPDATE users SET fullName=?, email=? WHERE id=?");
    $sql->bind_param("ssi", $fullName, $email, $user['id']);

    if ($sql->execute()) {
        // Cập nhật lại session
        $_SESSION['currentUser']['fullName'] = $fullName;
        $_SESSION['currentUser']['email'] = $email;
        $_SESSION['update_success'] = "Cập nhật thông tin thành công!";
    } else {
        $_SESSION['update_error'] = "Lỗi khi cập nhật, vui lòng thử lại!";
    }

    header("Location: account.php");
    exit();
} else {
    header("Location: account.php");
    exit();
}
?>

<?php include 'includes/footer.php'; ?>

