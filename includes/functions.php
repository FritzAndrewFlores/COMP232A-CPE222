<?php
session_start(); // MUST be the very first thing

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