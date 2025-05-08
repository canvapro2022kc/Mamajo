<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$showArchived = isset($_GET['archived']) && $_GET['archived'] == '1';

// Fetch orders based on view mode
if ($showArchived) {
    $orders = $conn->query("SELECT * FROM orders WHERE archived = 1 ORDER BY created_at DESC");
} else {
    $orders = $conn->query("SELECT * FROM orders WHERE archived = 0 ORDER BY created_at DESC");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Mamajo's</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #ff9533 !important;
    }
    .orders-container {
      background: white;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 0 8px rgba(0, 0, 0, 0.1);
    }
    .btn-custom-green {
      background-color: #055b00;
      color: white;
      border: none;
    }
    .btn-custom-green:hover {
      background-color: #044a00;
      color: white;
    }
    .btn-custom-red {
      background-color: #ff220c;
      color: white;
      border: none;
    }
    .btn-custom-red:hover {
      background-color: #e01e0a;
      color: white;
    }
  </style>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="container mt-5 orders-container">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3><?= $showArchived ? 'Archived Orders' : 'Order History' ?></h3>
    <a href="orders.php?archived=<?= $showArchived ? '0' : '1' ?>" class="btn btn-sm btn-secondary">
      <?= $showArchived ? 'Show Active Orders' : 'Show Archived Orders' ?>
    </a>
  </div>

  <table class="table table-striped table-bordered">
    <thead>
      <tr>
        <th>Order ID</th>
        <th>Total</th>
        <th>Date</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($row = $orders->fetch_assoc()): ?>
      <tr>
        <td><?= $row['id'] ?></td>
        <td>₱<?= number_format($row['total_amount'], 2) ?></td>
        <td><?= $row['created_at'] ?></td>
        <td>
          <a href="receipt.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-custom-green" target="_blank">Print Receipt</a>
          <?php if ($showArchived): ?>
            <a href="toggle_archive_order.php?id=<?= $row['id'] ?>&action=unarchive&archived=1" class="btn btn-sm btn-custom-red" onclick="return confirm('Unarchive this order?');">Unarchive</a>
          <?php else: ?>
            <a href="toggle_archive_order.php?id=<?= $row['id'] ?>&action=archive&archived=0" class="btn btn-sm btn-custom-red" onclick="return confirm('Are you sure you want to archive this order?');">Archive</a>
          <?php endif; ?>
        </td>
      </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</div>

</body>
</html>
