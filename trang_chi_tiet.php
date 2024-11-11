<?php
session_start();
// Kết nối database
$conn = mysqli_connect("localhost:3307", "root", "", "bookstore_db");

// Kiểm tra kết nối
if (!$conn) {
    die("Kết nối thất bại: " . mysqli_connect_error());
}
$base_url = '/home';
// Lấy ID sách từ URL
$book_id = isset($_GET['id']) ? $_GET['id'] : 1; // Mặc định là 1 nếu không có ID

// Truy vấn thông tin sách
$sql = "SELECT books.* 
        FROM books 
        WHERE books.id = $book_id";
$result = mysqli_query($conn, $sql);
$book = mysqli_fetch_assoc($result);

// Nếu có category_id thì mới truy vấn thêm thông tin category
if (!empty($book['category_id'])) {
    $category_sql = "SELECT name FROM categories WHERE id = " . $book['category_id'];
    $category_result = mysqli_query($conn, $category_sql);
    if ($category_data = mysqli_fetch_assoc($category_result)) {
        $book['category_name'] = $category_data['name'];
    }
}

// Truy vấn sách liên quan (cùng thể loại)
if (!empty($book['category_id'])) {
    $related_sql = "SELECT * FROM books WHERE category_id = '{$book['category_id']}' AND id != $book_id LIMIT 6";
} else {
    $related_sql = "SELECT * FROM books WHERE id != $book_id LIMIT 6";
}
$related_result = mysqli_query($conn, $related_sql);

?>



<!DOCTYPE html>
<html lang="vi">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Chi tiết sách</title>

    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"
    />

    <link rel="stylesheet" href="style.css" />
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
    <span id="cart-count" class="cart-count">0</span>
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

    <div class="book-detail-container">
      <!-- Hình ảnh sách -->
      <div class="book-image">
        
        <img src="<?php echo htmlspecialchars($book['image_url']); ?>" 
        alt="<?php echo htmlspecialchars($book['title']); ?>" />
        <div class="button-container">
          <button class="add-to-cart-button" onclick="addToCart(<?php echo $book['id']; ?>)">
            <i class="fas fa-shopping-cart"></i
            ><span style="margin-left: 5px">Thêm vào giỏ hàng</span>
          </button>
          <button class="buy-button">Mua Ngay</button>
        </div>
        <div id="notification" class="notification" style="display: none;"></div>
        <div style="line-height: 1.5; margin-top: 10px">
          <span style="font-weight: bold">Chính sách ưu đãi </span><br /><span
            ><i class="fas fa-shipping-fast"></i>
            <strong>Thời gian giao hàng</strong>: Giao nhanh và uy tín
          </span>
          <br />
          <span
            ><i class="fas fa-exchange-alt"></i>
            <strong>Chính sách đổi trả</strong>: Đổi trả miễn phí toàn quốc </span
          ><br /><span
            ><i class="fas fa-boxes"></i> <strong>Chính sách khách sỉ</strong>:
            Ưu đãi khi mua số lượng lớn</span
          >
        </div>
      </div>

      <!-- Thông tin sách -->
<div class="right_container">
            <div class="book-info">
            <span class="book-title"><?php echo htmlspecialchars($book['title']); ?></span>
    <p class="book-author">Tác giả: <?php echo htmlspecialchars($book['author']); ?></p>
    <div class="price-info">
        <!-- Hiển thị giá khuyến mãi -->
        <p class="book-price">
            <span class="discounted-price"><?php echo number_format($book['discounted_price']); ?>đ</span>
            <span class="discount">-<?php echo $book['discount_percent']; ?>%</span>
        </p>
        <!-- Hiển thị giá gốc -->
        <p class="original-price">
            <del><?php echo number_format($book['price']); ?>đ</del>
        </p>
    </div>
                <div class="book-rating">
                    <span>⭐⭐⭐⭐⭐</span>
                    <span> Đã bán <?php echo $book['sales_count']; ?></span>
                </div>
            </div>
            
            <div class="infor">
              <span style="font-weight: bold; font-size: 24px">Thông tin chi tiết</span>
              <p class="book-genre">
                  Thể loại: <?php echo isset($book['category_name']) ? htmlspecialchars($book['category_name']) : 'Chưa phân loại'; ?>
              </p>
              <p>
                  Tác giả: <?php echo !empty($book['author']) ? htmlspecialchars($book['author']) : 'Chưa cập nhật'; ?>
              </p>
              <p>
                  Nhà xuất bản: <?php echo !empty($book['publisher']) ? htmlspecialchars($book['publisher']) : 'Chưa cập nhật'; ?>
              </p>
              <p class="book-manufacture">
                  Năm sản xuất: <?php echo !empty($book['publish_year']) ? $book['publish_year'] : 'Chưa cập nhật'; ?>
              </p>
              <p class="book-pages">
                  Số trang: <?php echo !empty($book['pages']) ? $book['pages'] : 'Chưa cập nhật'; ?>
              </p>
              <p>
                  Hình thức: <?php echo !empty($book['format']) ? htmlspecialchars($book['format']) : 'Chưa cập nhật'; ?>
              </p>
