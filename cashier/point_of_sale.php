<?php
require_once '../includes/functions.php';
require_once '../includes/db_connection.php';

if (!isCashier()) {
    redirect('../index.php');
}

// Initialize the cart if it doesn't exist
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Point of Sale</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
<div class="container">
    <h2>Point of Sale</h2>

    <?php
    if (isset($_SESSION['success_message'])) {
        echo '<p class="success-message">' . htmlspecialchars($_SESSION['success_message']) . '</p>';
        unset($_SESSION['success_message']); // Clear the message after displaying it
    }

    if (isset($_SESSION['error_message'])) {
        echo '<p class="error-message">' . htmlspecialchars($_SESSION['error_message']) . '</p>';
        unset($_SESSION['error_message']); // Clear the message after displaying it
    }
    ?>

    <form action="add_to_cart.php" method="post">
        <label for="product_code">Product Code:</label>
        <input type="text" id="product_code" name="product_code" required>
        <label for="quantity">Quantity:</label>
        <input type="number" id="quantity" name="quantity" required min="1">
        <input type="submit" value="Add to Cart">
    </form>

    <h3>Cart</h3>
    <table>
        <thead>
            <tr>
                <th>Product Code</th>
                <th>Description</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
             <?php
                $grand_total = 0; // Initialize grand total

                if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
                    foreach ($_SESSION['cart'] as $item) { // Corrected foreach loop
                        // Fetch product details from the database (using prepared statement)
                        $stmt = $pdo->prepare("SELECT description, price FROM products WHERE product_code = ?");
                        $stmt->execute([$item['product_code']]);
                        $product = $stmt->fetch();

                        if ($product) { // Check if product was found
                            $total = $product['price'] * $item['quantity'];
                            $grand_total += $total;
                            ?>
                            <tr>
                                <td><?= htmlspecialchars($item['product_code']) ?></td>
                                <td><?= htmlspecialchars($product['description']) ?></td>
                                <td><?= htmlspecialchars($product['price']) ?></td>
                                <td><?= htmlspecialchars($item['quantity']) ?></td>
                                <td><?= htmlspecialchars($total) ?></td>
                            </tr>
                            <?php
                        } else {
                            // Handle the case where the product code is not found in the database
                            echo "<tr><td colspan='5'>Product not found: " . htmlspecialchars($item['product_code']) . "</td></tr>";
                        }
                    }
                } else {
                    echo "<tr><td colspan='5'>Your cart is empty.</td></tr>"; // Display message if cart is empty
                }
            ?>
        </tbody>
        <tfoot>
          <tr>
            <td colspan="4">Grand Total:</td>
            <td><?= htmlspecialchars($grand_total)?></td>
          </tr>
        </tfoot>
    </table>

    <form action="checkout_process.php" method="post">
        <label for="cash_tendered">Cash Tendered:</label>
        <input type="number" step="0.01" name="cash_tendered" id="cash_tendered" required>
        <input type="hidden" name="grand_total" value="<?= htmlspecialchars($grand_total) ?>">
        <input type="submit" value="Checkout">
    </form>

    <a href="../logout.php">Logout</a>
    </div>
</body>
</html>