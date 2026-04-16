<?php
header('Content-Type: text/html; charset=utf-8');
require __DIR__ . '/includes/checkauth.php';
require __DIR__ . '/config/db.php';
require __DIR__ . '/includes/functions.php';

$rental_id = (int)$_GET['id'];
$user_id = $_SESSION['user_id'];
$role = $_SESSION['user_role'];

$stmt = $pdo->prepare("
    SELECT r.*, i.title, i.image_url, u.email as user_email, u.full_name 
    FROM rentals r 
    JOIN inventory i ON r.inventory_id = i.id 
    JOIN users u ON r.user_id = u.id 
    WHERE r.id = ?
");
$stmt->execute([$rental_id]);
$rental = $stmt->fetch();

if (!$rental) die("Аренда не найдена");
if ($role !== 'admin' && $rental['user_id'] != $user_id) die("Нет доступа");

$title = "Аренда #$rental_id";
require __DIR__ . '/includes/header.php';
?>

<h1>Детали аренды #<?= $rental_id ?></h1>
<a href="<?= $role === 'admin' ? '/admin/rentals.php' : '/myrentals.php' ?>" class="btn btn-secondary mb-3">Назад</a>

<div class="card mb-4">
    <div class="card-body">
        <div class="row">
            <div class="col-md-4">
                <img src="<?= h($rental['image_url'] ?: 'https://via.placeholder.com/300x200?text=No+Image') ?>" class="img-fluid rounded">
            </div>
            <div class="col-md-8">
                <h3><?= h($rental['title']) ?></h3>
                <p><strong>Арендатор:</strong> <?= h($rental['full_name']) ?> (<?= h($rental['user_email']) ?>)</p>
                <p><strong>Начало:</strong> <?= $rental['start_time'] ?></p>
                <p><strong>Окончание:</strong> <?= $rental['end_time'] ?></p>
                <p><strong>Итого:</strong> <?= number_format($rental['total_price'], 2) ?> руб</p>
                <p><strong>Статус:</strong> <?= getRentalStatusBadge($rental['status']) ?></p>
                <p><strong>Создано:</strong> <?= $rental['created_at'] ?></p>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>