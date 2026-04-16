<?php
header('Content-Type: text/html; charset=utf-8');
require __DIR__ . '/../includes/checkadmin.php';
require __DIR__ . '/../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rental_id = (int)$_POST['rental_id'];
    $action = $_POST['action'];
    
    if ($action === 'complete') {
        $pdo->beginTransaction();
        try {
            $pdo->prepare("UPDATE rentals SET status = 'completed' WHERE id = ?")->execute([$rental_id]);
            $pdo->prepare("UPDATE inventory SET status = 'free' WHERE id = (SELECT inventory_id FROM rentals WHERE id = ?)")->execute([$rental_id]);
            $pdo->commit();
        } catch (Exception $e) {
            $pdo->rollBack();
        }
    } elseif ($action === 'cancel') {
        $pdo->beginTransaction();
        try {
            $pdo->prepare("UPDATE rentals SET status = 'cancelled' WHERE id = ?")->execute([$rental_id]);
            $pdo->prepare("UPDATE inventory SET status = 'free' WHERE id = (SELECT inventory_id FROM rentals WHERE id = ?)")->execute([$rental_id]);
            $pdo->commit();
        } catch (Exception $e) {
            $pdo->rollBack();
        }
    }
}

header("Location: rentals.php");
exit;