</div>

            <div class="mo_ta">
                <span style="font-weight: bold; font-size: 24px">Mô tả sản phẩm</span>
                <p><?php echo nl2br(htmlspecialchars($book['description'])); ?></p>
            </div>
        </div>
    </div>

    <div class="container_1">
        <h2 style="margin-left: 50px">Các sách liên quan</h2>
        <div class="container_2">
            <?php while ($related_book = mysqli_fetch_assoc($related_result)) : ?>
            <div class="book-item">
                <a href="trang_chi_tiet.php?id=<?php echo $related_book['id']; ?>">
                    <img src="<?php echo htmlspecialchars($related_book['image_url']); ?>" 
                         alt="<?php echo htmlspecialchars($related_book['title']); ?>" />
                    <div class="book-info">
                        <p><?php echo htmlspecialchars($related_book['title']); ?></p>
                        <div class="price-info">
                            <span class="discounted-price"><?php echo number_format($related_book['discounted_price']); ?>đ</span>
                            <span class="discount">-<?php echo $related_book['discount_percent']; ?>%</span><br />
                            <del><?php echo number_format($related_book['price']); ?>đ</del>
                        </div>
                        <div class="sales-info">
                            <div class="chua_ban">
                                <span class="sales-count">Đã bán: <?php echo $related_book['sales_count']; ?></span>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <?php endwhile; ?>
        </div>
    </div>

       
    <div class="review">
      <div class="text_review"><span>Đánh giá sản phẩm</span></div>
      <div class="rating-overall">
        <div class="rating-score">
          <span class="score">5</span><span>/5</span>
        </div>
        <div class="stars">
          <span class="star">★</span><span class="star">★</span
          ><span class="star">★</span><span class="star">★</span
          ><span class="star">★</span>
        </div>
        <div class="rating-count">(22 đánh giá)</div>
      </div>
      <div class="rating-details">
        <div class="rating-row">
          <span>5 sao</span>
          <div class="rating-bar">
            <div class="rating-fill" style="width: 100%"></div>
          </div>
          <span>100%</span>
        </div>
        <div class="rating-row">
          <span>4 sao</span>
          <div class="rating-bar">
            <div class="rating-fill" style="width: 0%"></div>
          </div>
          <span>0%</span>
        </div>
        <div class="rating-row">
          <span>3 sao</span>
          <div class="rating-bar">
            <div class="rating-fill" style="width: 0%"></div>
          </div>
          <span>0%</span>
        </div>
        <div class="rating-row">
          <span>2 sao</span>
          <div class="rating-bar">
            <div class="rating-fill" style="width: 0%"></div>
          </div>
          <span>0%</span>
        </div>
        <div class="rating-row">
          <span>1 sao</span>
          <div class="rating-bar">
            <div class="rating-fill" style="width: 0%"></div>
          </div>
          <span>0%</span>
        </div>
      </div>
      <div class="note">
        <span
          >Chỉ có thành viên mới có thể viết đánh giá. Vui lòng
          <a href="">đăng nhập </a>hoặc <a href="">đăng ký</a>.</span
        >
      </div>
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
      function updateCartCount() {
    fetch('./get_cart_count.php')
    .then(response => response.json())
    .then(data => {
        const cartCount = document.querySelector('.cart-count');
        if (cartCount) {
            cartCount.textContent = data.count;
        }
    })
    .catch(error => console.error('Error updating cart count:', error));
}
      function addToCart(bookId) {
          console.log('Adding book:', bookId);
          
          const formData = new FormData();
          formData.append('book_id', bookId);
          formData.append('quantity', 1);

          fetch('./add_to_cart.php', {
              method: 'POST',
              body: formData
          })
          .then(response => response.json())
          .then(data => {
              console.log('Data:', data);
              
              const notification = document.getElementById('notification');
              if (notification) {
                  notification.textContent = data.message;
                  notification.style.display = 'block';
                  
                  if (data.success) {
                      notification.className = 'notification success';
                      updateCartCount();
                      
                      // Hiển thị thông báo và chuyển hướng sau 2 giây
                      setTimeout(() => {
                          window.location.href = 'cart.php';
                      }, 2000); // 2000ms = 2 giây
                  } else {
                      notification.className = 'notification error';
                      setTimeout(() => {
                          notification.style.display = 'none';
                      }, 3000);
                  }
              }
          })
          .catch(error => {
              console.error('Error:', error);
              alert('Có lỗi xảy ra khi thêm vào giỏ hàng');
          });
      } 
</script>

    <?php
    // Đóng kết nối database
    mysqli_close($conn);
    ?>
    
  </body>
</html>
