<?php
session_start();
require_once 'config.php';

if (isset($_GET['resultCode'])) {
    if ($_GET['resultCode'] == 0) {
        // Thanh toán thành công
        $orderId = $_GET['orderId'];
        
        // Cập nhật trạng thái đơn hàng
        $sql = "UPDATE orders SET status = 'completed' WHERE order_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$orderId]);
        
        // Xóa giỏ hàng
        $sql = "DELETE FROM cart WHERE session_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$_SESSION['cart_id']]);
        
        // Hiển thị trang thành công
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <title>Thanh toán thành công</title>
        </head>
        <body>
            <h1>Thanh toán thành công!</h1>
            <p>Mã đơn hàng: <?php echo htmlspecialchars($orderId); ?></p>
            <a href="index.php">Quay về trang chủ</a>
        </body>
        </html>
        <?php
    } else {
        // Thanh toán thất bại
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <title>Thanh toán thất bại</title>
        </head>
        <body>
            <h1>Thanh toán thất bại!</h1>
            <p>Lỗi: <?php echo htmlspecialchars($_GET['message']); ?></p>
            <a href="cart.php">Quay về giỏ hàng</a>
        </body>
        </html>
        <?php
    }
}