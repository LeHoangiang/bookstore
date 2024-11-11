<?php
session_start();
require_once 'config.php';

if (!isset($_GET['order_id'])) {
    header('Location: order_history.php');
    exit();
}

$orderId = $_GET['order_id']; // Giữ nguyên order_id bao gồm cả "DH"

try {
   
    // Lấy thông tin đơn hàng và thông tin user cùng lúc
    $stmt = $pdo->prepare("
    SELECT o.*, s.customer_name, s.email, s.phone, s.address
    FROM orders o
    LEFT JOIN shipping_addresses s ON o.shipping_address_id = s.id
    WHERE o.order_id = ?
");
    $stmt->execute([$orderId]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);
     
    if (!$order) {
        echo "Không tìm thấy đơn hàng với ID: " . $orderId;
        exit();
    }

    // Lấy chi tiết đơn hàng
    $itemsStmt = $pdo->prepare("
    SELECT oi.*, b.title as book_title
    FROM order_items oi
    JOIN books b ON oi.book_id = b.id
    WHERE oi.order_id = ?
");
    $itemsStmt->execute([$orderId]);
    $items = $itemsStmt->fetchAll(PDO::FETCH_ASSOC);

} catch(PDOException $e) {
    echo "Lỗi truy vấn: " . $e->getMessage();
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Hóa đơn #<?php echo $order['order_id']; ?></title>
    <style>
        @media print {
            .no-print {
                display: none;
            }
        }
        .invoice {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .invoice-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .customer-info {
            margin-bottom: 20px;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .items-table th, .items-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .total {
            text-align: right;
            margin-top: 20px;
        }
        .buttons {
            margin-top: 30px;
            text-align: center;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            margin: 0 10px;
            border-radius: 5px;
            text-decoration: none;
            color: white;
            font-weight: bold;
            cursor: pointer;
            border: none;
        }
        .btn-print {
            background-color: #28a745;
        }
        .btn-back {
            background-color: #6c757d;
        }
        .btn:hover {
            opacity: 0.9;
        }
    </style>
</head>
<body>
    <div class="invoice">
        <div class="invoice-header">
            <h1>HÓA ĐƠN</h1>
            <p>Mã đơn hàng: <?php echo htmlspecialchars($order['order_id']); ?></p>
            <p>Ngày: <?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></p>
        </div>

        <div class="customer-info">
            <h3>Thông tin khách hàng:</h3>
            <p>Tên: <?php echo htmlspecialchars($order['customer_name']); ?></p>
            <p>Email: <?php echo htmlspecialchars($order['email']); ?></p>
            <p>Số điện thoại: <?php echo htmlspecialchars($order['phone']); ?></p>
            <p>Địa chỉ: <?php echo htmlspecialchars($order['address']); ?></p>
        </div>

        <table class="items-table">
            <thead>
                <tr>
                    <th>STT</th>
                    <th>Sản phẩm</th>
                    <th>Số lượng</th>
                    <th>Đơn giá</th>
                    <th>Thành tiền</th>
                </tr>
            </thead>
            <tbody>
        <?php if (!empty($items)): ?>
            <?php $i = 1; foreach ($items as $item): ?>
                <tr>
                    <td><?php echo $i++; ?></td>
                    <td><?php echo htmlspecialchars($item['book_title']); ?></td>
                    <td><?php echo $item['quantity']; ?></td>
                    <td><?php echo number_format($item['price']); ?>đ</td>
                    <td><?php echo number_format($item['price'] * $item['quantity']); ?>đ</td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="5" style="text-align: center;">Không có sản phẩm</td>
            </tr>
        <?php endif; ?>
    </tbody>
        </table>

        <div class="total">
            <p>Tổng tiền: <?php echo number_format($order['total_amount']); ?>đ</p>
            <p>Phương thức thanh toán: <?php echo $order['payment_method']; ?></p>
            <p>Trạng thái: <?php echo ucfirst($order['status']); ?></p>
        </div>

        <div class="buttons no-print">
            <button class="btn btn-print" onclick="window.print()">In hóa đơn</button>
            <a href="order_history.php" class="btn btn-back">Quay lại</a>
        </div>
    </div>
</body>
</html>