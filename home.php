<?php
// Kết nối database
$conn = mysqli_connect("localhost:3307", "root", "", "bookstore_db");

// Kiểm tra kết nối
if (!$conn) {
    die("Kết nối thất bại: " . mysqli_connect_error());
}

// Lấy sách flash sale
$flash_sale_sql = "SELECT * FROM books WHERE is_flash_sale = 1 LIMIT 8";
$flash_sale_result = mysqli_query($conn, $flash_sale_sql);

// Lấy sách xu hướng thường ngày
$trending_daily_sql = "SELECT * FROM books WHERE category_id = 1 LIMIT 10"; 
$trending_daily_result = mysqli_query($conn, $trending_daily_sql);

// Lấy sách nhân quan sinh
$trending_life_sql = "SELECT * FROM books WHERE category_id = 2 LIMIT 10";
$trending_life_result = mysqli_query($conn, $trending_life_sql);

// Lấy sách bestseller ngoại văn
$trending_foreign_sql = "SELECT * FROM books WHERE category_id = 3 LIMIT 10";
$trending_foreign_result = mysqli_query($conn, $trending_foreign_sql);

// Lấy sách bán chạy
$bestseller_sql = "SELECT * FROM books ORDER BY sales_count DESC LIMIT 9";
$bestseller_result = mysqli_query($conn, $bestseller_sql);

?>



