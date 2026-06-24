<?php
include 'config.php';

$search = $_POST['query'] ?? '';

$search = "%".$search."%";

$stmt = $conn->prepare("
    SELECT listings.*, users.fullname 
    FROM listings
    LEFT JOIN users ON users.id = listings.user_id
    WHERE listings.title LIKE ?
    OR users.fullname LIKE ?
");

$stmt->bind_param("ss", $search, $search);
$stmt->execute();

$result = $stmt->get_result();

$data = [];

while($row = $result->fetch_assoc()){
    $data[] = $row;
}

echo json_encode($data);
?>