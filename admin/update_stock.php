<?php
require_once '../includes/functions.php';
require_once '../includes/db_connection.php';

if (!isAdmin()) {
    redirect('../index.php'); 
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_code'], $_POST['new_stock'])) {
    $product_code = trim($_POST['product_code']);
    $new_stock = (int)$_POST['new_stock']; 

    
    if ($new_stock < 0) {
       
        $_SESSION['error_message'] = "Stock level cannot be negative.";
        redirect('manage_products.php');
        exit;
    }

      
    $stmt_check = $pdo->prepare("SELECT product_code FROM products WHERE product_code = ?");
    $stmt_check->execute([$product_code]);
    if (!$stmt_check->fetch()) {
       $_SESSION['error_message'] = "Product not found.";
        redirect('manage_products.php');
        exit;
    }


   
    $stmt = $pdo->prepare("UPDATE products SET stock = ? WHERE product_code = ?");
    $stmt->execute([$new_stock, $product_code]);

   
    $_SESSION['success_message'] = "Stock updated successfully.";
    redirect('manage_products.php');

} else {
   
    $_SESSION['error_message'] = "Invalid request.";
    redirect('manage_products.php');
}