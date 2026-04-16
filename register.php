<?php
header('Content-Type: text/html; charset=utf-8');
require __DIR__ . '/config/db.php';
$error = $success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $pass = $_POST['password'];
    $confirm = $_POST['password_confirm'];
    
    if (empty($full_name) || empty($email) || empty($pass)) $error = "Заполните все поля";
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $error = "Некорректный email";
    elseif ($pass !== $confirm) $error = "Пароли не совпадают";
    else {
        $hash = password_hash($pass, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (full_name, email, phone, password_hash, role) VALUES (?, ?, ?, ?, 'client')");
        try {
            $stmt->execute([$full_name, $email, $phone, $hash]);
            $success = "Регистрация успешна! <a href='/login.php'>Войти</a>";
        } catch (PDOException $e) {
            $error = $e->getCode() == 23000 ? "Email уже занят" : "Ошибка БД";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Регистрация</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/style.css" rel="stylesheet">
</head>
<body class="bg-dark">
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-primary text-white">Регистрация</div>
                <div class="card-body">
                    <?php if($error):?>
                        <div class="alert alert-danger"><?=$error?></div>
                    <?php endif;?>
                    <?php if($success):?>
                        <div class="alert alert-success"><?=$success?></div>
                    <?php else:?>
                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label">ФИО</label>
                                <input type="text" name="full_name" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Телефон</label>
                                <input type="tel" name="phone" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Пароль</label>
                                <input type="password" name="password" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Подтверждение</label>
                                <input type="password" name="password_confirm" class="form-control" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Зарегистрироваться</button>
                        </form>
                        <div class="mt-3 text-center">
                            <a href="/login.php">Уже есть аккаунт? Войти</a>
                        </div>
                    <?php endif;?>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>