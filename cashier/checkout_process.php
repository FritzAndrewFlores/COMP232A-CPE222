<?php
session_start();

require_once '../includes/db_connection.php';
require_once '../includes/functions.php';

// Check if the user is a cashier
if (!isCashier()) {
    redirect('../index.php');
}

// Check if the request method is POST and if the necessary data is present
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cash_tendered'], $_POST['grand_total'])) {

    $cash_tendered = (float)$_POST['cash_tendered']; // Cast to float
    $grand_total = (float)$_POST['grand_total'];    // Cast to float

    // Validate input (basic checks)
    if ($cash_tendered < $grand_total) {
        $_SESSION['error_message'] = "Insufficient cash tendered.";
        redirect('point_of_sale.php');
    }

    if ($grand_total <= 0) {
        $_SESSION['error_message'] = "Grand total must be greater than zero.";
        redirect('point_of_sale.php');
    }
    // Begin transaction
    $pdo->beginTransaction();

    try {
        // Calculate the change
        $change = $cash_tendered - $grand_total;

        // Get the cashier's username from the session
        $cashier_username = $_SESSION['username'];

        // 1. Record the sale in the 'sales' table
        foreach ($_SESSION['cart'] as $item) {
            $product_code = $item['product_code'];
            $quantity = $item['quantity'];

             // Fetch the price *again* from the database to prevent manipulation
            $stmt = $pdo->prepare("SELECT price, stock FROM products WHERE product_code = ?");
            $stmt->execute([$product_code]);
            $product = $stmt->fetch();

            // Double-check that the product exists and has enough stock
            // (extra safety check)
            if (!$product || $product['stock'] < $quantity) {
                throw new Exception("Product not found or insufficient stock: $product_code");
            }

            $stmt = $pdo->prepare("INSERT INTO sales (product_code, quantity, cash_tendered, `change`, cashier_username) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$product_code, $quantity, $cash_tendered, $change, $cashier_username]);

            // 2. Update the product stock in the 'products' table
            $stmt = $pdo->prepare("UPDATE products SET stock = stock - ? WHERE product_code = ?");
            $stmt->execute([$quantity, $product_code]);
        }


        // Commit the transaction (save all changes)
        $pdo->commit();

        // Clear the cart
        $_SESSION['cart'] = [];

        // Set a success message
        $_SESSION['success_message'] = "Checkout successful! Change: " . number_format($change, 2);

        // Redirect to the point of sale page
        redirect('point_of_sale.php');

    } catch (Exception $e) {
        // Rollback the transaction if any error occurred
        $pdo->rollBack();

        // Set an error message
        $_SESSION['error_message'] = "Checkout failed: " . $e->getMessage();

        // Redirect back to the point of sale page
        redirect('point_of_sale.php');
    }

} else {
    // Invalid request
    $_SESSION['error_message'] = "Invalid request.";
    redirect('point_of_sale.php');
}
?>