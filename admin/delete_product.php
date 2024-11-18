<?php
    session_start();
    include 'connect.php';
// Kiểm tra xem có ID không
if (isset($_POST['id'])) {
    $id = intval($_POST['id']); // Chuyển đổi ID thành số nguyên để bảo mật
     $sql = "DELETE  FROM books WHERE id = '$id'";
     if (mysqli_query($conn, $sql)) {
        echo " Đã xóa thành công sản phẩm có id = $id";
        // Chuyển hướng về trang detail.php sau khi xóa thành công
        header("Location: detail.php");
        exit();
    }else {
        echo "Lỗi xóa sản phẩm: " . mysqli_error($conn);
    }
} else {
    echo "ID không hợp lệ.";
}

// Đóng kết nối
mysqli_close($conn);
?>