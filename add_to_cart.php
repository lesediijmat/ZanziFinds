<?php
error_reporting(0);
ini_set('display_errors', 0);

include 'config.php';
session_start();

header('Content-Type: application/json');

$product_id = $_POST['product_id'] ?? null;

if (!$product_id) {
    echo json_encode([
        "status" => "error",
        "message" => "No product ID"
    ]);
    exit;
}

$stmt = $conn->prepare("SELECT title, price FROM listings WHERE id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();

if (!$product) {
    echo json_encode([
        "status" => "error",
        "message" => "Product not found"
    ]);
    exit;
}

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if (!isset($_SESSION['cart'][$product_id])) {

    $_SESSION['cart'][$product_id] = [
        'title' => $product['title'],
        'price' => (float)$product['price'],
        'qty' => 1
    ];

} else {
    $_SESSION['cart'][$product_id]['qty'] += 1;
}

echo json_encode([
    "status" => "success",
    "message" => "Added to cart"
]);
exit;
?>