<!DOCTYPE html>
<html lang="vi">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Giới thiệu</title>
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"
    />
    <link rel="stylesheet" href="styles.css" />
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
    <!-- Slider -->
    <div class="slideshow-container">
      <div class="slides">
        <img class="slide" src="./images/anh8.jpg" alt="Poster 1" />
        <img class="slide" src="./images/a10.jpg" alt="Poster 2" />
        <img class="slide" src="./images/a9.jpg" alt="Poster 3" />
        <img class="slide" src="./images/anh_5.png" alt="Poster 4" />
        <img class="slide" src="./images/a12.jpg" alt="Poster 5" />
        <img class="slide" src="./images/anh_7.jpg" alt="Poster 6" />
      </div>
      <button class="prev" onclick="changeSlide(-1)">&#10094;</button>
      <button class="next" onclick="changeSlide(1)">&#10095;</button>
    </div>

    <!-- thời gian flash sale -->
    <div class="boc_container">
      <div class="flash_sale">
        <div class="flash_sale_test">
          <span class="lightning-bolt">⚡</span> Flash Sale!
          <span class="lightning-bolt">⚡</span>
          <div class="countdown">
            <!-- <div class="time"><span id="days">00:</span></div> -->
            <div class="time">
              <span id="hours">00</span> <span class="colon">:</span>
            </div>
            <div class="time">
              <span id="minutes">00</span> <span class="colon">:</span>
            </div>
            <div class="time">
              <span id="seconds">00</span>
            </div>
          </div>
        </div>
        <span class="flash_sale_line"
          >Xem tất cả <i class="fa-solid fa-chevron-right"></i
        ></span>
      </div>

      <!-- Phần Flash Sale -->
      <div class="container" style="margin-left: 100px">
        <section class="books-sale">
          <div class="container_1">
            <div class="container_2">
              <?php while($book = mysqli_fetch_assoc($flash_sale_result)) : ?>
                <div class="book-item">
                  <div class="img-container" style="height: 350px">
                    <a href="trang_chi_tiet.php?id=<?php echo $book['id']; ?>">
                      <img src="<?php echo htmlspecialchars($book['image_url']); ?>" 
                       alt="<?php echo htmlspecialchars($book['title']); ?>" />
                    </a>
                  </div>
                  <div class="book-info">
                    <a href="trang_chi_tiet.php?id=<?php echo $book['id']; ?>">
                      <p><?php echo htmlspecialchars($book['title']); ?></p>
                    </a>
                  <div class="price-info">
                    <span class="discounted-price"><?php echo number_format($book['discounted_price']); ?>đ</span>
                    <span class="discount">-<?php echo $book['discount_percent']; ?>%</span>
                    <del><?php echo number_format($book['price']); ?>đ</del>
                    <div class="sales-info">
                      <div class="chua_ban">
                         <span class="sales-count">Đã bán: <?php echo $book['sales_count']; ?></span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>    
              <?php endwhile; ?>
            </div>
          </div>
        </section>
      </div>
    </div>
                    <!-- Phần HTML của xu hướng -->
    <div class="new-book">
      <div class="container_1 step_1">
        <div class="tieu_de">
          <span id="tieu_de_xu_huong_1">Xu hướng thường ngày </span>
          <span id="tieu_de_xu_huong_2" style="color: rgb(141, 17, 17)">Nhân quan sinh</span>
          <span id="tieu_de_xu_huong_3" style="color: rgb(141, 17, 17)">Bestseller Ngoại văn</span>
          <hr />
        </div>
        <div class="carousel-wrapper">
          <!-- List 1: Xu hướng thường ngày -->
          <div class="container_2 step_2" id="list_1">
            <?php while($daily_book = mysqli_fetch_assoc($trending_daily_result)) : ?>
            <div class="book-item">
              <a href="trang_chi_tiet.php?id=<?php echo $daily_book['id']; ?>">
                <img src="<?php echo htmlspecialchars($daily_book['image_url']); ?>" alt="<?php echo htmlspecialchars($daily_book['title']); ?>" />
                <div class="book-info" style="margin-top:5px;">
                  <p><?php echo htmlspecialchars($daily_book['title']); ?></p>
                  <div class="price-info">
                    <span class="discounted-price"><?php echo number_format($daily_book['discounted_price']); ?>đ</span>
                    <span class="discount">-<?php echo $daily_book['discount_percent']; ?>%</span><br />
                    <del><?php echo number_format($daily_book['price']); ?>đ</del>
                  </div>
                  <div class="chua_ban" style="height:15px">
                    <span class="sales-count " >Đã bán: <?php echo $daily_book['sales_count']; ?></span>
                  </div>
                </div>
              </a>
            </div>
            <?php endwhile; ?>
          </div>

          <!-- List 2: Nhân quan sinh -->
          <div class="container_2 step_2" id="list_2">
            <?php while($life_book = mysqli_fetch_assoc($trending_life_result)) : ?>
            <div class="book-item">
              <a href="trang_chi_tiet.php?id=<?php echo $life_book['id']; ?>">
                <img src="<?php echo htmlspecialchars($life_book['image_url']); ?>" alt="<?php echo htmlspecialchars($life_book['title']); ?>" />
                <div class="book-info">
                  <p><?php echo htmlspecialchars($life_book['title']); ?></p>
                  <div class="price-info">
                    <span class="discounted-price"><?php echo number_format($life_book['discounted_price']); ?>đ</span>
                    <span class="discount">-<?php echo $life_book['discount_percent']; ?>%</span><br />
                    <del><?php echo number_format($life_book['price']); ?>đ</del>
                  </div>
                  <div class="sales-info">
                    <div class="chua_ban">
                      <span class="sales-count">Đã bán: <?php echo $life_book['sales_count']; ?></span>
                    </div>
                  </div>
                </div>
              </a>
            </div>
            <?php endwhile; ?>
          </div>

          <!-- List 3: Bestseller Ngoại văn -->
          <div class="container_2 step_2" id="list_3">
            <?php while($foreign_book = mysqli_fetch_assoc($trending_foreign_result)) : ?>
            <div class="book-item">
              <a href="trang_chi_tiet.php?id=<?php echo $foreign_book['id']; ?>">
                <img src="<?php echo htmlspecialchars($foreign_book['image_url']); ?>" alt="<?php echo htmlspecialchars($foreign_book['title']); ?>" />
                <div class="book-info">
                  <p><?php echo htmlspecialchars($foreign_book['title']); ?></p>
                  <div class="price-info">
                    <span class="discounted-price"><?php echo number_format($foreign_book['discounted_price']); ?>đ</span>
                    <span class="discount">-<?php echo $foreign_book['discount_percent']; ?>%</span><br />
                    <del><?php echo number_format($foreign_book['price']); ?>đ</del>
                  </div>
                  <div class="sales-info">
                    <div class="chua_ban">
                      <span class="sales-count">Đã bán: <?php echo $foreign_book['sales_count']; ?></span>
                    </div>
                  </div>
                </div>
              </a>
            </div>
            <?php endwhile; ?>
          </div>
        </div>
        <button class="them">Xem thêm</button>
      </div>
    </div>

    
    <div class="tu_sach">
      <div class="line_tu_sach">
        <span class="text_line_tu_sach">
          <i class="fas fa-book" style="color: rgb(240, 35, 35); margin-right: 10px"></i>
            TỦ SÁCH BÁN CHẠY
        </span>
      </div>
      <button class="arrows left-arrows">
        <i class="fa-solid fa-chevron-left"></i>
      </button>
  
                <!-- Phần Tủ sách bán chạy -->
      <div class="bestseller_container">
        <?php while($bestseller = mysqli_fetch_assoc($bestseller_result)) : ?>
        <div class="book-item_bestseller">
          <a href="trang_chi_tiet.php?id=<?php echo $bestseller['id']; ?>">
            <img src="<?php echo htmlspecialchars($bestseller['image_url']); ?>" 
                alt="<?php echo htmlspecialchars($bestseller['title']); ?>" />
            <div class="book-info">
              <p style="margin-left: 25px"><?php echo htmlspecialchars($bestseller['title']); ?></p>
            </div>
          </a>
        </div>
        <?php endwhile; ?>
      </div>   
      <button class="arrows right-arrows">
        <i class="fa-solid fa-chevron-right"></i>
      </button>
    </div>
    <div class="ebook">
      <div class="text_ebook">
        <span>eBook</span>
      </div>  
      <hr />
      <div class="line_ebook">
        <button class="arrows left_arrows">
          <i class="fa-solid fa-chevron-left"></i>
        </button>
        <div class="ebook-slider">
          <div class="ebook-item">
            <a href="https://nhasachmienphi.com/doc-online/dam-uoc-mo-325880" target="_blank">
              <img src="./images/nhasachmienphi-dam-uoc-mo.jpg" alt="Book 1" />
              <div class="ebook-info" style="height: 40px">
                <p style="margin-top: 10px">DÁM ƯỚC MƠ, DÁM THAY ĐỔI, DÁM THÀNH CÔNG!</p>
              </div>
              <hr class="hr-ebook" />
              <p class="ebook-derect">
                "Dám Ước Mơ" là nguồn cảm hứng mạnh mẽ, khuyến khích người đọc tin vào khả năng biến ước mơ thành hiện thực thông qua niềm tin, nỗ lực và sự quyết tâm không ngừng.
              </p>
            </a>
          </div>
          <div class="ebook-item">
            <a href="https://nhasachmienphi.com/doc-online/doi-ngan-dung-ngu-dai-325959" target="_blank">
              <img src="./images/nhasachmienphi-doi-ngan-dung-ngu-dai.jpg" alt="Book 1" />
              <div class="ebook-info" style="height: 40px">
                <p style="margin-top: 10px">ĐỜI NGẮN LẮM, ĐỪNG ĐỂ GIẤC MƠ NGỦ QUÊN!</p>
              </div>
              <hr class="hr-ebook" />
              <p class="ebook-derect">
                "Đời Ngắn Đừng Ngủ Dài" thúc giục bạn tỉnh giấc và làm chủ cuộc đời, khơi dậy đam mê và khát vọng để tạo dựng một cuộc sống đầy ý nghĩa.
              </p>
            </a>
          </div>
          <div class="ebook-item">
            <a
              href="https://file.nhasachmienphi.com/pdf/nhasachmienphi-hanh-phuc-that-gian-don.pdf"
              target="_blank"
            >
              <img
                src="./images/nhasachmienphi-hanh-phuc-that-gian-don.jpg"
                alt="Book 1"
              />

              <div class="ebook-info">
                <p>
                  HẠNH PHÚC KHÔNG Ở ĐÂU XA, NÓ CHÍNH LÀ NHỮNG ĐIỀU GIẢN ĐƠN
                  NHẤT.
                </p>
              </div>
              <hr class="hr-ebook" />
              <p class="ebook-derect">
                Hạnh phúc không phải là những điều to tát, mà là sự trân trọng
                những khoảnh khắc giản dị trong cuộc sống hàng ngày.
              </p>
            </a>
          </div>
          <div class="ebook-item">
            <a
              href="https://nhasachmienphi.com/readfile-online?id=167447&type=pdf"
              target="_blank"
            >
              <img
                src="./images/nhasachmienphi-php-mysql-javascript-html5-all-in-one-for-dummies.jpg"
                alt="Book 1"
              />

              <div class="ebook-info">
                <p>
                  SỨC MẠNH CỦA WEB, TỪ MÃ NGUỒN ĐẾN GIAO DIỆN, TRONG MỘT CUỐN
                  SÁCH!
                </p>
              </div>
              <hr class="hr-ebook" />
              <p class="ebook-derect">
                Từ người mới bắt đầu đến những lập trình viên giàu kinh nghiệm,
                nắm vững các công nghệ web phổ biến, với các bài học dễ hiểu và
                thực hành ngay lập tức.
              </p>
            </a>
          </div>
          <div class="ebook-item">
            <a
              href="https://file.nhasachmienphi.com/pdf/nhasachmienphi-vi-dai-do-lua-chon.pdf"
              target="_blank"
            >
              <img
                src="./images/nhasachmienphi-vi-dai-do-lua-chon.jpg"
                alt="Book 1"
              />

              <div class="ebook-info">
                <p>VĨ ĐẠI KHÔNG PHẢI LÀ MAY MẮN – ĐÓ LÀ LỰA CHỌN CỦA BẠN!</p>
              </div>
              <hr class="hr-ebook" />
              <p class="ebook-derect">
                Sự vĩ đại của một doanh nghiệp hay cá nhân không phải là ngẫu
                nhiên mà là kết quả của những quyết định sáng suốt, kiên trì và
                có định hướng.
              </p>
            </a>
          </div>

          <div class="ebook-item">
            <a
              href="https://nhasachmienphi.com/doc-online/nghi-lon-de-thanh-cong-321956"
              target="_blank"
            >
              <img
                src="./images/nhasachmienphi-nghi-lon-de-thanh-cong.jpg"
                alt="Book 1"
              />

              <div class="ebook-info">
                <p>
                  THÀNH CÔNG BẮT ĐẦU TỪ TƯ DUY LỚN LAO VÀ QUYẾT TÂM KHÔNG GIỚI
                  HẠN
                </p>
              </div>
              <hr class="hr-ebook" />
              <p class="ebook-derect">
                Những quan điểm và chiến lược táo bạo giúp người đọc nhận ra tầm
                quan trọng của tư duy lớn, táo bạo trong việc đạt được thành
                công trong sự nghiệp và cuộc sống.
              </p>
            </a>
          </div>
          <div class="ebook-item">
            <a
              href="https://file.nhasachmienphi.com/pdf/nhasachmienphi-vo-nguyen-giap-chien-thang-bang-moi-gia.pdf"
              target="_blank"
            >
              <img
                src="./images/nhasachmienphi-vo-nguyen-giap-chien-thang-bang-moi-gia.jpg"
                alt="Book 1"
              />

              <div class="ebook-info">
                <p>VÕ NGUYÊN GIÁP – HUYỀN THOẠI CỦA Ý CHÍ VÀ CHIẾN THẮNG</p>
              </div>
              <hr class="hr-ebook" />
              <p class="ebook-derect">
                Chiến lược, trí tuệ và ý chí kiên cường đã giúp Võ Nguyên Giáp
                đạt được những chiến thắng vang dội, làm thay đổi lịch sử Việt
                Nam.
              </p>
            </a>
          </div>
          <div class="ebook-item">
            <a
              href="https://nhasachmienphi.com/doc-online/tai-sao-mac-dung-307586"
              target="_blank"
            >
              <img
                src="./images/nhasachmienphi-tai-sao-mac-dung.jpg"
                alt="Book 1"
              />

              <div class="ebook-info">
                <p>HIỂU ĐÚNG VỀ MARX ĐỂ NHÌN SÂU VÀO THẾ GIỚI HIỆN ĐẠI.</p>
              </div>
              <hr class="hr-ebook" />
              <p class="ebook-derect">
                Giải thích các tư tưởng của Karl Marx trong bối cảnh hiện đại,
                từ đó hiểu được giá trị và tính ứng dụng của chủ nghĩa Marx
                trong phân tích các vấn đề xã hội và kinh tế hôm nay.
              </p>
            </a>
          </div>
        </div>
        <button class="arrows right_arrows">
          <i class="fa-solid fa-chevron-right"></i>
        </button>
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

    <script src="script.js"></script>
    <!-- Link đến file JavaScript -->
    <?php
// Đóng kết nối
mysqli_close($conn);
?>
  </body>
</html>
