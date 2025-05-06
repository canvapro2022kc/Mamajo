<?php
include 'db_connect.php';

if (!isset($_GET['id']) || !isset($_GET['action'])) {
    header("Location: orders.php");
    exit();
}

$id = intval($_GET['id']);
$action = $_GET['action'];
$archivedParam = isset($_GET['archived']) ? intval($_GET['archived']) : 0;

if ($action === 'archive') {
    $stmt = $conn->prepare("UPDATE orders SET archived = 1 WHERE id = ?");
} elseif ($action === 'unarchive') {
    $stmt = $conn->prepare("UPDATE orders SET archived = 0 WHERE id = ?");
} else {
    header("Location: orders.php");
    exit();
}

$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->close();

header("Location: orders.php?archived=$archivedParam");
exit();
