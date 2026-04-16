<?php
session_start();
header('Content-Type: text/html; charset=utf-8');

// Данные для подключения к базе данных (InfinityFree)
$host = 'sql313.infinityfree.com';
$db   = 'if0_41386943_db';
$user = 'if0_41386943';
$pass = '1253827Km';
$charset = 'utf8mb4';

// DSN - строка подключения
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

// Настройки PDO
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,        // Выбрасывать исключения при ошибках
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,   // Режим выборки - ассоциативный массив
    PDO::ATTR_EMULATE_PREPARES => false,                // Отключаем эмуляцию подготовленных запросов
];

// Подключение к базе данных
try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    
    // Принудительная установка кодировки UTF-8
    $pdo->exec("SET NAMES utf8mb4");
    $pdo->exec("SET CHARACTER SET utf8mb4");
    $pdo->exec("SET character_set_connection=utf8mb4");
    $pdo->exec("SET character_set_client=utf8mb4");
    $pdo->exec("SET character_set_results=utf8mb4");
    
} catch (PDOException $e) {
    // Если ошибка подключения - показываем сообщение
    die("Ошибка подключения к базе данных: " . $e->getMessage());
}

/**
 * Генерация CSRF-токена для защиты форм
 * @return string
 */
function csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Проверка CSRF-токена
 * @param string $token
 * @return bool
 */
function verify_csrf($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}
