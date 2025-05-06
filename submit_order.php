<?php
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);
date_default_timezone_set('Asia/Manila');
header('Content-Type: application/json');
require 'db_connect.php';

// Log error helper
function log_error($message) {
    file_put_contents('php_error_log.txt', "[" . date('Y-m-d H:i:s') . "] " . $message . PHP_EOL, FILE_APPEND);
}

// Decode JSON input
$data = json_decode(file_get_contents('php://input'), true);

if (json_last_error() !== JSON_ERROR_NONE) {
    log_error("Invalid JSON: " . json_last_error_msg());
    echo json_encode(['success' => false, 'message' => 'Invalid JSON received']);
    exit;
}

// Validate required fields
if (
    !isset($data['customerName']) ||
    !isset($data['customerContact']) ||
    !isset($data['items']) ||
    !is_array($data['items'])
) {
    log_error("Missing required fields or invalid item format: " . json_encode($data));
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
    exit;
}

$customerName = $data['customerName'];
$customerContact = $data['customerContact'];
$amountGiven = $data['amountGiven'];
$createdAt = date('Y-m-d H:i:s');

// Calculate total
$total = 0;
foreach ($data['items'] as $item) {
    $total += $item['price'] * $item['quantity'];
}

// Step 1: Insert order
$order_stmt = $conn->prepare("INSERT INTO orders (customer_name, customer_contact, total_amount, amount_given, created_at) VALUES (?, ?, ?, ?, ?)");
if (!$order_stmt) {
    log_error("Prepare failed for orders insert: " . $conn->error);
    echo json_encode(['success' => false, 'message' => 'Server error.']);
    exit;
}

$order_stmt->bind_param("ssdss", $customerName, $customerContact, $total, $amountGiven, $createdAt);
if (!$order_stmt->execute()) {
    log_error("Execute failed for orders insert: " . $order_stmt->error);
    echo json_encode(['success' => false, 'message' => 'Failed to create order.']);
    exit;
}

$order_id = $conn->insert_id;
if (!$order_id) {
    log_error("Insert ID not returned after order insert.");
    echo json_encode(['success' => false, 'message' => 'Failed to retrieve order ID']);
    exit;
}

// Step 2: Insert order items
$item_stmt = $conn->prepare("INSERT INTO order_items (order_id, product_name, quantity, price) VALUES (?, ?, ?, ?)");
if (!$item_stmt) {
    log_error("Prepare failed for order_items insert: " . $conn->error);
    echo json_encode(['success' => false, 'message' => 'Server error.']);
    exit;
}

// Prepare stock update statement
$stock_stmt = $conn->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
if (!$stock_stmt) {
    log_error("Prepare failed for stock update: " . $conn->error);
    echo json_encode(['success' => false, 'message' => 'Server error (stock update)']);
    exit;
}

foreach ($data['items'] as $item) {
    $product_name = $item['title'];
    $quantity = $item['quantity'];
    $price = $item['price'];
    $product_id = $item['product_id'];  // Ensure this exists in your JSON

    // Insert order item
    $item_stmt->bind_param("isid", $order_id, $product_name, $quantity, $price);
    if (!$item_stmt->execute()) {
        log_error("Failed to insert item: $product_name | Error: " . $item_stmt->error);
        echo json_encode(['success' => false, 'message' => "Failed to insert item: $product_name"]);
        exit;
    }

    // Deduct stock
    $stock_stmt->bind_param("ii", $quantity, $product_id);
    if (!$stock_stmt->execute()) {
        log_error("Failed to update stock for product_id $product_id: " . $stock_stmt->error);
        echo json_encode(['success' => false, 'message' => "Failed to update stock for: $product_name"]);
        exit;
    }
}

// Optional: Save original data for review
file_put_contents('php_debug_log.txt', json_encode($data, JSON_PRETTY_PRINT));

echo json_encode(['success' => true, 'orderId' => $order_id]);
exit;
