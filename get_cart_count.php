<?php
session_start();
require_once 'config.php';

try {
    $stmt = $pdo->prepare("SELECT SUM(quantity) as count FROM cart WHERE session_id = ?");
    $stmt->execute([$_SESSION['cart_id']]);
    $result = $stmt->fetch();
    
    echo json_encode(['count' => $result['count'] ?? 0]);
} catch (PDOException $e) {
    echo json_encode(['count' => 0]);
}
?>