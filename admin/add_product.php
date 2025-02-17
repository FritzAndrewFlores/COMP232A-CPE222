<?php
require_once '../includes/functions.php';
require_once '../includes/db_connection.php';

if (!isAdmin()) {
redirect('../index.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_code = $_POST['product_code'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];

    try {
        $stmt = $pdo->prepare("INSERT INTO products (product_code, description, price, stock) VALUES (?, ?, ?, ?)");
        $stmt->execute([$product_code, $description, $price, $stock]);
        redirect('manage_products.php'); // Redirect after successful addition
    } catch (PDOException $e) {
       echo "Error: " . $e->getMessage();
    }
}

?>