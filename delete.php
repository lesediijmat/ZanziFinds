<?php
include 'config.php';

header('Content-Type: application/json');

if(!isset($_SESSION['user_id'])){
    echo json_encode(["status" => "error", "message" => "Not logged in"]);
    exit;
}

$user_id = $_SESSION['user_id'];
$id = intval($_POST['id']);

$stmt = $conn->prepare("SELECT id FROM sellers WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$seller = $stmt->get_result()->fetch_assoc();

if(!$seller){
    echo json_encode(["status" => "error", "message" => "Not a seller"]);
    exit;
}

$seller_id = $seller['id'];

$stmt = $conn->prepare("
    DELETE FROM listings 
    WHERE id = ? AND seller_id = ?
");

$stmt->bind_param("ii", $id, $seller_id);
$stmt->execute();

echo json_encode([
    "status" => "success",
    "id" => $id
]);
