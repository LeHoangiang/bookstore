<?php
session_start();
header('Content-Type: application/json');

// Đọc dữ liệu JSON từ request
$data = json_decode(file_get_contents('php://input'), true);

$conn = mysqli_connect("localhost:3307", "root", "", "bookstore_db");

if (!$conn) {
    die(json_encode(['success' => false, 'message' => 'Kết nối database thất bại']));
}

if (isset($data['cart_id'])) {
    $cart_id = (int)$data['cart_id'];
    
    $sql = "DELETE FROM cart WHERE id = ? AND session_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "is", $cart_id, $_SESSION['cart_id']);
    
    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Lỗi khi xóa sản phẩm']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ']);
}

mysqli_close($conn);
?>