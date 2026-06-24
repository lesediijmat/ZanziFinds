<?php
include 'config.php';

if(!isset($_SESSION['cart'])){
    $_SESSION['cart'] = [];
}

$id = $_POST['id'];
$change = intval($_POST['change']);

if(isset($_SESSION['cart'][$id])){

    $_SESSION['cart'][$id]['qty'] += $change;

    if($_SESSION['cart'][$id]['qty'] < 1){
        $_SESSION['cart'][$id]['qty'] = 1;
    }
}

$subtotal = 0;

foreach($_SESSION['cart'] as $item){
    $subtotal += $item['price'] * $item['qty'];
}

$delivery = ($subtotal > 0) ? 20 : 0;

echo json_encode([
    "status" => "success",
    "qty" => $_SESSION['cart'][$id]['qty'],
    "subtotal" => $subtotal,
    "total" => $subtotal + $delivery,
    "delivery" => $delivery
]);