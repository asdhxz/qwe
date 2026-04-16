<?php
header('Content-Type: text/html; charset=utf-8');
session_start();
require __DIR__ . '/config/db.php';
require __DIR__ . '/includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) die("CSRF");
    
    $inventory_id = (int)$_POST['inventory_id'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    
    if (strtotime($start_time) >= strtotime($end_time)) {
        die("Дата окончания должна быть позже начала");
    }
    
    $stmt = $pdo->prepare("SELECT i.*, t.price_per_hour, t.price_per_day FROM inventory i JOIN tariffs t ON i.tariff_id = t.id WHERE i.id = ?");
    $stmt->execute([$inventory_id]);
    $item = $stmt->fetch();
    
    if (!$item) die("Товар не найден");
    
    $total_price = calculateRentalPrice($start_time, $end_time, $item['price_per_hour'], $item['price_per_day']);
    
    if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
    
    $_SESSION['cart'][$inventory_id] = [
        'start_time' => $start_time,
        'end_time' => $end_time,
        'total_price' => $total_price
    ];
}

redirect('/cart.php');