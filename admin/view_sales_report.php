<?php
require_once '../includes/functions.php';
require_once '../includes/db_connection.php';

if (!isAdmin()) {
    redirect('../index.php');
}

// --- Daily Total ---
$stmt_daily = $pdo->prepare("SELECT SUM(s.quantity * p.price) AS daily_total
                            FROM sales s
                            JOIN products p ON s.product_code = p.product_code
                            WHERE DATE(s.sale_date) = CURDATE()");
$stmt_daily->execute();
$daily_total = $stmt_daily->fetch(PDO::FETCH_ASSOC)['daily_total'];
$daily_total = ($daily_total === null) ? 0 : $daily_total; // Handle case where there are no sales


// --- Monthly Total ---
$stmt_monthly = $pdo->prepare("SELECT SUM(s.quantity * p.price) AS monthly_total
                            FROM sales s
                            JOIN products p ON s.product_code = p.product_code
                            WHERE YEAR(s.sale_date) = YEAR(CURDATE()) AND MONTH(s.sale_date) = MONTH(CURDATE())");
$stmt_monthly->execute();
$monthly_total = $stmt_monthly->fetch(PDO::FETCH_ASSOC)['monthly_total'];
$monthly_total = ($monthly_total === null) ? 0 : $monthly_total; // Handle case where there are no sales

// --- Yearly Total ---
$stmt_yearly = $pdo->prepare("SELECT SUM(s.quantity * p.price) AS yearly_total
                            FROM sales s
                            JOIN products p ON s.product_code = p.product_code
                            WHERE YEAR(s.sale_date) = YEAR(CURDATE())");
$stmt_yearly->execute();
$yearly_total = $stmt_yearly->fetch(PDO::FETCH_ASSOC)['yearly_total'];
$yearly_total = ($yearly_total === null) ? 0 : $yearly_total;

// --- Fetch all sales data (for the table) ---
$stmt = $pdo->prepare("SELECT sales.*, products.description
                        FROM sales
                        JOIN products ON sales.product_code = products.product_code
                        ORDER BY sales.id");
$stmt->execute();
$sales_data = $stmt->fetchAll();

?>
<!DOCTYPE html>
<html>
<head>
    <title>Sales Report</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <div class="container">
        <h2>Sales Report</h2>
        <a href="admin_panel.php">Back to Admin Panel</a>

        <p><strong>Total Earnings for Today:</strong> <?= number_format($daily_total, 2) ?></p>
        <p><strong>Total Earnings for This Month:</strong> <?= number_format($monthly_total, 2) ?></p>
        <p><strong>Total Earnings for This Year:</strong> <?= number_format($yearly_total, 2) ?></p>

        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Product Code</th>
                        <th>Description</th>
                        <th>Quantity</th>
                        <th>Cash Tendered</th>
                        <th>Change</th>
                        <th>Date/Time</th>
                        <th>Cashier</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($sales_data as $sale): ?>
                    <tr>
                        <td><?= $sale['id'] ?></td>
                        <td><?= htmlspecialchars($sale['product_code']) ?></td>
                        <td><?= htmlspecialchars($sale['description']) ?></td>
                        <td><?= $sale['quantity'] ?></td>
                        <td><?= $sale['cash_tendered'] ?></td>
                        <td><?= $sale['change'] ?></td>
                        <td><?= $sale['sale_date'] ?></td>
                        <td><?= htmlspecialchars($sale['cashier_username']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>