<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$host = 'sql302.infinityfree.com';
$db   = 'if0_42167435_zanzifinds';
$user = 'if0_42167435';
$pass = 'THQCRWzjAMFFe';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>