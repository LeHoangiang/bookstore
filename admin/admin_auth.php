<?php
session_start();
$conn = mysqli_connect("localhost:3307", "root", "", "bookstore_db");

// Kiểm tra nếu user chưa đăng nhập
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Lấy thông tin người dùng
$user_id = $_SESSION['user_id'];
$sql = "SELECT role FROM user WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

// Kiểm tra vai trò
if ($user['role'] !== 'admin') {
    echo "Bạn không có quyền truy cập trang này.";
    exit();
}
?>
