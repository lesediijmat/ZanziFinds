<?php
session_start();
header('Content-Type: application/json');

if (!isset($_POST['index'])) {
    echo json_encode(["status" => "error", "message" => "No index"]);
    exit;
}

$index = $_POST['index'];

if (isset($_SESSION['cart'][$index])) {
    unset($_SESSION['cart'][$index]);
    $_SESSION['cart'] = array_values($_SESSION['cart']);
}

$subtotal = 0;

foreach ($_SESSION['cart'] as $item) {
    $subtotal += $item['price'];
}

$empty = empty($_SESSION['cart']);

$delivery = $empty ? 0 : 20;
$total = $subtotal + $delivery;

echo json_encode([
    "status" => "success",
    "subtotal" => $subtotal,
    "total" => $total,
    "empty" => $empty
]);
?>