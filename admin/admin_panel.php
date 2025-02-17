<?php
    require_once '../includes/functions.php';
    if (!isAdmin()) {
        redirect('../index.php');
    }
    ?>

    <!DOCTYPE html>
    <html>
    <head>
        <title>Admin Panel</title>
        <link rel="stylesheet" href="../css/styles.css">
    </head>
    <body>
    <div class="container">
        <h2>Admin Panel</h2>
        <ul>
            <li><a href="manage_products.php">Manage Products</a></li>
            <li><a href="manage_users.php">Manage Users</a></li>
            <li><a href="view_sales_report.php">View Sales Report</a></li>
            <li><a href="../logout.php">Logout</a></li>
        </ul>
    </div>
    </body>
    </html>