<?php
session_start();
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Lưu thông tin địa chỉ giao hàng
        $sql = "INSERT INTO shipping_addresses (
                    customer_name, email, phone, 
                    province, district, ward, address,
                    created_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $_POST['ho-ten'],
            $_POST['email'],
            $_POST['so-dien-thoai'],
            $_POST['province_name'],
            $_POST['district_name'],
            $_POST['ward_name'],
            $_POST['dia-chi']
        ]);
        
        $addressId = $pdo->lastInsertId();

        // Tính tổng tiền từ giỏ hàng
        $total = 0;
        if (isset($_SESSION['cart_id'])) {
            $sql = "SELECT c.quantity, b.price 
                    FROM cart c 
                    JOIN books b ON c.book_id = b.id 
                    WHERE c.session_id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$_SESSION['cart_id']]);
            $cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($cart_items as $item) {
                $total += $item['quantity'] * $item['price'];
            }
        }

        // Tạo mã đơn hàng
        $orderId = "ORDER_" . time();
        
        // Lưu thông tin đơn hàng
        $sql = "INSERT INTO orders (
                    order_id, session_id, shipping_address_id,
                    total_amount, status, payment_method,
                    notes, created_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $orderId,
            $_SESSION['cart_id'],
            $addressId,
            $total,
            'pending',
            'momo',
            $_POST['ghi-chu'] ?? null
        ]);

        // Lưu chi tiết đơn hàng
        $sql = "INSERT INTO order_items (order_id, book_id, quantity, price, created_at)
                SELECT ?, c.book_id, c.quantity, b.price, NOW()
                FROM cart c
                JOIN books b ON c.book_id = b.id
                WHERE c.session_id = ?";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$orderId, $_SESSION['cart_id']]);

        // Tạo thanh toán MoMo
        $orderInfo = "Thanh toan don hang " . $orderId;
        $result = createMoMoPayment($orderId, $total, $orderInfo);

        if (isset($result['payUrl'])) {
            // Chuyển hướng đến trang thanh toán MoMo
            header('Location: ' . $result['payUrl']);
            exit;
        } else {
            // Log lỗi để debug
            error_log("MoMo Payment Error: " . print_r($result, true));
            throw new Exception("Không thể tạo thanh toán MoMo");
        }

    } catch (Exception $e) {
        // Log lỗi
        error_log($e->getMessage());
        
        // Trả về thông báo lỗi
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => 'Có lỗi xảy ra khi tạo thanh toán: ' . $e->getMessage()
        ]);
    }
} else {
    // Nếu không phải POST request
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Phương thức không được hỗ trợ'
    ]);
}
?>