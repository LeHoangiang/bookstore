<?php
session_start();
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo->beginTransaction();

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
            $_POST['tinh-thanh-pho'],
            $_POST['quan-huyen'],
            $_POST['phuong-xa'],
            $_POST['dia-chi']
        ]);
        
        $addressId = $pdo->lastInsertId();

        // Tạo đơn hàng
        $orderId = "ORDER_" . time();
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
            $_POST['total_amount'],
            'pending',
            $_POST['payment_method'],
            $_POST['ghi-chu'] ?? null
        ]);

        $pdo->commit();

        // Chuyển hướng dựa vào phương thức thanh toán
        if ($_POST['payment_method'] === 'momo') {
            header('Location: process_momo_payment.php?order_id=' . $orderId);
        } else {
            header('Location: order_success.php?order_id=' . $orderId);
        }
        exit;

    } catch (Exception $e) {
        $pdo->rollBack();
        error_log($e->getMessage());
        header('Location: checkout.php?error=1');
        exit;
    }
}