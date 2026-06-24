<?php
session_start();
header('Content-Type: application/json');

$id = $_POST['index'] ?? null;

if (!$id || !isset($_SESSION['cart'][$id])) {
    echo json_encode(["status" => "error"]);
    exit;
}

unset($_SESSION['cart'][$id]);

$subtotal = 0;

foreach ($_SESSION['cart'] as $item) {
    $subtotal += $item['price'] * $item['qty'];
}

$empty = empty($_SESSION['cart']);
$delivery = $empty ? 0 : 20;

echo json_encode([
    "status" => "success",
    "id" => $id,
    "subtotal" => $subtotal,
    "total" => $subtotal + $delivery,
    "empty" => $empty
]);
