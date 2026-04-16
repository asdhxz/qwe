<?php
header('Content-Type: text/html; charset=utf-8');
session_start();
require __DIR__ . '/config/db.php';
require __DIR__ . '/includes/functions.php';

$item_id = (int)$_GET['id'];

$stmt = $pdo->prepare("SELECT i.*, t.price_per_hour, t.price_per_day FROM inventory i JOIN tariffs t ON i.tariff_id = t.id WHERE i.id = ? AND i.status = 'free'");
$stmt->execute([$item_id]);
$item = $stmt->fetch();

if (!$item) {
    die("Инвентарь не найден или уже арендован");
}

$title = "Аренда: " . $item['title'];
require __DIR__ . '/includes/header.php';
?>

<h1>Аренда: <?= h($item['title']) ?></h1>

<form method="POST" action="/updatecart.php" class="card p-4">
    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
    <input type="hidden" name="inventory_id" value="<?= $item['id'] ?>">
    
    <div class="mb-3">
        <label class="form-label">Дата и время начала аренды</label>
        <input type="datetime-local" name="start_time" class="form-control" required min="<?= date('Y-m-d\TH:i') ?>">
    </div>
    
    <div class="mb-3">
        <label class="form-label">Дата и время окончания аренды</label>
        <input type="datetime-local" name="end_time" class="form-control" required min="<?= date('Y-m-d\TH:i', strtotime('+1 hour')) ?>">
    </div>
    
    <div class="alert alert-info">
        <strong>Тариф:</strong><br>
        <?= number_format($item['price_per_hour'], 2) ?> руб/час<br>
        <?= number_format($item['price_per_day'], 2) ?> руб/день (24 часа)
    </div>
    
    <button type="submit" class="btn btn-success">Добавить в корзину</button>
    <a href="/" class="btn btn-secondary mt-2">Назад к каталогу</a>
</form>

<?php require __DIR__ . '/includes/footer.php'; ?>