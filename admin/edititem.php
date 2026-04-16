<?php
header('Content-Type: text/html; charset=utf-8');
require __DIR__ . '/../includes/checkadmin.php';
require __DIR__ . '/../config/db.php';

$id = (int)$_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM inventory WHERE id = ?");
$stmt->execute([$id]);
$item = $stmt->fetch();
if (!$item) die("Инвентарь не найден");

$tariffs = $pdo->query("SELECT id, name FROM tariffs WHERE is_active = 1")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) die("CSRF");
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $category = $_POST['category'];
    $tariff_id = (int)$_POST['tariff_id'];
    
    $upd = $pdo->prepare("UPDATE inventory SET title=?, description=?, category=?, tariff_id=? WHERE id=?");
    $upd->execute([$title, $description, $category, $tariff_id, $id]);
    header("Location: index.php");
    exit;
}

$title = "Редактировать инвентарь";
require __DIR__ . '/../includes/header.php';
?>

<h1>Редактирование инвентаря</h1>
<a href="index.php" class="btn btn-secondary mb-3">Назад</a>

<form method="POST" class="card p-4">
    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
    
    <div class="mb-3">
        <label class="form-label">Название</label>
        <input type="text" name="title" class="form-control" value="<?= h($item['title']) ?>" required>
    </div>
    
    <div class="mb-3">
        <label class="form-label">Категория</label>
        <select name="category" class="form-select" required>
            <option value="ski" <?= $item['category'] == 'ski' ? 'selected' : '' ?>>Лыжи</option>
            <option value="bike" <?= $item['category'] == 'bike' ? 'selected' : '' ?>>Велосипед</option>
        </select>
    </div>
    
    <div class="mb-3">
        <label class="form-label">Тариф</label>
        <select name="tariff_id" class="form-select" required>
            <?php foreach ($tariffs as $t): ?>
                <option value="<?= $t['id'] ?>" <?= $t['id'] == $item['tariff_id'] ? 'selected' : '' ?>><?= h($t['name']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    
    <div class="mb-3">
        <label class="form-label">Описание</label>
        <textarea name="description" class="form-control" rows="4"><?= h($item['description']) ?></textarea>
    </div>
    
    <button type="submit" class="btn btn-primary">Сохранить изменения</button>
</form>

<?php require __DIR__ . '/../includes/footer.php'; ?>