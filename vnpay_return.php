<?php
session_start();
require_once('config.php');

// Lấy các tham số trả về từ VNPay
$vnp_ResponseCode = $_GET['vnp_ResponseCode']; 
$vnp_TxnRef = $_GET['vnp_TxnRef']; 
$vnp_Amount = $_GET['vnp_Amount']/100; 
$vnp_OrderInfo = $_GET['vnp_OrderInfo'];
$vnp_TransactionNo = $_GET['vnp_TransactionNo'];
$vnp_BankCode = $_GET['vnp_BankCode'];
$vnp_PayDate = $_GET['vnp_PayDate'];

// Kiểm tra chữ ký
$inputData = array();
foreach ($_GET as $key => $value) {
    if (substr($key, 0, 4) == "vnp_") {
        $inputData[$key] = $value;
    }
}
unset($inputData['vnp_SecureHash']);
ksort($inputData);
$i = 0;
$hashData = "";
foreach ($inputData as $key => $value) {
    if ($i == 1) {
        $hashData = $hashData . '&' . urlencode($key) . "=" . urlencode($value);
    } else {
        $hashData = $hashData . urlencode($key) . "=" . urlencode($value);
        $i = 1;
    }
}

$secureHash = hash_hmac('sha512', $hashData, VNPAY_HASH_SECRET);
$vnp_SecureHash = $_GET['vnp_SecureHash'];

// Chuẩn bị nội dung HTML
$pageContent = '';

// Xử lý kết quả trả về
if ($secureHash == $vnp_SecureHash) {
    if ($vnp_ResponseCode == '00') {
        // Giao dịch thành công
        try {
            $stmt = $pdo->prepare("UPDATE orders SET 
                status = 'completed',
                transaction_id = ?,
                payment_method = 'vnpay',
                updated_at = NOW()
                WHERE order_id = ?");
            
            $stmt->execute([$vnp_TransactionNo, $vnp_TxnRef]);
            
            // Lưu order_id vào session
            $_SESSION['last_order_id'] = $vnp_TxnRef;

            $pageContent = '
                <div class="container">
                    <div class="success-icon">✓</div>
                    <h2 style="color: #28a745;">Thanh toán thành công</h2>
                    
                    <div class="order-info">
                        <p><strong>Mã đơn hàng:</strong> ' . htmlspecialchars($vnp_TxnRef) . '</p>
                        <p><strong>Số tiền:</strong> ' . number_format($vnp_Amount) . ' VNĐ</p>
                        <p><strong>Nội dung:</strong> ' . htmlspecialchars($vnp_OrderInfo) . '</p>
                        <p><strong>Mã giao dịch:</strong> ' . htmlspecialchars($vnp_TransactionNo) . '</p>
                        <p><strong>Ngân hàng:</strong> ' . htmlspecialchars($vnp_BankCode) . '</p>
                        <p><strong>Thời gian:</strong> ' . date('d/m/Y H:i:s', strtotime($vnp_PayDate)) . '</p>
                    </div>

                    <div class="buttons">
                        <a href="order_history.php" class="btn btn-primary">Xem lịch sử đơn hàng</a>
                        <a href="print_invoice.php?order_id=' . $vnp_TxnRef . '" class="btn btn-success" target="_blank">In hóa đơn</a>
                        <a href="home.php" class="btn btn-secondary">Về trang chủ</a>
                    </div>
                </div>';
        } catch(PDOException $e) {
            $pageContent = '
                <div class="container">
                    <div class="error-icon">!</div>
                    <h2 style="color: #dc3545;">Lỗi cập nhật dữ liệu</h2>
                    <p>' . $e->getMessage() . '</p>
                    <div class="buttons">
                        <a href="home.php" class="btn btn-secondary">Về trang chủ</a>
                    </div>
                </div>';
        }
    } else {
        // Giao dịch thất bại
        try {
            $stmt = $pdo->prepare("UPDATE orders SET 
                status = 'failed',
                transaction_id = ?,
                payment_method = 'vnpay',
                updated_at = NOW()
                WHERE order_id = ?");
            
            $stmt->execute([$vnp_TransactionNo, $vnp_TxnRef]);
            
            $pageContent = '
                <div class="container">
                    <div class="error-icon">×</div>
                    <h2 style="color: #dc3545;">Thanh toán thất bại</h2>
                    <p>Mã lỗi: ' . htmlspecialchars($vnp_ResponseCode) . '</p>
                    <p>Mã đơn hàng: ' . htmlspecialchars($vnp_TxnRef) . '</p>
                    <p>Vui lòng thử lại hoặc chọn phương thức thanh toán khác.</p>
                    <div class="buttons">
                        <a href="home.php" class="btn btn-secondary">Về trang chủ</a>
                    </div>
                </div>';
        } catch(PDOException $e) {
            $pageContent = '
                <div class="container">
                    <div class="error-icon">!</div>
                    <h2 style="color: #dc3545;">Lỗi cập nhật dữ liệu</h2>
                    <p>' . $e->getMessage() . '</p>
                    <div class="buttons">
                        <a href="home.php" class="btn btn-secondary">Về trang chủ</a>
                    </div>
                </div>';
        }
    }
} else {
    // Chữ ký không hợp lệ
    $pageContent = '
        <div class="container">
            <div class="error-icon">!</div>
            <h2 style="color: #dc3545;">Lỗi giao dịch</h2>
            <p>Chữ ký không hợp lệ</p>
            <p>Vui lòng liên hệ admin để được hỗ trợ.</p>
            <div class="buttons">
                <a href="home.php" class="btn btn-secondary">Về trang chủ</a>
            </div>
        </div>';
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Kết quả thanh toán</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 50px auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            text-align: center;
        }
        .success-icon {
            color: #28a745;
            font-size: 48px;
            margin-bottom: 20px;
        }
        .error-icon {
            color: #dc3545;
            font-size: 48px;
            margin-bottom: 20px;
        }
        .order-info {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
            text-align: left;
        }
        .order-info p {
            margin: 10px 0;
            color: #333;
        }
        .buttons {
            margin-top: 30px;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            margin: 0 10px 10px 10px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            transition: opacity 0.2s;
        }
        .btn:hover {
            opacity: 0.8;
        }
        .btn-primary {
            background-color: #007bff;
            color: white;
        }
        .btn-success {
            background-color: #28a745;
            color: white;
        }
        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }
    </style>
</head>
<body>
    <?php echo $pageContent; ?>
</body>
</html>