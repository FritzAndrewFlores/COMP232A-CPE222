<?php
require_once '../includes/functions.php';
require_once '../includes/db_connection.php';
if (!isAdmin()) {
    redirect('../index.php');
}

// Fetch products for display
$stmt = $pdo->query("SELECT * FROM products");
$products = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Products</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
<div class="container">
    <h2>Manage Products</h2>
    <a href="admin_panel.php">Back to Admin Panel</a>

    <h3>Add Product</h3>
    <form action="add_product.php" method="post">
        <label for="product_code">Product Code:</label>
        <input type="text" id="product_code" name="product_code" required><br><br>

        <label for="description">Description:</label>
        <input type="text" id="description" name="description" required><br><br>

        <label for="price">Price:</label>
        <input type="number" step="0.01" id="price" name="price" required><br><br>

        <label for="stock">Stock:</label>
        <input type="number" id="stock" name="stock" required><br><br>

        <input type="submit" value="Add Product">
    </form>


    <h3>Product List</h3>
    <table>
        <thead>
            <tr>
                <th>Product Code</th>
                <th>Description</th>
                <th>Price</th>
                <th>Stock</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $product): ?>
            <tr>
                <td><?= htmlspecialchars($product['product_code']) ?></td>
                <td><?= htmlspecialchars($product['description']) ?></td>
                <td><?= htmlspecialchars($product['price']) ?></td>
                <td><?= htmlspecialchars($product['stock']) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>