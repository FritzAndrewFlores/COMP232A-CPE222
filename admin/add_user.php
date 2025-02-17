<?php
require_once '../includes/functions.php';
require_once '../includes/db_connection.php';

if (!isAdmin()) {
    redirect('../index.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    // No password handling
    $role = $_POST['role'];

    try {
        $stmt = $pdo->prepare("INSERT INTO users (username, role) VALUES (?, ?)"); // No password
        $stmt->execute([$username, $role]);
        redirect('manage_users.php');

    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>