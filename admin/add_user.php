<?php
require_once '../includes/functions.php';
require_once '../includes/db_connection.php';

if (!isAdmin()) {
    redirect('../index.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']); // New password field
    $role = trim($_POST['role']);

    if (empty($username) || empty($password) || empty($role)) {
        $_SESSION['error_message'] = "All fields are required!";
        redirect('manage_users.php');
        exit;
    }

    $hashed_password = $password; // Store password as plain text

    try {
        $stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
        $stmt->execute([$username, $hashed_password, $role]);

        $_SESSION['success_message'] = "User added successfully!";
        redirect('manage_users.php');

    } catch (PDOException $e) {
        $_SESSION['error_message'] = "Error adding user!";
        redirect('manage_users.php');
    }
}
?>
