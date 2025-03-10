<?php
require_once '../includes/functions.php';
require_once '../includes/db_connection.php';

if (!isCashier()) {
    redirect('../index.php');
}

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if (isset($_GET['reset_cart']) && $_GET['reset_cart'] === '1') {
    $_SESSION['cart'] = []; 
    unset($_SESSION['success_message']); 
    unset($_SESSION['error_message']); 
    redirect('point_of_sale.php'); 
    exit; 
}

if (isset($_GET['cancel_item'])) {
    $product_code_to_cancel = trim($_GET['cancel_item']);

    if (isset($_SESSION['cart'][$product_code_to_cancel])) {
        unset($_SESSION['cart'][$product_code_to_cancel]); 
        $_SESSION['success_message'] = "Item removed from cart.";
    } else {
        $_SESSION['error_message'] = "Item not found in cart.";
    }

    redirect('point_of_sale.php'); 
    exit;
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
        unset($_SESSION['success_message']); // Clear the message
    }

    if (isset($_SESSION['error_message'])) {
        echo '<p class="error-message">' . htmlspecialchars($_SESSION['error_message']) . '</p>';
        unset($_SESSION['error_message']); // Clear the message
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
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
             <?php
                $grand_total = 0;

                if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
                    foreach ($_SESSION['cart'] as $item) {
                        $stmt = $pdo->prepare("SELECT description, price FROM products WHERE product_code = ?");
                        $stmt->execute([$item['product_code']]);
                        $product = $stmt->fetch();

                        if ($product) {
                            $total = $product['price'] * $item['quantity'];
                            $grand_total += $total;
                            ?>
                            <tr>
                                <td><?= htmlspecialchars($item['product_code']) ?></td>
                                <td><?= htmlspecialchars($product['description']) ?></td>
                                <td>₱<?= number_format(htmlspecialchars($product['price']), 2) ?></td>
                                <td><?= htmlspecialchars($item['quantity']) ?></td>
                                <td>₱<?= number_format(htmlspecialchars($total), 2) ?></td>
                                <td><a href="?cancel_item=<?= htmlspecialchars($item['product_code']) ?>" class="cancel-item-btn">Cancel</a></td>
                            </tr>
                            <?php
                        } else {
                            echo "<tr><td colspan='6'>Product not found: " . htmlspecialchars($item['product_code']) . "</td></tr>"; //colspan 6
                        }
                    }
                } else {
                    echo "<tr><td colspan='6'>Your cart is empty.</td></tr>";
                }
            ?>
        </tbody>
        <tfoot>
          <tr>
            <td colspan="4">Grand Total:</td>
            <td>₱<?= number_format($grand_total, 2)?></td>
          </tr>
        </tfoot>
    </table>

    <form action="checkout_process.php" method="post">
        <label for="cash_tendered">Cash Tendered:</label>
        <div class="input-group">
            <span class="input-group-text">₱</span>
            <input type="number" step="0.01" name="cash_tendered" id="cash_tendered" required>
        </div>
        <input type="hidden" name="grand_total" value="<?= htmlspecialchars($grand_total) ?>">
        <input type="submit" value="Checkout">
    </form>

     <a href="?reset_cart=1" class = "reset-cart-btn">Reset Cart</a>
    <a href="../logout.php">Logout</a>
    </div>
</body>
</html>