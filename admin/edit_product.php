<?php
    session_start();
    include 'connect.php';

    // Kiểm tra xem có ID không
    if (!isset($_GET['id'])) {
        die("ID không hợp lệ.");
    }

    $id = intval($_GET['id']); // Chuyển đổi ID thành số nguyên để bảo mật
    $sql = "SELECT * FROM books WHERE id = '$id'";
    $query = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($query);

    // Khi nhấn nút cập nhật
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

        // Cập nhật thông tin sách
        $sql = "UPDATE books SET title='$name', image_url='$img', price='$price', discount_percent='$discount_percent', description='$description', category_name='$category_name', publisher='$publisher', publish_year='$publish_year', pages='$pages' WHERE id = '$id'";
        
        if (mysqli_query($conn, $sql)) {
            move_uploaded_file($img_tmp_name, 'img/' . $img);
            header("Location: detail.php"); // Chuyển hướng về trang danh sách sách
            exit();
        } else {
            echo "Lỗi cập nhật sản phẩm: " . mysqli_error($conn);
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="edit.css">
    <title>Chỉnh sửa sản phẩm</title>
</head>
<body>
<h1>Chỉnh sửa thông tin sản phẩm: <?php echo $row['title']; ?></h1>

<form method='POST' enctype='multipart/form-data'>
    <table>
        <tr>
            <td>Tên sách</td>
            <td><input type="text" name="name" value="<?php echo $row['title']; ?>"></td>
        </tr>
        <tr>
            <td>Hình ảnh</td>
            <td><input type="file" name="img"></td>
            <td><img src="img/<?php echo $row['image_url']; ?>" alt="Hình ảnh" style="width: 100px; height: auto;"></td>
           
        </tr>
        <tr>
            <td>Giá</td>
            <td><input type="text" name="price" value="<?php echo $row['price']; ?>"></td>
        </tr>
        <tr>
            <td>Giảm giá (%)</td>
            <td><input type="text" name="discount_percent" value="<?php echo $row['discount_percent']; ?>"></td>
        </tr>
        <tr>
            <td>Mô tả</td>
            <td><textarea name="description"><?php echo $row['description']; ?></textarea></td>
        </tr>
        <tr>
            <td>Loại</td>
            <td><input type="text" name="category_name" value="<?php echo $row['category_name']; ?>"></td>
        </tr>
        <tr>
            <td>Nhà xuất bản</td>
            <td><input type="text" name="publisher" value="<?php echo $row['publisher']; ?>"></td>
        </tr>
        <tr>
            <td>Năm xuất bản</td>
            <td><input type="text" name="publish_year" value="<?php echo $row['publish_year']; ?>"></td>
        </tr>
        <tr>
            <td>Số trang</td>
            <td><input type="text" name="pages" value="<?php echo $row['pages']; ?>"></td>
        </tr>
    </table>
    <div class="button-container">
        <button type="submit" name="btn">Cập nhật</button>
        <a href="detail.php">Quay lại</a>
    </div>
</form>
</body>
</html>