<?php
session_start();
header('Content-Type: application/json');
$conn = mysqli_connect("localhost:3307", "root", "", "bookstore_db");

if (!$conn) {
    die(json_encode(['success' => false, 'message' => 'Kết nối database thất bại']));
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $book_id = isset($_POST['book_id']) ? (int)$_POST['book_id'] : 0;
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
    
    // Nếu chưa có session_id thì tạo mới
    if (!isset($_SESSION['cart_id'])) {
        $_SESSION['cart_id'] = session_id();
    }
    
    try {
        // Kiểm tra xem sản phẩm đã có trong giỏ hàng chưa
        $check_sql = "SELECT id, quantity FROM cart WHERE session_id = ? AND book_id = ?";
        $check_stmt = mysqli_prepare($conn, $check_sql);
        mysqli_stmt_bind_param($check_stmt, "si", $_SESSION['cart_id'], $book_id);
        mysqli_stmt_execute($check_stmt);
        $result = mysqli_stmt_get_result($check_stmt);
        $existing_item = mysqli_fetch_assoc($result);
        
        if ($existing_item) {
            // Nếu đã có thì cập nhật số lượng
            $update_sql = "UPDATE cart SET quantity = quantity + ? WHERE id = ?";
            $update_stmt = mysqli_prepare($conn, $update_sql);
            mysqli_stmt_bind_param($update_stmt, "ii", $quantity, $existing_item['id']);
            $success = mysqli_stmt_execute($update_stmt);
        } else {
            // Nếu chưa có thì thêm mới
            $insert_sql = "INSERT INTO cart (session_id, book_id, quantity) VALUES (?, ?, ?)";
            $insert_stmt = mysqli_prepare($conn, $insert_sql);
            mysqli_stmt_bind_param($insert_stmt, "sii", $_SESSION['cart_id'], $book_id, $quantity);
            $success = mysqli_stmt_execute($insert_stmt);
        }
        
        if ($success) {
            echo json_encode(['success' => true, 'message' => 'Thêm vào giỏ hàng thành công']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Lỗi khi thêm vào giỏ hàng']);
        }
        
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()]);
    }
}

mysqli_close($conn);
?>