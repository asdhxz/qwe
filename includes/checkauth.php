<?php
header('Content-Type: text/html; charset=utf-8');
session_start();
if (!isset($_SESSION['user_id'])) {
    die("Доступ запрещён. <a href='/login.php'>Войдите</a>");
}