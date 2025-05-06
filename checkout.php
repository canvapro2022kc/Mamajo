<?php

include 'db_connect.php';

if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header("Location: sales.php");
    exit();
}

// Insert into orders table
$total = $_POST['total'];
$conn->query("INSERT INTO orders (total) VALUES ($total)");
$order_id = $conn->insert_id;

// Insert each cart item
foreach ($_SESSION['cart'] as $pid => $qty) {
    $product = $conn->query("SELECT * FROM products WHERE id = $pid")->fetch_assoc();
    $price = $product['price'];
    $conn->query("INSERT INTO order_items (order_id, product_id, quantity, price) 
                  VALUES ($order_id, $pid, $qty, $price)");

    // Update stock
    $conn->query("UPDATE products SET stock = stock - $qty WHERE id = $pid");
}

// Clear cart
unset($_SESSION['cart']);

// Redirect to receipt
header("Location: receipt.php?id=$order_id");
exit();
