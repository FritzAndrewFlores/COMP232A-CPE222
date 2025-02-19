<?php
require_once '../includes/functions.php';
require_once '../includes/db_connection.php';

if (!isAdmin()) {
    redirect('../index.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_code'])) {
    $product_code = trim($_POST['product_code']);

    $stmt_check = $pdo->prepare("SELECT COUNT(*) FROM sales WHERE product_code = ?");
    $stmt_check->execute([$product_code]);
    $sales_count = $stmt_check->fetchColumn();

    if ($sales_count > 0) {

        $_SESSION['error_message'] = "Cannot delete product '$product_code' because it has been used in sales.";
        redirect('manage_products.php');
        exit;
    }

    $pdo->beginTransaction();

    try {

        $stmt = $pdo->prepare("DELETE FROM products WHERE product_code = ?");
        $stmt->execute([$product_code]);

    
        $pdo->commit();

        $_SESSION['success_message'] = "Product deleted successfully.";
        redirect('manage_products.php');

    } catch (PDOException $e) {
       
        $pdo->rollBack();
        $_SESSION['error_message'] = "Error deleting product: " . $e->getMessage();
        redirect('manage_products.php');
    }

} else {
    $_SESSION['error_message'] = "Invalid request.";
    redirect('manage_products.php');
}