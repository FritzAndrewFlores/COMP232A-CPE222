<?php
require_once '../includes/functions.php';
require_once '../includes/db_connection.php';

if (!isAdmin()) {
    redirect('../index.php');
}

$stmt = $pdo->query("SELECT * FROM products");
$products = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Products</title>
    <link rel="stylesheet" href="../css/styles.css">
    <script>
        function confirmDelete() {
            return confirm("Are you sure you want to delete this product? This action cannot be undone.");
        }
    </script>
</head>
<body>
<div class="container">
    <h2>Manage Products</h2>
    <a href="admin_panel.php">Back to Admin Panel</a>

    <h3>Add Product</h3>
    <form action="add_product.php" method="post">
        <label for="product_code">Product Code:</label>
        <input type="text" id="product_code" name="product_code" required>

        <label for="description">Description:</label>
        <input type="text" id="description" name="description" required>

        <label for="price">Price:</label>
        <div class="input-group">
            <span class="input-group-text">₱</span>
            <input type="number" step="0.01" id="price" name="price" required>
        </div>

        <label for="stock">Stock:</label>
        <input type="number" id="stock" name="stock" required>

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
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $product): ?>
            <tr>
                <td><?= htmlspecialchars($product['product_code']) ?></td>
                <td><?= htmlspecialchars($product['description']) ?></td>
                <td>₱<?= number_format(htmlspecialchars($product['price']), 2) ?></td>
                <td><?= htmlspecialchars($product['stock']) ?></td>
                <td>
                    <form action="update_stock.php" method="post">
                        <input type="hidden" name="product_code" value="<?= htmlspecialchars($product['product_code']) ?>">
                        <input type="number" name="new_stock" value="<?= htmlspecialchars($product['stock']) ?>" required>
                        <button type="submit" class="update-stock-btn">Update Stock</button>
                    </form>
                    <form action="delete_product.php" method="post" onsubmit="return confirmDelete();">
                        <input type="hidden" name="product_code" value="<?= htmlspecialchars($product['product_code']) ?>">
                        <button type="submit" class="delete-product-btn">Delete</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
  </div>
</body>
</html>