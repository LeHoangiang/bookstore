<?php
session_start();
require_once 'config.php';

// Lấy thông tin giỏ hàng
$cart_items = [];
$total = 0;
if (isset($_SESSION['cart_id'])) {
    $sql = "SELECT c.*, b.title, b.price, b.image_url 
            FROM cart c 
            JOIN books b ON c.book_id = b.id 
            WHERE c.session_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$_SESSION['cart_id']]);
    $cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Tính tổng tiền
    foreach ($cart_items as $item) {
        $total += $item['quantity'] * $item['price'];
    }
}

// Nếu không có sản phẩm trong giỏ, chuyển về trang giỏ hàng
if (empty($cart_items)) {
    header('Location: cart.php');
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Thanh toán đơn hàng</title>
    <link rel="stylesheet" href="thanh_toan.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <form action="process_order.php" method="POST" id="checkout-form">
        <div class="order-summary">
            <h2>KIỂM TRA LẠI ĐƠN HÀNG</h2>
            <hr />
            <?php foreach ($cart_items as $item): ?>
            <div class="product">
                <img src="<?php echo htmlspecialchars($item['image_url']); ?>" 
                     alt="<?php echo htmlspecialchars($item['title']); ?>" />
                <div class="product-info">
                    <p><?php echo htmlspecialchars($item['title']); ?></p>
                    <p>Giá: <?php echo number_format($item['price']); ?>₫</p>
                    <p>Số lượng: <?php echo $item['quantity']; ?></p>
                    <p>Thành tiền: <?php echo number_format($item['price'] * $item['quantity']); ?>₫</p>
                </div>
            </div>
            <?php endforeach; ?>
            
            <div class="total">
                <p>Tổng tiền: <?php echo number_format($total); ?>₫</p>
            </div>
        </div>

        <div class="form-container">
            <h2>ĐỊA CHỈ GIAO HÀNG</h2>
            <div class="form-group">
                <label for="ho-ten">Họ và tên người nhận:</label>
                <input type="text" id="ho-ten" name="ho-ten" required />
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required />
            </div>
            <div class="form-group">
                <label for="so-dien-thoai">Số điện thoại:</label>
                <input type="tel" id="so-dien-thoai" name="so-dien-thoai" required />
            </div>
            <div class="form-group">
                <label for="tinh-thanh-pho">Tỉnh/Thành Phố:</label>
                <select id="tinh-thanh-pho" name="tinh-thanh-pho" required>
                    <option value="">Chọn Tỉnh/Thành Phố</option>
                    <!-- Sẽ được điền bởi API -->
                </select>
            </div>
            <div class="form-group">
                <label for="quan-huyen">Quận/Huyện:</label>
                <select id="quan-huyen" name="quan-huyen" required>
                    <option value="">Chọn Quận/Huyện</option>
                </select>
            </div>
            <div class="form-group">
                <label for="phuong-xa">Phường/Xã:</label>
                <select id="phuong-xa" name="phuong-xa" required>
                    <option value="">Chọn Phường/Xã</option>
                </select>
            </div>
            <div class="form-group">
                <label for="dia-chi">Địa chỉ giao hàng:</label>
                <input type="text" id="dia-chi" name="dia-chi" 
                       placeholder="Nhập địa chỉ giao hàng cụ thể" required />
            </div>
        </div>

        <div class="payment-methods">
            <h2>Phương thức thanh toán</h2>
            <div class="payment-option">
                <input type="radio" name="payment_method" id="momo" value="momo" required />
                <label for="momo">
                    <img src="images/momo-logo.png" alt="MoMo" /> Ví MoMo
                </label>
            </div>
            <div class="payment-option">
                <input type="radio" name="payment_method" id="vnpay" value="vnpay" required />
                <label for="vnpay">
                    <img src="images/vnpay-logo.png" alt="VNPay" /> VNPay
                </label>
            </div>
            <div class="payment-option">
                <input type="radio" name="payment_method" id="cash" value="cod" required />
                <label for="cash">
                    <i class="fas fa-money-bill-wave"></i> Thanh toán khi nhận hàng
                </label>
            </div>
        </div>

        <div class="container">
            <h2>THÔNG TIN KHÁC</h2>
            <div class="form-group">
                <label for="ghi-chu">Ghi chú:</label>
                <textarea id="ghi-chu" name="ghi-chu" rows="3"></textarea>
            </div>
        </div>

        <button type="submit" class="checkout-button">Đặt hàng</button>
    </form>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.21.1/axios.min.js"></script>
    <script>
var citis = document.getElementById("tinh-thanh-pho");
var districts = document.getElementById("quan-huyen");
var wards = document.getElementById("phuong-xa");

//API địa chỉ VN
var Parameter = {
    url: "https://raw.githubusercontent.com/kenzouno1/DiaGioiHanhChinhVN/master/data.json",
    method: "GET",
    responseType: "application/json",
};

// Load dữ liệu địa chỉ
axios.get(Parameter.url)
    .then(function (response) {
        renderCity(response.data);
    });

function renderCity(data) {
    for (const x of data) {
        citis.options[citis.options.length] = new Option(x.Name, x.Id);
    }

    // Xử lý khi tỉnh/thành phố thay đổi
    citis.onchange = function () {
        districts.length = 1;
        wards.length = 1;
        
        if(this.value !== "") {
            const result = data.filter(n => n.Id === this.value);

            for (const k of result[0].Districts) {
                districts.options[districts.options.length] = new Option(k.Name, k.Id);
            }
        }
    };

    // Xử lý khi quận/huyện thay đổi
    districts.onchange = function () {
        wards.length = 1;
        
        const dataCity = data.filter((n) => n.Id === citis.value);
        if (this.value !== "") {
            const dataWards = dataCity[0].Districts.filter(n => n.Id === this.value)[0].Wards;

            for (const w of dataWards) {
                wards.options[wards.options.length] = new Option(w.Name, w.Id);
            }
        }
    };
}

// Xử lý form submit
document.getElementById('checkout-form').onsubmit = function(e) {
    e.preventDefault();
    
    // Lấy tên địa chỉ thay vì ID
    const province = citis.options[citis.selectedIndex].text;
    const district = districts.options[districts.selectedIndex].text;
    const ward = wards.options[wards.selectedIndex].text;
    
    // Thêm input ẩn để gửi tên địa chỉ
    const form = this;
    const hiddenInputs = `
        <input type="hidden" name="province_name" value="${province}">
        <input type="hidden" name="district_name" value="${district}">
        <input type="hidden" name="ward_name" value="${ward}">
    `;
    form.insertAdjacentHTML('beforeend', hiddenInputs);
    
    const paymentMethod = document.querySelector('input[name="payment_method"]:checked').value;
    
    switch(paymentMethod) {
        case 'momo':
            form.action = 'process_momo_payment.php';
            break;
        case 'vnpay':
            form.action = 'process_vnpay_payment.php';
            break;
        case 'cod':
            form.action = 'process_cod_order.php';
            break;
    }
    
    form.submit();
};
</script>
</body>
</html>