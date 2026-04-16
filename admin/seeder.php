<?php
header('Content-Type: text/html; charset=utf-8');
$title = "Генератор данных";
require __DIR__ . '/../includes/header.php';
require __DIR__ . '/../includes/checkadmin.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $count = (int)$_POST['count'];
    
    $tariffs = [
        ['Эконом', 150, 1000, 1],
        ['Стандарт', 250, 1800, 1],
        ['Премиум', 400, 3000, 1],
    ];
    foreach ($tariffs as $t) {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM tariffs WHERE name = ?");
        $stmt->execute([$t[0]]);
        if ($stmt->fetchColumn() == 0) {
            $pdo->prepare("INSERT INTO tariffs (name, price_per_hour, price_per_day, is_active) VALUES (?, ?, ?, ?)")->execute($t);
        }
    }
    
    $items = [
        ['Горные лыжи Rossi', 'Профессиональные горные лыжи, отличное состояние', 'ski', 2],
        ['Лыжи Atomic', 'Любительские лыжи для начинающих', 'ski', 1],
        ['Горный велосипед Trek', 'Алюминиевая рама, 21 скорость', 'bike', 3],
        ['Велосипед Giant', 'Городской велосипед, удобный для прогулок', 'bike', 2],
        ['Лыжи Salomon', 'Карвинговые лыжи для опытных', 'ski', 3],
        ['Электровелосипед', 'С электроприводом, запас хода 50 км', 'bike', 3],
        ['Беговые лыжи Fischer', 'Для классического стиля', 'ski', 1],
        ['Велосипед Author', 'Чешский велосипед высокого качества', 'bike', 2],
    ];
    
    $inserted = 0;
    for ($i = 0; $i < $count; $i++) {
        $item = $items[array_rand($items)];
        $stmt = $pdo->prepare("INSERT INTO inventory (title, description, category, tariff_id, status) VALUES (?, ?, ?, ?, 'free')");
        try {
            $stmt->execute([$item[0] . ' ' . ($i+1), $item[1], $item[2], $item[3]]);
            $inserted++;
        } catch (Exception $e) {}
    }
    
    $message = '<div class="alert alert-success">Создано ' . $inserted . ' единиц инвентаря</div>';
}
?>

<div class="card">
    <div class="card-header">
        <h3 class="mb-0">Генератор тестовых данных</h3>
    </div>
    <div class="card-body">
        <?= $message ?>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Количество единиц инвентаря</label>
                <input type="number" name="count" class="form-control" value="10" min="1" max="50">
            </div>
            <div class="alert alert-warning">
                <small>Будет создано указанное количество единиц инвентаря. Тарифы будут добавлены автоматически.</small>
            </div>
            <button type="submit" class="btn btn-success">Создать данные</button>
            <a href="index.php" class="btn btn-secondary">Назад</a>
        </form>
    </div>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>