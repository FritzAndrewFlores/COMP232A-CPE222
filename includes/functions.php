<?php
session_start();

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isLoggedIn() && $_SESSION['role'] === 'admin';
}

function isCashier() {
    return isLoggedIn() && $_SESSION['role'] === 'cashier';
}

function redirect($location) {
    header("Location: $location");
    exit;
}
?>