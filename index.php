<?php
header('Content-Type: text/html; charset=utf-8');
$title = "Аренда спортивного инвентаря";
require __DIR__ . '/includes/header.php';

// Получаем параметры фильтрации
$category = isset($_GET['category']) ? trim($_GET['category']) : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 6;
$offset = ($page - 1) * $limit;

// Строим WHERE условие
$where = "WHERE i.status = 'free'";
$params = [];

if (!empty($category) && in_array($category, ['ski', 'bike'])) {
    $where .= " AND i.category = :category";
    $params[':category'] = $category;
}

// Подсчет общего количества
$countSql = "SELECT COUNT(*) FROM inventory i $where";
$countStmt = $pdo->prepare($countSql);
foreach ($params as $key => $value) {
    $countStmt->bindValue($key, $value);
}
$countStmt->execute();
$total_rows = $countStmt->fetchColumn();
$total_pages = ceil($total_rows / $limit);

// Основной запрос
$sql = "SELECT i.*, t.price_per_hour, t.price_per_day 
        FROM inventory i 
        JOIN tariffs t ON i.tariff_id = t.id 
        $where 
        ORDER BY i.id DESC 
        LIMIT :limit OFFSET :offset";

$stmt = $pdo->prepare($sql);
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$items = $stmt->fetchAll();
?>

<!-- Форма фильтрации -->
<div class="row mb-4">
    <div class="col-md-12">
        <form method="GET" action="/" class="row g-3 align-items-end">
            <div class="col-auto">
                <label class="form-label">Категория</label>
                <select name="category" class="form-select" onchange="this.form.submit()">
                    <option value="">Все категории</option>
                    <option value="ski" <?= $category == 'ski' ? 'selected' : '' ?>>Лыжи</option>
                    <option value="bike" <?= $category == 'bike' ? 'selected' : '' ?>>Велосипеды</option>
                </select>
            </div>
            <div class="col-auto">
                <a href="/" class="btn btn-secondary">Сбросить фильтр</a>
            </div>
        </form>
    </div>
</div>

<!-- Результаты -->
<?php if (empty($items)): ?>
    <div class="alert alert-warning">
        <?php if (!empty($category)): ?>
            В данной категории пока нет свободного инвентаря.
        <?php else: ?>
            Свободного инвентаря пока нет.
        <?php endif; ?>
    </div>
<?php else: ?>
    <div class="row">
        <?php foreach ($items as $item): ?>
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <img src="<?= h($item['image_url'] ?: 'https://via.placeholder.com/300x200?text=No+Image') ?>" class="card-img-top">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title"><?= h($item['title']) ?></h5>
                        <p class="card-text text-muted"><?= h(mb_substr($item['description'] ?? '', 0, 100)) ?>...</p>
                        <p class="product-price mt-auto">
                            от <?= number_format($item['price_per_hour'], 2) ?> руб/час<br>
                            <small><?= number_format($item['price_per_day'], 2) ?> руб/день</small>
                        </p>
                        <?= getItemStatusBadge($item['status']) ?>
                        <a href="/addtocart.php?id=<?= $item['id'] ?>" class="btn btn-primary w-100 mt-2">Арендовать</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<!-- Пагинация -->
<?php if ($total_pages > 1): ?>
    <nav>
        <ul class="pagination justify-content-center">
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                    <a class="page-link" href="?page=<?= $i ?>&category=<?= urlencode($category) ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>
        </ul>
    </nav>
<?php endif; ?>

<?php require __DIR__ . '/includes/footer.php'; ?>