<?php
session_start();

require_once '../includes/db_connection.php';
require_once '../includes/functions.php';

if (!isCashier()) {
    redirect('../index.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_code'], $_POST['quantity'])) {

    $product_code = trim($_POST['product_code']);
    $quantity = (int)$_POST['quantity'];

    if ($quantity <= 0) {
        $_SESSION['error_message'] = "Invalid quantity. Please enter a quantity greater than 0.";
        redirect('point_of_sale.php');
        exit; // Important: Exit after redirect
    }

    $stmt = $pdo->prepare("SELECT product_code, description, price, stock FROM products WHERE product_code = ?"); // Select necessary fields
    $stmt->execute([$product_code]);
    $product = $stmt->fetch();

    if ($product) {
        if ($product['stock'] >= $quantity) {
            // Check if the product already exists in the cart
            if (isset($_SESSION['cart'][$product_code])) {
                // Update quantity if it exists
                $_SESSION['cart'][$product_code]['quantity'] += $quantity;
            } else {
                // Add new item to cart.  Key is product_code, value is an array.
                $_SESSION['cart'][$product_code] = [
                    'product_code' => $product['product_code'], // Use fetched data
                    'description'  => $product['description'],  // Store description
                    'price'        => $product['price'],      // Store price
                    'quantity'     => $quantity,
                ];
            }

            $_SESSION['success_message'] = "Product added to cart!";
            redirect('point_of_sale.php');
            exit; // Important: Exit after redirect

        } else {
            $_SESSION['error_message'] = "Not enough stock available.";
            redirect('point_of_sale.php');
            exit; // Important: Exit after redirect
        }
    } else {
        $_SESSION['error_message'] = "Product not found.";
        redirect('point_of_sale.php');
        exit; // Important: Exit after redirect
    }
} else {
    $_SESSION['error_message'] = "Invalid request.";
    redirect('point_of_sale.php');
    exit; // Important: Exit after redirect
}
?>