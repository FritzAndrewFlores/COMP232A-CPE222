<?php
    require_once '../includes/functions.php';
    require_once '../includes/db_connection.php';
    if (!isAdmin()) {
        redirect('../index.php');
    }

    $stmt = $pdo->query("SELECT id, username, role FROM users");
    $users = $stmt->fetchAll();
    ?>
        <!DOCTYPE html>
    <html>
    <head>
        <title>Manage Users</title>
        <link rel="stylesheet" href="../css/styles.css">
    </head>
    <body>
    <div class="container">

        <h2>Manage Users</h2>
        <a href="admin_panel.php">Back to Admin Panel</a>
        <h3>Add User</h3>
        <form action="add_user.php" method="post">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required><br><br>

            <label for="role">Role:</label>
            <select id="role" name="role" required>
                <option value="admin">Admin</option>
                <option value="cashier">Cashier</option>
            </select><br><br>

            <input type="submit" value="Add User">
        </form>

        <h3>User List</h3>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Role</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
            <tr>
                <td><?= $user['id'] ?></td>
                <td><?= htmlspecialchars($user['username']) ?></td>
                <td><?= htmlspecialchars($user['role']) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    </div>
    </body>
    </html>