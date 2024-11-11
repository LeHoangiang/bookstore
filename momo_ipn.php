<?php
require_once 'config.php';

// Nhận dữ liệu từ MoMo
$inputData = file_get_contents('php://input');
$result = json_decode($inputData, true);

// Ghi log để debug
$logFile = fopen("momo_ipn_log.txt", "a");
fwrite($logFile, date('Y-m-d H:i:s') . ": " . $inputData . "\n");

if ($result['resultCode'] == 0) {
    $orderId = $result['orderId'];
    
    // Xác thực chữ ký
    $rawHash = "accessKey=" . MOMO_ACCESS_KEY .
               "&amount=" . $result['amount'] .
               "&extraData=" . $result['extraData'] .
               "&orderId=" . $result['orderId'] .
               "&orderInfo=" . $result['orderInfo'] .
               "&orderType=" . $result['orderType'] .
               "&partnerCode=" . $result['partnerCode'] .
               "&payType=" . $result['payType'] .
               "&requestId=" . $result['requestId'] .
               "&responseTime=" . $result['responseTime'] .
               "&resultCode=" . $result['resultCode'] .
               "&transId=" . $result['transId'];

    $signature = hash_hmac('sha256', $rawHash, MOMO_SECRET_KEY);
    
    if ($signature == $result['signature']) {
        // Cập nhật trạng thái đơn hàng
        $sql = "UPDATE orders SET 
                status = 'completed',
                transaction_id = ?,
                payment_type = 'momo'
                WHERE order_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$result['transId'], $orderId]);
        
        fwrite($logFile, date('Y-m-d H:i:s') . ": Order " . $orderId . " updated successfully\n");
    } else {
        fwrite($logFile, date('Y-m-d H:i:s') . ": Invalid signature for order " . $orderId . "\n");
    }
}

fclose($logFile);

// Trả về response cho MoMo
echo json_encode(['message' => 'Received']);