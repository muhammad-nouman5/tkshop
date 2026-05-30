<?php
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'tkshop';

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fixed: Session start only if not already active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>