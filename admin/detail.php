<?php 
session_start();
include 'connect.php';

// Truy vấn tất cả sách từ bảng books
$sql = "SELECT * FROM books";
$result = mysqli_query($conn, $sql);

if (!$result) {
    die("Lỗi truy vấn: " . mysqli_error($conn));
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="detail.css" />
    <title>Product Details</title>
    <script>
        function confirmDelete() {
            return confirm("Bạn có chắc chắn muốn xóa sách này không?");
        }
    </script>
</head>
<body>
    <form action="" method="POST" enctype='multipart/form-data'>
        <h1>Danh sách sách trong hệ thống</h1>
        <a href="add_product.php" class="addBooks">Thêm sách</a>
        <button class="logout">Đăng xuất</button>
        <!-- Main container -->
         <div class="main-container">
            <?php while ($row = mysqli_fetch_assoc($result)) : ?>
              <div class="book-info">
                <div class="book-image">
                  <img src="<?php echo htmlspecialchars($row['image_url']); ?>" alt="<?php echo htmlspecialchars($row['title']); ?>" />
                </div>
                <div class="book-details">
                  <h2 class="book-title"><?php echo $row['title']; ?></h2>
                  <p>Tác giả: <?php echo $row['author']; ?></p>
                  <p>Giá: <?php echo number_format($row['price']); ?>đ</p>
                  <p>Giảm giá: <?php echo $row['discount_percent']; ?>%</p>
                  <p>Mô tả:<?php echo $row['description']; ?></p>
                  <p>Loại:<?php echo $row['category_name']; ?></p>
                  <p>Nhà xuất bản:<?php echo $row['publisher']; ?></p>
                  <p>Năm xuất bản:<?php echo $row['publish_year']; ?></p>
                  <p>Số trang: <?php echo $row['pages']; ?></p>
                  <a href="edit_product.php?id=<?php echo $row['id']; ?>" class="fix-button">Sửa</a>
                  <form action="delete_product.php" method="POST" style="display:inline;">
                      <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                      <button type="submit" class="delete-button" onclick="return confirmDelete();">Xoá</button>
                  </form>
                  <!-- <button class="fix-button">Sửa</button> -->
                  <!-- <button class="delete-button">Xoá</button> -->
                  </div>
              </div>
              <?php endwhile; ?>
        </div>
    </form>
</body>
</html>  
<?php
// Đóng kết nối
mysqli_close($conn);
?>