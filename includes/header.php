<?php
header('Content-Type: text/html; charset=utf-8');
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/functions.php';

$cart_count = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;
$title = $title ?? 'Аренда спортивного инвентаря';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title><?= h($title) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/style.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg">
    <div class="container-fluid">
        <a href="/" class="navbar-brand text-decoration-none">SportRent</a>
        <div class="ms-auto d-flex gap-2">
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="/cart.php" class="btn btn-outline-primary position-relative">
                    Корзина
                    <?php if ($cart_count > 0): ?>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill cart-badge"><?= $cart_count ?></span>
                    <?php endif; ?>
                </a>
                <a href="/myrentals.php" class="btn btn-outline-info">Мои аренды</a>
                <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                    <a href="/admin/" class="btn btn-danger">Админка</a>
                <?php endif; ?>
                <span class="navbar-text mx-2"><?= h($_SESSION['user_name'] ?? '') ?></span>
                <a href="/logout.php" class="btn btn-dark">Выйти</a>
            <?php else: ?>
                <a href="/login.php" class="btn btn-primary">Войти</a>
                <a href="/register.php" class="btn btn-secondary">Регистрация</a>
            <?php endif; ?>
        </div>
    </div>
</nav>
<main class="container py-4">