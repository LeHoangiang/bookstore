<?php
session_start();
require_once 'config.php';

// Lấy tất cả đơn hàng
$stmt = $pdo->prepare("
    SELECT o.*, GROUP_CONCAT(oi.book_id) as book_ids, 
           GROUP_CONCAT(oi.quantity) as quantities,
           GROUP_CONCAT(oi.price) as prices
    FROM orders o
    LEFT JOIN order_items oi ON o.order_id = oi.order_id
    GROUP BY o.order_id
    ORDER BY o.created_at DESC
");
$stmt->execute();
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Highlight đơn hàng vừa thanh toán nếu có
$lastOrderId = isset($_SESSION['last_order_id']) ? $_SESSION['last_order_id'] : null;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Lịch sử mua hàng</title>
   <link rel="stylesheet" href="order_history.css">
</head>
<body>
    <a href="home.php" class="btn btn-secondary home-btn">← Trang chủ</a>
    <h1>Lịch sử mua hàng</h1>
    
    <div class="orders-container">
        <?php if (empty($orders)): ?>
            <div style="text-align: center; padding: 20px;">
                <p>Chưa có đơn hàng nào.</p>
            </div>
        <?php else: ?>
            <?php foreach ($orders as $order): ?>
                <div class="order-item <?php echo ($order['order_id'] == $lastOrderId) ? 'highlight' : ''; ?>">
                    <div class="order-header">
                        <h3>Đơn hàng: <?php echo htmlspecialchars($order['order_id']); ?></h3>
                        <span class="order-date"><strong>Ngày giao dịch:</strong><?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></span>
                        <span class="order-status status-<?php echo $order['status']; ?>">
                            <?php 
                            switch($order['status']) {
                                case 'completed':
                                    echo 'Hoàn thành';
                                    break;
                                case 'pending':
                                    echo 'Đang xử lý';
                                    break;
                                case 'failed':
                                    echo 'Thất bại';
                                    break;
                                default:
                                    echo ucfirst($order['status']);
                            }
                            ?>
                        </span>
                    </div>
                    <div class="order-details">
                        <p><strong>Tổng tiền:</strong> <?php echo number_format($order['total_amount']); ?>đ</p>
                        <p><strong>Phương thức thanh toán:</strong> <?php echo $order['payment_method']; ?></p>
                        <?php if ($order['transaction_id']): ?>
                            <p><strong>Mã giao dịch:</strong> <?php echo htmlspecialchars($order['transaction_id']); ?></p>
                        <?php endif; ?>
                    </div>
                    <!-- Trong phần hiển thị đơn hàng -->
                    <div class="order-actions">
                        <a href="print_invoice.php?order_id=<?php echo htmlspecialchars($order['order_id']); ?>" 
                            class="btn btn-success" 
                            target="_blank">In hóa đơn</a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</body>
</html>