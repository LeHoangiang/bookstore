<?php
    session_start();
    // if ( isset($_SESSION['username'])){
    //     header("location:trangchu.php");
    // }
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Nhập</title>
    <style>
        /* To ensure full-screen height and center content */
         body {
            height: 100vh;
            background-image: url("../images/Background.png");
            background-repeat: no-repeat;
            background-attachment: fixed;
            background-size: cover;
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        

        .main {
            width: 300px;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #fff;
            text-align: center;
        }

        h2 {
            margin-bottom: 20px;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        button {
            background-color:  #d60000;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-left: 50px;
        }
button:hover{
    background-color: brown;
}



        .message {
            margin-bottom: 15px;
            color: red; 
        }
    </style>
</head>
<body>
    <div class="main">
        <h2>Đăng Nhập</h2>
        <p>
        <?php
        
        if(isset($_SESSION['thongbao'])){
            echo $_SESSION['thongbao'];
            unset($_SESSION['thongbao']);
        }
?>
        </p>
        <form action="login_submit.php" method="POST">
            <table>
                <tr>
                    <td>Username:</td>
                    <td><input type="text" name="username"></td>
                </tr>
                <tr>
                    <td>Password</td>
                    <td><input type="password" name="password"></td>
                </tr>
                <tr><td colspan="2">
                    <button type="submit" name="submit">Đăng nhập</button>
                    
                </td>
            </tr>
            </table>
        </form>
    </div>
</body>
</html>