<?php
session_start();
header('Content-Type: application/json');

$id = $_POST['id'] ?? null;
$change = intval($_POST['change']);

if (!$id || !isset($_SESSION['cart'][$id])) {
    echo json_encode(["status" => "error"]);
    exit;
}

$_SESSION['cart'][$id]['qty'] += $change;

if ($_SESSION['cart'][$id]['qty'] < 1) {
    $_SESSION['cart'][$id]['qty'] = 1;
}

$subtotal = 0;

foreach ($_SESSION['cart'] as $item) {
    $subtotal += $item['price'] * $item['qty'];
}

$delivery = ($subtotal > 0) ? 20 : 0;

echo json_encode([
    "status" => "success",
    "id" => $id,
    "qty" => $_SESSION['cart'][$id]['qty'],
    "subtotal" => $subtotal,
    "total" => $subtotal + $delivery,
    "delivery" => $delivery
]);
