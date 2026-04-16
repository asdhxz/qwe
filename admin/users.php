<?php
header('Content-Type: text/html; charset=utf-8');
$title = "Арендаторы";
require __DIR__ . '/../includes/header.php';
require __DIR__ . '/../includes/checkadmin.php';

$users = $pdo->query("SELECT id, full_name, email, phone, role, created_at FROM users ORDER BY id DESC")->fetchAll();
?>

<h1>Арендаторы</h1>
<a href="index.php" class="btn btn-secondary mb-3">Назад</a>

<div class="table-responsive">
    <table class="table">
        <thead>
            <tr><th>ID</th><th>ФИО</th><th>Email</th><th>Телефон</th><th>Роль</th><th>Дата регистрации</th></tr>
        </thead>
        <tbody>
        <?php foreach ($users as $u): ?>
        <tr>
            <td><?= $u['id'] ?></td>
            <td><?= h($u['full_name']) ?></td>
            <td><?= h($u['email']) ?></td>
            <td><?= h($u['phone'] ?: '—') ?></td>
            <td><?= $u['role'] == 'admin' ? 'Администратор' : 'Клиент' ?></td>
            <td><?= $u['created_at'] ?></td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>