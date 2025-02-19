<?php
require_once '../includes/functions.php';
require_once '../includes/db_connection.php';

if (!isAdmin()) {
    redirect('../index.php'); 
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'], $_POST['new_role'])) {
    $user_id = (int)$_POST['user_id'];
    $new_role = $_POST['new_role'];


    if ($new_role !== 'admin' && $new_role !== 'cashier') {
    
        $_SESSION['error_message'] = "Invalid role selected.";
        redirect('manage_users.php');
        exit;
    }

    
    $stmt_check = $pdo->prepare("SELECT id FROM users WHERE id = ?");
    $stmt_check->execute([$user_id]);
    if (!$stmt_check->fetch()) {
        $_SESSION['error_message'] = "User not found.";
        redirect('manage_users.php');
        exit;
    }


    
    $stmt = $pdo->prepare("UPDATE users SET role = ? WHERE id = ?");
    $stmt->execute([$new_role, $user_id]);

   
    $_SESSION['success_message'] = "User role updated successfully.";
    redirect('manage_users.php');

} else {
    
    $_SESSION['error_message'] = "Invalid request.";
    redirect('manage_users.php');
}