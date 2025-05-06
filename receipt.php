<?php
include 'db_connect.php';
if (!isset($_GET['id'])) {
  echo "Order ID is missing";
  exit;
}
$order_id = intval($_GET['id']);
// Fix: Use correct column name (id instead of order_id)
$stmt = $conn->prepare("SELECT * FROM orders WHERE id = ?");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

if (!$order) {
    die("❌ Order not found.");
}

// Fix: Query directly from order_items (no join needed)
$items_stmt = $conn->prepare("SELECT * FROM order_items WHERE order_id = ?");
$items_stmt->bind_param("i", $order_id);
$items_stmt->execute();
$items = $items_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Receipt</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
  body {
    font-family: Arial, sans-serif;
    color: #333;
  }

  .receipt-container {
    max-width: 600px;
    margin: auto;
    padding: 20px;
    border: 2px dashed #444;
    border-radius: 10px;
    background-color: #f9f9f9;
  }

  .receipt-header {
    text-align: center;
    margin-bottom: 20px;
  }

  .receipt-header h2 {
    margin-bottom: 5px;
    font-size: 28px;
    color: #2c3e50;
  }

  .receipt-info p {
    margin: 2px 0;
    font-size: 14px;
  }

  table {
    width: 100%;
    margin-top: 10px;
  }

  table th,
  table td {
    text-align: center;
    padding: 8px;
    font-size: 14px;
    border: 1px solid #ccc;
  }

  table th {
    background-color: #e9ecef;
  }

  .total-section {
    text-align: right;
    margin-top: 20px;
    font-size: 18px;
    font-weight: bold;
  }

  .btn-group {
    margin-top: 20px;
    text-align: center;
  }

  .btn-group .btn {
    margin: 0 10px;
  }

  @media print {
    .no-print {
      display: none;
    }

    body {
      background: white;
    }

    .receipt-container {
      border: none;
      box-shadow: none;
    }
  }
</style>

</head>
<body class="p-5">

<div class="receipt-container">
  <div class="receipt-header">
    <h2>Mamajo's POS</h2>
    <p class="text-muted">Customer Receipt</p>
  </div>

  <div class="receipt-info">
    <p>Receipt #: <?= $order_id ?></p>
    <p>Date: <?= $order['created_at'] ?></p>
    <p>Customer: <?= htmlspecialchars($order['customer_name']) ?></p>
    <p>Contact: <?= htmlspecialchars($order['customer_contact']) ?></p>
    <p>Amount Given: ₱<?= number_format($order['amount_given'], 2) ?></p>
    <p>Change: ₱<?= number_format($order['amount_given'] - $order['total_amount'], 2) ?></p>
  </div>

  <table class="table table-bordered mt-3">
  <thead>
    <tr>
      <th>Product</th>
      <th>Qty</th>
      <th>Price</th>
      <th>Total</th>
    </tr>
  </thead>
  <tbody>
    <?php while ($item = $items->fetch_assoc()): ?>
      <tr>
        <td><?= htmlspecialchars($item['product_name']) ?></td>
        <td><?= $item['quantity'] ?></td>
        <td>₱<?= number_format($item['price'], 2) ?></td>
        <td>₱<?= number_format($item['price'] * $item['quantity'], 2) ?></td>
      </tr>
    <?php endwhile; ?>
  </tbody>
</table>

  <div class="total-section">
    Total: ₱<?= number_format($order['total_amount'], 2) ?>
  </div>

  <!-- QR Code Section -->
  <div class="feedback-qr" style="text-align: center; margin-top: 30px;">
    <p style="font-size: 14px; margin-bottom: 5px;">
      We value your feedback!<br>
      <strong>Scan the QR code to leave a review:</strong>
    </p>
    <img src="images/feedback QR.png" alt="Feedback QR Code" style="width: 120px; height: 120px; margin-top: 10px;">
  </div>

  <div class="btn-group no-print">
    <button onclick="window.print()" class="btn btn-primary">🖨️ Print Receipt</button>
    <a href="sales.php" class="btn btn-secondary">← Back to POS</a>
  </div>
</div>

</body>
</html>