<?php 
    $server = 'localhost:3307';
    $user = 'root';
    $pass = '';
    $db = 'bookstore_db';
    $conn = new mysqli($server,$user,$pass,$db);
    if ($conn->connect_error) {
        die("ket noi that bai!: " . $conn->connect_error);
    }
?>