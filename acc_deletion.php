<?php
include 'config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$password = $_POST['password'] ?? '';

$stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!password_verify($password, $user['password'])) {
    echo "<script>alert('Incorrect password'); window.history.back();</script>";
    exit;
}

$delete = $conn->prepare("DELETE FROM users WHERE id = ?");
$delete->bind_param("i", $user_id);
$delete->execute();

session_destroy();

header("Location: index.php?deleted=1");
exit;
?>