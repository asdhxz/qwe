<?php
header('Content-Type: text/html; charset=utf-8');
$title = "Мои аренды";
require __DIR__ . '/includes/header.php';
require __DIR__ . '/includes/checkauth.php';

$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("
    SELECT r.*, i.title, i.image_url 
    FROM rentals r 
    JOIN inventory i ON r.inventory_id = i.id 
    WHERE r.user_id = ? 
    ORDER BY r.created_at DESC
");
$stmt->execute([$user_id]);
$rentals = $stmt->fetchAll();
?>

<h1>Мои аренды</h1>
<a href="/" class="btn btn-secondary mb-3">На главную</a>

<?php if (empty($rentals)): ?>
    <div class="alert alert-info">У вас пока нет активных или завершённых аренд.</div>
<?php else: ?>
    <div class="row">
        <?php foreach ($rentals as $rental): ?>
            <div class="col-md-4 mb-3">
                <div class="card h-100">
                    <img src="<?= h($rental['image_url'] ?: 'https://via.placeholder.com/300x200?text=No+Image') ?>" class="card-img-top" style="height:150px;object-fit:cover">
                    <div class="card-body">
                        <h5><?= h($rental['title']) ?></h5>
                        <p><strong>С:</strong> <?= $rental['start_time'] ?><br>
                           <strong>По:</strong> <?= $rental['end_time'] ?><br>
                           <strong>Стоимость:</strong> <?= number_format($rental['total_price'], 2) ?> руб<br>
                           <strong>Статус:</strong> <?= getRentalStatusBadge($rental['status']) ?>
                        </p>
                        <a href="/rental_details.php?id=<?= $rental['id'] ?>" class="btn btn-sm btn-info">Подробнее</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php require __DIR__ . '/includes/footer.php'; ?>