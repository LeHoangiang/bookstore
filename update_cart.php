<?php
session_start();
header('Content-Type: application/json');

// Đọc dữ liệu JSON từ request
$data = json_decode(file_get_contents('php://input'), true);

$conn = mysqli_connect("localhost:3307", "root", "", "bookstore_db");

if (!$conn) {
    die(json_encode(['success' => false, 'message' => 'Kết nối database thất bại']));
}

if (isset($data['cart_id']) && isset($data['change'])) {
    $cart_id = (int)$data['cart_id'];
    $change = (int)$data['change'];

    // Lấy số lượng hiện tại
    $sql = "SELECT quantity FROM cart WHERE id = ? AND session_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "is", $cart_id, $_SESSION['cart_id']);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($row = mysqli_fetch_assoc($result)) {
        $new_quantity = $row['quantity'] + $change;
        
        if ($new_quantity <= 0) {
            // Nếu số lượng <= 0, xóa sản phẩm
            $delete_sql = "DELETE FROM cart WHERE id = ? AND session_id = ?";
            $delete_stmt = mysqli_prepare($conn, $delete_sql);
            mysqli_stmt_bind_param($delete_stmt, "is", $cart_id, $_SESSION['cart_id']);
            $success = mysqli_stmt_execute($delete_stmt);
        } else {
            // Cập nhật số lượng mới
            $update_sql = "UPDATE cart SET quantity = ? WHERE id = ? AND session_id = ?";
            $update_stmt = mysqli_prepare($conn, $update_sql);
            mysqli_stmt_bind_param($update_stmt, "iis", $new_quantity, $cart_id, $_SESSION['cart_id']);
            $success = mysqli_stmt_execute($update_stmt);
        }
        
        if ($success) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Lỗi khi cập nhật số lượng']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Không tìm thấy sản phẩm']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ']);
}

mysqli_close($conn);
?>