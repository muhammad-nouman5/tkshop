<?php
session_start();
$count = 0;
if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $qty) {
        $count += (int)$qty;
    }
}
header('Content-Type: application/json');
echo json_encode(['count' => $count]);
?>