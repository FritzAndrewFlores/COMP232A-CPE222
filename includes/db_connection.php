<?php
$host = 'localhost';
$db   = 'pos_system';  // MAKE SURE THIS IS YOUR DATABASE NAME
$user = 'root';       // Default XAMPP username
$pass = '';          // Default XAMPP password (empty)
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    // DEBUGGING:  You could *temporarily* add this to test the connection (remove after)
    // echo "Database connection successful!";
} catch (\PDOException $e) {
    //  It's good practice to *not* display the full error message in a production environment.
    //  Log it instead.  But for debugging, it's helpful:
    echo "Database Connection Error: " . $e->getMessage(); // REMOVE THIS IN PRODUCTION
    exit; // Stop execution if there's a connection error.
}

?>