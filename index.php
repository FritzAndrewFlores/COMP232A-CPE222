<?php
require_once 'includes/functions.php';
require_once 'includes/db_connection.php';

$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    // No password handling here

    $stmt = $pdo->prepare("SELECT id, username, role FROM users WHERE username = ?"); // No password in query
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user) { // Just check if the user exists
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        if ($user['role'] === 'admin') {
            redirect('admin/admin_panel.php');
        } else {
            redirect('cashier/point_of_sale.php');
        }
    } else {
        $error_message = "Invalid username."; // Simpler error message
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="container">
        <h2>Login</h2>
        <?php if ($error_message): ?>
            <p class="error-message"><?= $error_message ?></p>
        <?php endif; ?>
        <form method="post">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required autocomplete="off">

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" autocomplete="off">

            <input type="submit" value="Login">
        </form>
    </div>
</body>
</html>