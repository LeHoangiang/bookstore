<?php 
session_start();
include 'connect.php';

if (isset($_POST['btn'])) {
    $name = $_POST['name'];
    $img = $_FILES['img']['name'];
    $img_tmp_name = $_FILES['img']['tmp_name'];
    $price = $_POST['price'];
    $discount_percent = $_POST['discount_percent'];
    $description = $_POST['description'];
    $category_name = $_POST['category_name'];
    $publisher = $_POST['publisher'];
    $publish_year = $_POST['publish_year'];
    $pages = $_POST['pages'];

    // Thêm sách vào cơ sở dữ liệu
    $sql = "INSERT INTO books (title, image_url, price, discount_percent, description, category_name, publisher, publish_year, pages) 
            VALUES ('$name', '$img', '$price', '$discount_percent', '$description', '$category_name', '$publisher', '$publish_year', '$pages')";
    
    if (mysqli_query($conn, $sql)) {
        move_uploaded_file($img_tmp_name, 'img/' . $img);
        header('Location: detail.php'); // Chuyển hướng về trang danh sách sách
        exit();
    } else {
        echo "Lỗi thêm sản phẩm: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="edit.css">
    <title>Thêm sản phẩm</title>
</head>
<body>
<h1>Thêm sản phẩm mới</h1>
<form action="add_product.php" method="POST" enctype="multipart/form-data" >
    <table>
        <tr>
            <td>Tên sách:</td>
            <td><input type="text" name="name" required></td>
        </tr>
        <tr>
            <td>Hình ảnh:</td>
            <td><input type="file" name="img" required></td>
        </tr>
        <tr>
            <td>Giá:</td>
            <td><input type="number" name="price" required></td>
        </tr>
        <tr>
            <td>Giảm giá (%):</td>
            <td><input type="number" name="discount_percent" required></td>
        </tr>
        <tr>
            <td>Mô tả:</td>
            <td><textarea name="description" required></textarea></td>
        </tr>
        <tr>
            <td>Loại:</td>
            <td><input type="text" name="category_name" required></td>
        </tr>
        <tr>
            <td>Nhà xuất bản:</td>
            <td><input type="text" name="publisher" required></td>
        </tr>
        <tr>
            <td>Năm xuất bản:</td>
            <td><input type="number" name="publish_year" required></td>
        </tr>
        <tr>
            <td>Số trang:</td>
            <td><input type="number" name="pages" required></td>
        </tr>
       
    </table>
    <div class="button-container">
    <button type="submit" name="btn" >Thêm</button>
    <a href="detail.php">Quay lại danh sách sách</a>
    </div>
</form>

</body>
</html>