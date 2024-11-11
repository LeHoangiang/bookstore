<?php
session_start();
$conn = mysqli_connect("localhost:3307", "root", "", "bookstore_db");

if (!$conn) {
    die("Kết nối thất bại: " . mysqli_connect_error());
}

// Lấy thông tin giỏ hàng
$cart_items = [];
if (isset($_SESSION['cart_id'])) {
    $sql = "SELECT c.*, b.title, b.price, b.image_url 
            FROM cart c 
            JOIN books b ON c.book_id = b.id 
            WHERE c.session_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $_SESSION['cart_id']);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    while ($row = mysqli_fetch_assoc($result)) {
        $cart_items[] = $row;
    }
}

$total_items = count($cart_items);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giỏ hàng</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="gio_hang.css">
</head>
<body>
</head>
  <body>
    <header>
      <div class="top-banner">
        <p>THẮP LỬA TRI THỨC - KIẾN TẠO TƯƠNG LAI</p>
        <button class="discount-button">-35% MUA NGAY</button>
      </div>
      <div class="main-header">
        <div class="logo" style="width: 150px">
          <img src="images/logo.png" alt="Nhóm 11 Logo" />
        </div>
        <div class="search-bar">
          <input type="text" placeholder="Trắng - Han Kang giải Nobel 2024" />
          <button type="submit">
            <img src="images/search-icon.png" alt="Search" />
          </button>
        </div>
        <div class="icons">
          <a href="#"
            ><img src="images/bell-icon.png" alt="Thông Báo" /><span
              >Thông Báo</span
            ></a
          >
          <a href="cart.php" class="cart-icon">
    <img src="images/cart-icon.png" alt="Giỏ Hàng" />
    <span>Giỏ Hàng</span>
    <!-- <span id="cart-count" class="cart-count">0</span> -->
