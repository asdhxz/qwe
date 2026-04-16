<?php
header('Content-Type: text/html; charset=utf-8');

function h($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

function redirect($url) {
    header("Location: $url");
    exit;
}

function is_admin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

function calculateRentalPrice($start, $end, $price_per_hour, $price_per_day) {
    $start_dt = new DateTime($start);
    $end_dt = new DateTime($end);
    $interval = $start_dt->diff($end_dt);
    
    $total_hours = $interval->days * 24 + $interval->h;
    $total_days = $interval->days;
    
    if ($total_hours <= 24 && $total_days == 0) {
        return ceil($total_hours) * $price_per_hour;
    } else {
        return ($total_days + ($interval->h > 0 ? 1 : 0)) * $price_per_day;
    }
}

function getItemStatusBadge($status) {
    if ($status == 'free') {
        return '<span class="badge bg-success">Свободен</span>';
    } else {
        return '<span class="badge bg-danger">Занят</span>';
    }
}

function getRentalStatusBadge($status) {
    switch($status) {
        case 'active': return '<span class="badge bg-warning">Активна</span>';
        case 'completed': return '<span class="badge bg-success">Завершена</span>';
        case 'cancelled': return '<span class="badge bg-danger">Отменена</span>';
        default: return '<span class="badge bg-secondary">Неизвестно</span>';
    }
}