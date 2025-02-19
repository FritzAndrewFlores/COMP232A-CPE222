<?php
require_once '../includes/functions.php';
require_once '../includes/db_connection.php';

if (!isAdmin()) {
    redirect('../index.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_code = trim($_POST['product_code']);
    $description = trim($_POST['description']);
    $price = (float)$_POST['price']; 
    $cost = (float)$_POST['cost'];   
    $stock = (int)$_POST['stock'];   

    if (empty($product_code) || empty($description) || $price <= 0 || $stock < 0) {
        echo "Error: Invalid input."; 
        exit;
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO products (product_code, description, price, cost, stock) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$product_code, $description, $price, $cost, $stock]);
        redirect('manage_products.php'); 
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        exit;
    }
} else {
    redirect('manage_products.php');
}
?>