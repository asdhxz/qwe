<?php
header('Content-Type: text/html; charset=utf-8');
$title = "Админ-панель";
require __DIR__ . '/../includes/header.php';
require __DIR__ . '/../includes/checkadmin.php';

$total_items = $pdo->query("SELECT COUNT(*) FROM inventory")->fetchColumn();
$free_items = $pdo->query("SELECT COUNT(*) FROM inventory WHERE status = 'free'")->fetchColumn();
$active_rentals = $pdo->query("SELECT COUNT(*) FROM rentals WHERE status = 'active'")->fetchColumn();
$total_revenue = $pdo->query("SELECT SUM(total_price) FROM rentals WHERE status = 'completed'")->fetchColumn();
$total_users = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'client'")->fetchColumn();
?>

<div class="mb-4">
    <h1>Панель управления</h1>
    <p class="text-muted">Добро пожаловать, администратор</p>
</div>

<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="card text-center p-3">
            <h3><?= $total_items ?></h3>
            <p class="text-muted mb-0">Всего инвентаря</p>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card text-center p-3">
            <h3><?= $free_items ?></h3>
            <p class="text-muted mb-0">Свободно</p>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card text-center p-3">
            <h3><?= $active_rentals ?></h3>
            <p class="text-muted mb-0">Активных аренд</p>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card text-center p-3">
            <h3><?= number_format($total_revenue ?: 0, 0) ?> ₽</h3>
            <p class="text-muted mb-0">Выручка</p>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card p-4">
            <h3>Управление</h3>
            <hr>
            <div class="d-flex flex-wrap gap-2">
                <a href="additem.php" class="btn btn-primary">Добавить инвентарь</a>
                <a href="rentals.php" class="btn btn-info">Все аренды</a>
                <a href="users.php" class="btn btn-success">Арендаторы</a>
                <a href="tariffs.php" class="btn btn-secondary">Тарифы</a>
                <a href="seeder.php" class="btn btn-warning">Генератор данных</a>
                <a href="/logout.php" class="btn btn-danger">Выйти</a>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>