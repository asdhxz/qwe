<?php
header('Content-Type: text/html; charset=utf-8');
$title = "Корзина аренды";
require __DIR__ . '/includes/header.php';

$cart_items = [];
$total_price = 0;

if (!empty($_SESSION['cart'])) {
    $ids = array_keys($_SESSION['cart']);
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $stmt = $pdo->prepare("SELECT * FROM inventory WHERE id IN ($placeholders)");
    $stmt->execute($ids);
    $products = $stmt->fetchAll();
    
    foreach ($products as $product) {
        $cart_data = $_SESSION['cart'][$product['id']];
        $cart_items[] = [
            'product' => $product,
            'start_time' => $cart_data['start_time'],
            'end_time' => $cart_data['end_time'],
            'total_price' => $cart_data['total_price']
        ];
        $total_price += $cart_data['total_price'];
    }
}
?>

<h1>Корзина аренды</h1>
<a href="/" class="btn btn-secondary mb-3">Назад к каталогу</a>

<?php if (empty($cart_items)): ?>
    <div class="alert alert-info">Корзина пуста.</div>
<?php else: ?>
    <table class="table table-bordered">
        <thead>
            <tr><th>Инвентарь</th><th>Начало</th><th>Окончание</th><th>Стоимость</th><th></th></tr>
        </thead>
        <tbody>
        <?php foreach ($cart_items as $item): $p = $item['product']; ?>
            <tr>
                <td><?= h($p['title']) ?> <?= getItemStatusBadge($p['status']) ?></td>
                <td><?= $item['start_time'] ?></td>
                <td><?= $item['end_time'] ?></td>
                <td><?= number_format($item['total_price'], 2) ?> руб</td>
                <td>
                    <form method="POST" action="/removefromcart.php" class="d-inline">
                        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                        <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
                        <button type="submit" class="btn btn-sm btn-danger">Удалить</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr><th colspan="3">Итого</th><th><?= number_format($total_price, 2) ?> руб</th><th></th></tr>
        </tfoot>
    </table>
    
    <form method="POST" action="/checkout.php">
        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
        <button type="submit" class="btn btn-success">Оформить аренду</button>
    </form>
<?php endif; ?>

<?php require __DIR__ . '/includes/footer.php'; ?>