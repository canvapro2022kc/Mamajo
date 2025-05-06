<?php

include 'db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Toggle status
    $query = "UPDATE products SET status = IF(status='active', 'archived', 'active') WHERE id = $id";
    $conn->query($query);
}

header("Location: inventory.php");
exit();
