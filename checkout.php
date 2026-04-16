<?php
header('Content-Type: text/html; charset=utf-8');
session_start();
require __DIR__ . '/config/db.php';
require __DIR__ . '/includes/checkauth.php';
require __DIR__ . '/includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('/');
if (!verify_csrf($_POST['csrf_token'] ?? '')) die("CSRF");

$user_id = $_SESSION['user_id'];

if (empty($_SESSION['cart'])) {
    redirect('/cart.php');
}

$pdo->beginTransaction();
$error = null;

try {
    foreach ($_SESSION['cart'] as $inventory_id => $rental) {
        $stmt = $pdo->prepare("SELECT status FROM inventory WHERE id = ? FOR UPDATE");
        $stmt->execute([$inventory_id]);
        $item = $stmt->fetch();
        
        if (!$item || $item['status'] !== 'free') {
            throw new Exception("Инвентарь #$inventory_id уже арендован");
        }
        
        $stmt = $pdo->prepare("INSERT INTO rentals (user_id, inventory_id, start_time, end_time, total_price, status) VALUES (?, ?, ?, ?, ?, 'active')");
        $stmt->execute([$user_id, $inventory_id, $rental['start_time'], $rental['end_time'], $rental['total_price']]);
        
        $stmt = $pdo->prepare("UPDATE inventory SET status = 'rented' WHERE id = ?");
        $stmt->execute([$inventory_id]);
    }
    
    $pdo->commit();
    $_SESSION['cart'] = [];
    $success = true;
} catch (Exception $e) {
    $pdo->rollBack();
    $error = $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Оформление аренды</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/style.css" rel="stylesheet">
</head>
<body class="p-4">
<div class="container">
    <?php if (isset($success)): ?>
        <div class="alert alert-success">
            <h1>Аренда успешно оформлена</h1>
            <p>Вы можете отслеживать свои аренды в <a href="/myrentals.php">личном кабинете</a>.</p>
        </div>
    <?php elseif ($error): ?>
        <div class="alert alert-danger">
            <h1>Ошибка при оформлении</h1>
            <p><?= h($error) ?></p>
        </div>
    <?php endif; ?>
    <a href="/" class="btn btn-primary">На главную</a>
</div>
</body>
</html>