<?php
header('Content-Type: text/html; charset=utf-8');
$title = "Управление тарифами";
require __DIR__ . '/../includes/header.php';
require __DIR__ . '/../includes/checkadmin.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_tariff'])) {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) die("CSRF");
    $id = (int)$_POST['id'];
    $name = trim($_POST['name']);
    $hour = (float)$_POST['price_per_hour'];
    $day = (float)$_POST['price_per_day'];
    $active = isset($_POST['is_active']) ? 1 : 0;
    
    if ($id) {
        $stmt = $pdo->prepare("UPDATE tariffs SET name=?, price_per_hour=?, price_per_day=?, is_active=? WHERE id=?");
        $stmt->execute([$name, $hour, $day, $active, $id]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO tariffs (name, price_per_hour, price_per_day, is_active) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $hour, $day, $active]);
    }
    redirect('/admin/tariffs.php');
}

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $check = $pdo->prepare("SELECT COUNT(*) FROM inventory WHERE tariff_id = ?");
    $check->execute([$id]);
    if ($check->fetchColumn() == 0) {
        $pdo->prepare("DELETE FROM tariffs WHERE id = ?")->execute([$id]);
    }
    redirect('/admin/tariffs.php');
}

$tariffs = $pdo->query("SELECT * FROM tariffs ORDER BY id")->fetchAll();
?>

<h1>Тарифы</h1>
<a href="index.php" class="btn btn-secondary mb-3">Назад</a>

<div class="card mb-4">
    <div class="card-header">Добавить тариф</div>
    <div class="card-body">
        <form method="POST" class="row g-3">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
            <input type="hidden" name="id" value="0">
            <div class="col-md-4">
                <input type="text" name="name" placeholder="Название" class="form-control" required>
            </div>
            <div class="col-md-2">
                <input type="number" name="price_per_hour" placeholder="руб/час" class="form-control" step="0.01" required>
            </div>
            <div class="col-md-2">
                <input type="number" name="price_per_day" placeholder="руб/день" class="form-control" step="0.01" required>
            </div>
            <div class="col-md-2">
                <div class="form-check mt-2">
                    <input type="checkbox" name="is_active" class="form-check-input"> Активен
                </div>
            </div>
            <div class="col-md-2">
                <button type="submit" name="save_tariff" class="btn btn-primary w-100">Добавить</button>
            </div>
        </form>
    </div>
</div>

<div class="table-responsive">
    <table class="table">
        <thead>
            <tr><th>ID</th><th>Название</th><th>руб/час</th><th>руб/день</th><th>Статус</th><th></th></tr>
        </thead>
        <tbody>
        <?php foreach ($tariffs as $t): ?>
        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
            <input type="hidden" name="id" value="<?= $t['id'] ?>">
            <tr>
                <td><?= $t['id'] ?></td>
                <td><input type="text" name="name" value="<?= h($t['name']) ?>" class="form-control form-control-sm"></td>
                <td><input type="number" name="price_per_hour" value="<?= $t['price_per_hour'] ?>" class="form-control form-control-sm" step="0.01"></td>
                <td><input type="number" name="price_per_day" value="<?= $t['price_per_day'] ?>" class="form-control form-control-sm" step="0.01"></td>
                <td><input type="checkbox" name="is_active" value="1" <?= $t['is_active'] ? 'checked' : '' ?>></td>
                <td>
                    <button type="submit" name="save_tariff" class="btn btn-sm btn-primary">Сохранить</button>
                    <a href="?delete=<?= $t['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Удалить тариф?')">Удалить</a>
                </td>
            </tr>
        </form>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>