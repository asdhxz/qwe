<?php
header('Content-Type: text/html; charset=utf-8');
$title = "Добавить инвентарь";
require __DIR__ . '/../includes/header.php';
require __DIR__ . '/../includes/checkadmin.php';

$tariffs = $pdo->query("SELECT id, name FROM tariffs WHERE is_active = 1")->fetchAll();
$uploadDir = __DIR__ . '/../uploads/items/';
if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) die("CSRF");
    
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $category = $_POST['category'];
    $tariff_id = (int)$_POST['tariff_id'];
    $imagePath = '';
    
    if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
        $allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/jpg'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $_FILES['image_file']['tmp_name']);
        finfo_close($finfo);
        
        if (in_array($mime, $allowed)) {
            $ext = pathinfo($_FILES['image_file']['name'], PATHINFO_EXTENSION);
            $filename = uniqid() . '.' . $ext;
            if (move_uploaded_file($_FILES['image_file']['tmp_name'], $uploadDir . $filename)) {
                $imagePath = '/uploads/items/' . $filename;
            }
        }
    }
    
    if (empty($title)) {
        $message = '<div class="alert alert-danger">Заполните название</div>';
    } else {
        $stmt = $pdo->prepare("INSERT INTO inventory (title, description, category, tariff_id, image_url, status) VALUES (?, ?, ?, ?, ?, 'free')");
        $stmt->execute([$title, $description, $category, $tariff_id, $imagePath]);
        $message = '<div class="alert alert-success">Инвентарь успешно добавлен</div>';
    }
}
?>

<h1>Добавление инвентаря</h1>
<a href="index.php" class="btn btn-secondary mb-3">Назад</a>
<?= $message ?>

<form method="POST" enctype="multipart/form-data" class="card p-4">
    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
    
    <div class="mb-3">
        <label class="form-label">Название</label>
        <input type="text" name="title" class="form-control" required>
    </div>
    
    <div class="mb-3">
        <label class="form-label">Категория</label>
        <select name="category" class="form-select" required>
            <option value="ski">Лыжи</option>
            <option value="bike">Велосипед</option>
        </select>
    </div>
    
    <div class="mb-3">
        <label class="form-label">Тариф</label>
        <select name="tariff_id" class="form-select" required>
            <?php foreach ($tariffs as $t): ?>
                <option value="<?= $t['id'] ?>"><?= h($t['name']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    
    <div class="mb-3">
        <label class="form-label">Изображение</label>
        <input type="file" name="image_file" class="form-control" accept="image/jpeg,image/png,image/webp">
    </div>
    
    <div class="mb-3">
        <label class="form-label">Описание</label>
        <textarea name="description" class="form-control" rows="4"></textarea>
    </div>
    
    <button type="submit" class="btn btn-primary">Сохранить</button>
</form>

<?php require __DIR__ . '/../includes/footer.php'; ?>