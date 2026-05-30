<?php
function getCurrentUser() {
    global $conn;
    if (isset($_SESSION['user_id'])) {
        $id = (int)$_SESSION['user_id'];
        $result = $conn->query("SELECT * FROM users WHERE id = $id");
        return $result->fetch_assoc();
    }
    return null;
}

function isAdmin() {
    $user = getCurrentUser();
    return $user && $user['role'] === 'admin';
}
?>