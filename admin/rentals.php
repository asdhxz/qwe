<?php
header('Content-Type: text/html; charset=utf-8');
$title = "Все аренды";
require __DIR__ . '/../includes/header.php';
require __DIR__ . '/../includes/checkadmin.php';

$status = $_GET['status'] ?? '';
$params = [];
$where = '';
if ($status && in_array($status, ['active', 'completed', 'cancelled'])) {
    $where = "WHERE r.status = ?";
    $params[] = $status;
}

$sql = "SELECT r.*, u.email, u.full_name, i.title 
        FROM rentals r 
        JOIN users u ON r.user_id = u.id 
        JOIN inventory i ON r.inventory_id = i.id 
        $where 
        ORDER BY r.id DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$rentals = $stmt->fetchAll();
?>

<h1>Все аренды</h1>
<a href="index.php" class="btn btn-secondary mb-3">Назад</a>

<form method="GET" class="row g-3 mb-4">
    <div class="col-md-3">
        <select name="status" class="form-select" onchange="this.form.submit()">
            <option value="">Все статусы</option>
            <option value="active" <?= $status == 'active' ? 'selected' : '' ?>>Активные</option>
            <option value="completed" <?= $status == 'completed' ? 'selected' : '' ?>>Завершённые</option>
            <option value="cancelled" <?= $status == 'cancelled' ? 'selected' : '' ?>>Отменённые</option>
        </select>
    </div>
    <div class="col-md-3">
        <a href="rentals.php" class="btn btn-secondary">Сбросить</a>
    </div>
</form>

<div class="table-responsive">
    <table class="table">
        <thead>
            <tr><th>ID</th><th>Арендатор</th><th>Инвентарь</th><th>Начало</th><th>Окончание</th><th>Стоимость</th><th>Статус</th><th></th></tr>
        </thead>
        <tbody>
        <?php foreach ($rentals as $r): ?>
        <tr>
            <td><?= $r['id'] ?></td>
            <td><?= h($r['full_name']) ?><br><small class="text-muted"><?= h($r['email']) ?></small></td>
            <td><?= h($r['title']) ?></td>
            <td><?= $r['start_time'] ?></td>
            <td><?= $r['end_time'] ?></td>
            <td><?= number_format($r['total_price'], 2) ?> ₽</td>
            <td><?= getRentalStatusBadge($r['status']) ?></td>
            <td>
                <?php if ($r['status'] == 'active'): ?>
                    <form method="POST" action="updaterental.php" class="d-inline">
                        <input type="hidden" name="rental_id" value="<?= $r['id'] ?>">
                        <input type="hidden" name="action" value="complete">
                        <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Завершить аренду?')">Завершить</button>
                    </form>
                    <form method="POST" action="updaterental.php" class="d-inline">
                        <input type="hidden" name="rental_id" value="<?= $r['id'] ?>">
                        <input type="hidden" name="action" value="cancel">
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Отменить аренду?')">Отменить</button>
                    </form>
                <?php endif; ?>
                <a href="/rental_details.php?id=<?= $r['id'] ?>" class="btn btn-sm btn-info">Детали</a>
            </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>