<?php
// Database configuration
$host = 'localhost:3307';
$dbname = 'bookstore_db';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}

// MoMo Payment Configuration
define('MOMO_PARTNER_CODE', 'MOMOBKUN20180529'); // Thay bằng partner code thật
define('MOMO_ACCESS_KEY', 'klm05TvNBzhg7h7j'); // Thay bằng access key thật
define('MOMO_SECRET_KEY', 'at67qH6mk8w5Y1nAyMoYKMWACiEi2bsa'); // Thay bằng secret key thật
define('MOMO_API_ENDPOINT', 'https://test-payment.momo.vn/v2/gateway/api/create');
define('MOMO_REDIRECT_URL', 'https://webhook.site/b3088a6a-2d17-4f8d-a383-71389a6c600b');
define('MOMO_IPN_URL', 'https://webhook.site/b3088a6a-2d17-4f8d-a383-71389a6c600b');

// VNPay Payment Configuration
define('VNPAY_TMN_CODE', 'CGXZLS0Z'); // Điền mã website của bạn tại VNPAY
define('VNPAY_HASH_SECRET', 'XNBCJFAKAZQSGTARRLGCHVZWCIOIGSHN'); // Điền secret key
define('VNPAY_URL', 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html');
define('VNPAY_RETURN_URL', 'http://localhost/home/vnpay_return.php');

// Common functions
function getBookDetails($bookId) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM books WHERE id = ?");
    $stmt->execute([$bookId]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getRelatedBooks($categoryId, $currentBookId, $limit = 6) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM books WHERE category_id = ? AND id != ? LIMIT ?");
    $stmt->execute([$categoryId, $currentBookId, $limit]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// MoMo Payment Function
function createMoMoPayment($orderId, $amount, $orderInfo) {
    $requestId = time() . "";
    
    $rawHash = "accessKey=" . MOMO_ACCESS_KEY .
        "&amount=" . $amount .
        "&extraData=" .
        "&ipnUrl=" . MOMO_IPN_URL .
        "&orderId=" . $orderId .
        "&orderInfo=" . $orderInfo .
        "&partnerCode=" . MOMO_PARTNER_CODE .
        "&redirectUrl=" . MOMO_REDIRECT_URL .
        "&requestId=" . $requestId .
        "&requestType=captureWallet";
        
    $signature = hash_hmac('sha256', $rawHash, MOMO_SECRET_KEY);
    
    $data = array(
        'partnerCode' => MOMO_PARTNER_CODE,
        'requestId' => $requestId,
        'amount' => $amount,
        'orderId' => $orderId,
        'orderInfo' => $orderInfo,
        'redirectUrl' => MOMO_REDIRECT_URL,
        'ipnUrl' => MOMO_IPN_URL,
        'requestType' => 'captureWallet',
        'extraData' => '',
        'lang' => 'vi',
        'signature' => $signature
    );

    $ch = curl_init(MOMO_API_ENDPOINT);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json'
    ));
    
    $result = curl_exec($ch);
    curl_close($ch);
    
    return json_decode($result, true);
}

// VNPay Payment Function
function createVNPayPayment($orderId, $amount, $orderInfo) {
    $vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
    $vnp_Returnurl = "http://localhost/home/vnpay_return.php"; // Sửa lại URL phù hợp với domain của bạn
    $vnp_TmnCode = "CGXZLS0Z"; // Mã website tại VNPAY 
    $vnp_HashSecret = "XNBCJFAKAZQSGTARRLGCHVZWCIOIGSHN"; // Chuỗi bí mật



    $vnp_TxnRef = preg_replace('/[^A-Za-z0-9]/', '', $orderId); // Chỉ giữ lại chữ và số    $vnp_Amount = $amount * 100; // Số tiền * 100 (VNPay yêu cầu)
    $vnp_Locale = 'vn'; // Ngôn ngữ
    $vnp_BankCode = ''; // Có thể để trống để khách chọn ngân hàng thanh toán
    $vnp_IpAddr = $_SERVER['REMOTE_ADDR'];
    
    $inputData = array(
        "vnp_Version" => "2.1.0",
        "vnp_TmnCode" => VNPAY_TMN_CODE,
        "vnp_Amount" => $amount * 100,
        "vnp_Command" => "pay",
        "vnp_CreateDate" => date('YmdHis'),
        "vnp_CurrCode" => "VND",
        "vnp_IpAddr" => $_SERVER['REMOTE_ADDR'],
        "vnp_Locale" => 'vn',
        "vnp_OrderInfo" => $orderInfo,
        "vnp_OrderType" => "other", // Thay đổi từ billpayment thành other
        "vnp_ReturnUrl" => VNPAY_RETURN_URL,
        "vnp_TxnRef" => $vnp_TxnRef
    );

    ksort($inputData);
    $query = "";
    $i = 0;
    $hashdata = "";
    
    foreach ($inputData as $key => $value) {
        if ($i == 1) {
            $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
        } else {
            $hashdata .= urlencode($key) . "=" . urlencode($value);
            $i = 1;
        }
        $query .= urlencode($key) . "=" . urlencode($value) . '&';
    }

    $vnp_Url = VNPAY_URL . "?" . $query;
    $vnpSecureHash = hash_hmac('sha512', $hashdata, VNPAY_HASH_SECRET);
    $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
    
    return $vnp_Url;
}
?>
