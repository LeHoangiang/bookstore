<?php
session_start();
require_once 'config.php';

// Lấy thông tin đơn hàng từ form
$hoTen = $_POST['ho-ten'];
$email = $_POST['email'];
$soDienThoai = $_POST['so-dien-thoai'];
$diaChi = $_POST['dia-chi'];
$province = $_POST['province_name'];
$district = $_POST['district_name'];
$ward = $_POST['ward_name'];
$ghiChu = $_POST['ghi-chu'];

// Tính tổng tiền từ giỏ hàng
$total = 0; // Khởi tạo biến total
if (isset($_SESSION['cart_id'])) {
    $sql = "SELECT c.*, b.price FROM cart c 
            JOIN books b ON c.book_id = b.id 
            WHERE c.session_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$_SESSION['cart_id']]);
    $cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($cart_items as $item) {
        $total += $item['quantity'] * $item['price'];
    }
}

// Kiểm tra số tiền hợp lệ
if ($total < 5000 || $total > 1000000000) {
    echo "Số tiền thanh toán không hợp lệ. Số tiền phải từ 5,000đ đến 1,000,000,000đ";
    exit;
}

try {
    $pdo->beginTransaction();

    // Tạo mã đơn hàng unique
    $orderId = 'DH' . time() . rand(1000, 9999);

    // Debug thông tin
    error_log("Order ID: " . $orderId);
    error_log("Total Amount: " . $total);
    error_log("Cart Items: " . print_r($cart_items, true));

    // Lưu thông tin shipping address
    $sql = "INSERT INTO shipping_addresses (customer_name, email, phone, address, province, district, ward) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $hoTen,
        $email,
        $soDienThoai,
        $diaChi,
        $province,
        $district,
        $ward
    ]);
    $shipping_address_id = $pdo->lastInsertId();

    // Lưu đơn hàng
    $sql = "INSERT INTO orders (order_id, session_id, shipping_address_id, total_amount, status, payment_method, notes, created_at) 
            VALUES (?, ?, ?, ?, 'pending', 'vnpay', ?, NOW())";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $orderId,
        $_SESSION['cart_id'],
        $shipping_address_id,
        $total, // Đã được tính toán ở trên
        $ghiChu
    ]);

    // Lưu chi tiết đơn hàng
    foreach ($cart_items as $item) {
        $sql = "INSERT INTO order_items (order_id, book_id, quantity, price) 
                VALUES (?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $orderId, 
            $item['book_id'], 
            $item['quantity'], 
            $item['price']
        ]);
    }

    $pdo->commit();

    // Tạo URL thanh toán VNPay
    $vnpayUrl = createVNPayPayment(
        $orderId,
        $total,
        "Thanh toan don hang " . $orderId
    );

    // Debug URL thanh toán
    error_log("VNPay URL: " . $vnpayUrl);

    // Chuyển hướng đến trang thanh toán VNPay
    header('Location: ' . $vnpayUrl);
    exit;

} catch (Exception $e) {
    $pdo->rollBack();
    error_log("Payment Error: " . $e->getMessage());
    echo "Có lỗi xảy ra: " . $e->getMessage();
}
?>