<?php
session_start();
if (!isset($_SESSION['currentUser']) || strtolower($_SESSION['currentUser']['role']) !== 'admin') {
    header("Location: ../login.php");
    exit();
}

include '../db.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    if ($id > 0) {
        // Xóa bản ghi trong order_items liên quan
        $conn->query("DELETE FROM order_items WHERE product_id = $id");

        // Lấy ảnh cũ để xóa file
        $stmt = $conn->prepare("SELECT image FROM products WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->bind_result($oldImage);
        $stmt->fetch();
        $stmt->close();

        // Xóa file ảnh
        if ($oldImage && file_exists("../uploads/" . $oldImage)) {
            unlink("../uploads/" . $oldImage);
        }

        // Xóa sản phẩm
        $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            header("Location: products.php?msg=deleted");
            exit();
        } else {
            die("Lỗi SQL: " . $stmt->error);
        }
    } else {
        die("ID không hợp lệ.");
    }
} else {
    die("Thiếu tham số ID.");
}
?>