</a>
          <a href="#"
            ><img src="images/user-icon.png" alt="Tài Khoản" /><span
              >Tài Khoản</span
            ></a
          >
          <button class="singUp">
            <i class="fas fa-user-plus"></i
            ><span style="margin-left: 8px">Đăng kí</span>
          </button>
          <button class="login">
            <i class="fas fa-sign-in-alt"></i
            ><span style="margin-left: 8px">Đăng nhập</span>
          </button>
        </div>
      </div>
    </header>
    <div class="cart-container">
        <h2>GIỎ HÀNG (<?php echo $total_items; ?> sản phẩm)</h2>
        
        <?php if (empty($cart_items)): ?>
            <p>Giỏ hàng trống</p>
            <a href="index.php" class="continue-shopping">Tiếp tục mua sắm</a>
        <?php else: ?>
            <div class="cart-header">
                <input type="checkbox" id="select-all" onchange="toggleAllItems()"/>
                <span>Chọn tất cả (<?php echo $total_items; ?> sản phẩm)</span>
                <span>Số lượng</span>
                <span>Thành tiền</span>
            </div>
            
            <?php foreach ($cart_items as $item): ?>
                <div class="cart-item">
                    <div class="cart-item-info">
                        <input type="checkbox" class="item-checkbox" data-id="<?php echo $item['id']; ?>"/>
                        <img src="<?php echo htmlspecialchars($item['image_url']); ?>" 
                             alt="<?php echo htmlspecialchars($item['title']); ?>" />
                        <div class="item-details">
                            <p class="item-title"><?php echo htmlspecialchars($item['title']); ?></p>
                            <p class="price-current"><?php echo number_format($item['price']); ?> đ</p>
                        </div>
                    </div>
                    <div class="cart-item-actions">
                        <div class="quantity-control">
                            <button onclick="updateQuantity(<?php echo $item['id']; ?>, -1)">-</button>
                            <span><?php echo $item['quantity']; ?></span>
                            <button onclick="updateQuantity(<?php echo $item['id']; ?>, 1)">+</button>
                        </div>
                        <p class="price-total">
                            <?php echo number_format($item['price'] * $item['quantity']); ?> đ
                        </p>
                        <button class="delete-btn" onclick="removeItem(<?php echo $item['id']; ?>)">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>

            <div class="cart-summary">
                <div class="total">
                    <span>Tổng tiền:</span>
                    <span class="total-amount"><?php 
                        $total = array_sum(array_map(function($item) {
                            return $item['price'] * $item['quantity'];
                        }, $cart_items));
                        echo number_format($total);
                    ?> đ</span>
                </div>
                <button class="checkout-button" onclick="proceedToCheckout()">Thanh toán</button>
            </div>
        <?php endif; ?>
    </div>
    <footer>
      <!-- <div class="footer-top">
              <img src="brands.png" alt="Brands">
          </div> -->
      <!-- <div class="footer-top">
              <img src="brands.png" alt="Brands">
          </div> -->
      <div class="newsletter"></div>
      <div class="footer-main">
        <div class="footer-column company-info">
          <img src="images/logo(1)(1).png" alt="Nhóm 11" />
          <p>Phòng G503, Lầu 5, Học viện Hàng Không Việt Nam</p>
          <p>Học viện Hàng Không Việt Nam - NHÓM 11</p>
          <p>18A/1 Cộng Hòa, Phường 4, Quận Tân Bình, TP.HCM</p>
          <p>Nhận đặt hàng trực tuyến và giao hàng tận nơi.</p>
          <img src="images/bo-cong-thuong.png" alt="Bộ Công Thương" />
          <div class="social-icons">
            <img src="images/facebook.png" alt="Facebook" />
            <img src="images/youtube.png" alt="YouTube" />
            <img src="images/instagram.png" alt="Instagram" />
          </div>
        </div>
        <div class="footer-column">
          <h3>DỊCH VỤ</h3>
          <p>Điều khoản sử dụng</p>
          <p>Chính sách bảo mật thông tin cá nhân</p>
          <p>Chính sách bảo mật thanh toán</p>
          <p>Hệ thống trung tâm - nhà sách</p>
        </div>
        <div class="footer-column">
          <h3>HỖ TRỢ</h3>
          <p>Chính sách đổi - trả - hoàn tiền</p>
          <p>Chính sách bảo hành - bồi hoàn</p>
          <p>Chính sách vận chuyển</p>
          <p>Chính sách khách sĩ</p>
        </div>
        <div class="footer-column">
          <h3>TÀI KHOẢN CỦA TÔI</h3>
          <p>Đăng nhập/Tạo mới tài khoản</p>
          <p>Thay đổi địa chỉ khách hàng</p>
          <p>Chi tiết tài khoản</p>
          <p>Lịch sử mua hàng</p>
        </div>
        <div class="footer-column contact">
          <h3>LIÊN HỆ</h3>
          <p>18A/1 Cộng Hòa, Phường 4, Quận Tân Bình, TP.HCM</p>
          <p>Email: 22dhtt@vaa.edu.vn</p>
          <p>Điện thoại: 1900636467</p>
          <div class="partner-logos">
            <img
              src="images/vnpay.png"
              style="width: 150px; padding-right: 10px"
              alt="VNPay"
            />
            <img src="images/momo.png" alt="MoMo" />
          </div>
        </div>
      </div>
      <div class="footer-bottom">
        <p>
          Giấy chứng nhận Đăng ký Kinh doanh số 0304132047 do Sở Kế hoạch và Đầu
          tư Thành phố Hồ Chí Minh cấp ngày 20/12/2005, đăng ký thay đổi lần thứ
          10, ngày 20/05/2022.
        </p>
      </div>
    </footer>
    <script>
    function updateQuantity(cartId, change) {
        fetch('./update_cart.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                cart_id: cartId,
                change: change
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message || 'Có lỗi xảy ra');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Có lỗi xảy ra khi cập nhật số lượng');
        });
    }

    function removeItem(cartId) {
        if (confirm('Bạn có chắc chắn muốn xóa sản phẩm này?')) {
            fetch('./remove_cart_item.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    cart_id: cartId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.message || 'Có lỗi xảy ra');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Có lỗi xảy ra khi xóa sản phẩm');
            });
        }
    }

    function toggleAllItems() {
        const selectAll = document.getElementById('select-all');
        const checkboxes = document.querySelectorAll('.item-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = selectAll.checked;
        });
    }
    function proceedToCheckout() {
    // Kiểm tra xem có sản phẩm nào được chọn không
    const checkedItems = document.querySelectorAll('.item-checkbox:checked');
    if (checkedItems.length === 0) {
        alert('Vui lòng chọn ít nhất một sản phẩm để thanh toán');
        return;
    }

    // Lấy danh sách ID các sản phẩm được chọn
    const selectedItems = Array.from(checkedItems).map(checkbox => 
        checkbox.getAttribute('data-id')
    );

    // Chuyển hướng đến trang checkout với danh sách sản phẩm đã chọn
    window.location.href = 'checkout.php?items=' + selectedItems.join(',');
}
    </script>
</body>
</html>