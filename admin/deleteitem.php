<?php
header('Content-Type: text/html; charset=utf-8');
require __DIR__ . '/../includes/checkadmin.php';
require __DIR__ . '/../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) die("CSRF");
    $id = (int)$_POST['id'];
    
    $check = $pdo->prepare("SELECT COUNT(*) FROM rentals WHERE inventory_id = ? AND status = 'active'");
    $check->execute([$id]);
    if ($check->fetchColumn() > 0) {
        die("Нельзя удалить инвентарь, который находится в активной аренде");
    }
    
    $stmt = $pdo->prepare("DELETE FROM inventory WHERE id = ?");
    $stmt->execute([$id]);
}
header("Location: index.php");
exit;