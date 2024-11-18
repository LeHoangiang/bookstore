<?php
    session_start();
    include_once "connect.php";
    if (isset($_POST['submit']) && $_POST['username'] != '' && $_POST['password'] != '') {
        $username = $_POST['username'];
        $password = $_POST['password'];
    
        
        $sql = "SELECT * FROM users WHERE username = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);;
        if (mysqli_num_rows($result) > 0) {
            $user_data = mysqli_fetch_assoc($result);
            
            // So sánh mật khẩu người dùng nhập với mật khẩu đã mã hóa trong cơ sở dữ liệu
            if (password_verify($password, $user_data['password'])) {
                // Đăng nhập thành công
                $_SESSION['username'] = $username;
                $_SESSION['user_id'] = $user_data['id'];
                
                // Kiểm tra vai trò người dùng
                if ($user_data['role'] === 'admin') {
                    header('location: detail.php'); // Chuyển hướng đến trang admin
                }
                // } else {
                //     header('location: product.php'); // Chuyển hướng đến trang sản phẩm
                // }
            } else {
                $_SESSION['thongbao'] = "Sai tên đăng nhập hoặc mật khẩu";
                echo "Mật khẩu không khớp."; // Thông báo lỗi
                echo "Mật khẩu đã mã hóa: " . $user_data['password']; // In ra mật khẩu đã mã hóa
            }
        } else {
            $_SESSION['thongbao'] = "Sai tên đăng nhập hoặc mật khẩu";
            echo "Không tìm thấy người dùng."; // Thông báo lỗi
        }
    } else {
        $_SESSION['thongbao'] = "Vui lòng nhập đầy đủ thông tin";
        echo "Thông tin không đầy đủ."; // Thông báo lỗi
    }
?>


