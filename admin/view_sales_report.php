<?php
require_once '../includes/functions.php';
require_once '../includes/db_connection.php';

if (!isAdmin()) {
    redirect('../index.php');
}

// --- Date Handling ---
$report_date = $_GET['report_date'] ?? date('Y-m-d'); 
$report_type = $_GET['report_type'] ?? 'day'; 


if (!strtotime($report_date)) {
    $report_date = date('Y-m-d'); 
}


$total_earnings = 0; 

if ($report_type === 'day') {
    $stmt_total = $pdo->prepare("SELECT SUM(s.quantity * (p.price - p.cost)) AS total_earnings
                                FROM sales s
                                JOIN products p ON s.product_code = p.product_code
                                WHERE DATE(s.sale_date) = ?");
    $stmt_total->execute([$report_date]);

} elseif ($report_type === 'month') {
   
    $report_year = date('Y', strtotime($report_date));
    $report_month = date('m', strtotime($report_date));

    $stmt_total = $pdo->prepare("SELECT SUM(s.quantity * (p.price - p.cost)) AS total_earnings
                                FROM sales s
                                JOIN products p ON s.product_code = p.product_code
                                WHERE YEAR(s.sale_date) = ? AND MONTH(s.sale_date) = ?");
    $stmt_total->execute([$report_year, $report_month]);

} elseif ($report_type === 'year') {
     $report_year = date('Y', strtotime($report_date));
    $stmt_total = $pdo->prepare("SELECT SUM(s.quantity * (p.price - p.cost)) AS total_earnings
                                FROM sales s
                                JOIN products p ON s.product_code = p.product_code
                                WHERE YEAR(s.sale_date) = ?");
    $stmt_total->execute([$report_year]);
}

$total_earnings = $stmt_total->fetch(PDO::FETCH_ASSOC)['total_earnings'];
$total_earnings = ($total_earnings === null) ? 0 : $total_earnings; 



$stmt = $pdo->prepare("SELECT sales.*, products.description, products.price
                        FROM sales
                        JOIN products ON sales.product_code = products.product_code
                        WHERE " .
                        ($report_type === 'day' ? "DATE(sales.sale_date) = ?" :
                            ($report_type === 'month' ? "YEAR(sales.sale_date) = YEAR(?) AND MONTH(sales.sale_date) = MONTH(?)" :
                                "YEAR(sales.sale_date) = YEAR(?)")) .
                        " ORDER BY sales.id");

if ($report_type === 'day') {
$stmt->execute([$report_date]);
}
elseif ($report_type === 'month'){
    $stmt->execute([$report_date, $report_date]);
}
elseif ($report_type === 'year'){
    $stmt->execute([$report_date]);
}

$sales_data = $stmt->fetchAll();

?>
<!DOCTYPE html>
<html>
<head>
    <title>Sales Report</title>
    <link rel="stylesheet" href="../css/styles.css">
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const reportTypeSelect = document.getElementById('report_type');
            const reportDateInput = document.getElementById('report_date');

            function updateInputType() {
                switch (reportTypeSelect.value) {
                    case 'day':
                        reportDateInput.type = 'date';
                        break;
                    case 'month':
                        reportDateInput.type = 'month';
                        break;
                    case 'year':
                        reportDateInput.type = 'number';
                        reportDateInput.min = '1900';
                        reportDateInput.max = '2100';
                        break;
                }
            }

            
            updateInputType();

           
            reportTypeSelect.addEventListener('change', updateInputType);
        });
    </script>
</head>
<body>
    <div class="container">
        <h2>Sales Report</h2>
        <a href="admin_panel.php">Back to Admin Panel</a>

        <form method="get" action="">
            <label for="report_type">Report Type:</label>
            <select id="report_type" name="report_type">
                <option value="day" <?= $report_type === 'day' ? 'selected' : '' ?>>Day</option>
                <option value="month" <?= $report_type === 'month' ? 'selected' : '' ?>>Month</option>
                <option value="year" <?= $report_type === 'year' ? 'selected' : '' ?>>Year</option>
            </select>

            <label for="report_date">Select Date:</label>
            <input type="date" id="report_date" name="report_date" value="<?= htmlspecialchars($report_date) ?>">


            <input type="submit" value="Generate Report">
        </form>


        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Product Code</th>
                        <th>Description</th>
                        <th>Quantity</th>
                        <th>Price</th>
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
                        <td>₱<?= number_format(htmlspecialchars($sale['price']), 2) ?></td>
                        <td>₱<?= number_format($sale['cash_tendered'], 2) ?></td>
                        <td>₱<?= number_format($sale['change'], 2) ?></td>
                        <td><?= $sale['sale_date'] ?></td>
                        <td><?= htmlspecialchars($sale['cashier_username']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4"><strong>Total Earnings (<?= ucfirst(htmlspecialchars($report_type)) ?>):</strong></td>
                        <td colspan="5"><strong>₱<?= number_format($total_earnings, 2) ?></strong></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</body>
</html>