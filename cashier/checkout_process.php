<?php
session_start();

require_once '../includes/db_connection.php';
require_once '../includes/functions.php';

if (!isCashier()) {
    redirect('../index.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cash_tendered'], $_POST['grand_total'])) {

    $cash_tendered = (float)$_POST['cash_tendered'];
    $grand_total = (float)$_POST['grand_total']; 

    if ($cash_tendered < $grand_total) {
        $_SESSION['error_message'] = "Insufficient cash tendered.";
        redirect('point_of_sale.php');
    }

    if ($grand_total <= 0) {
        $_SESSION['error_message'] = "Grand total must be greater than zero.";
        redirect('point_of_sale.php');
    }
    $pdo->beginTransaction();

    try {
        
        $change = $cash_tendered - $grand_total;

        $cashier_username = $_SESSION['username'];

        foreach ($_SESSION['cart'] as $item) {
            $product_code = $item['product_code'];
            $quantity = $item['quantity'];

            
            $stmt = $pdo->prepare("SELECT price, stock FROM products WHERE product_code = ?");
            $stmt->execute([$product_code]);
            $product = $stmt->fetch();

            
            if (!$product || $product['stock'] < $quantity) {
                throw new Exception("Product not found or insufficient stock: $product_code");
            }

            $stmt = $pdo->prepare("INSERT INTO sales (product_code, quantity, cash_tendered, `change`, cashier_username) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$product_code, $quantity, $cash_tendered, $change, $cashier_username]);

            
            $stmt = $pdo->prepare("UPDATE products SET stock = stock - ? WHERE product_code = ?");
            $stmt->execute([$quantity, $product_code]);
        }

        $pdo->commit();

        $_SESSION['cart'] = [];

        $_SESSION['success_message'] = "Checkout successful! Change: " . number_format($change, 2);

        redirect('point_of_sale.php');

    } catch (Exception $e) {
        
        $pdo->rollBack();

        $_SESSION['error_message'] = "Checkout failed: " . $e->getMessage();

        redirect('point_of_sale.php');
    }

} else {
   
    $_SESSION['error_message'] = "Invalid request.";
    redirect('point_of_sale.php');
}
?>