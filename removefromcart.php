<?php
header('Content-Type: text/html; charset=utf-8');
session_start();
require __DIR__ . '/config/db.php';
require __DIR__ . '/includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        die("CSRF атака");
    }
    $product_id = (int)$_POST['product_id'];
    unset($_SESSION['cart'][$product_id]);
}
redirect('/cart.php');