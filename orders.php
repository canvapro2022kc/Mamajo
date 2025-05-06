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
  <title>Orders - Mamajo's POS</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="container mt-5">
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
        <a href="receipt.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-primary" target="_blank">Print Receipt</a>
          <?php if ($showArchived): ?>
            <a href="toggle_archive_order.php?id=<?= $row['id'] ?>&action=unarchive&archived=1" class="btn btn-sm btn-outline-warning" onclick="return confirm('Unarchive this order?');">Unarchive</a>
          <?php else: ?>
            <a href="toggle_archive_order.php?id=<?= $row['id'] ?>&action=archive&archived=0" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to archive this order?');">Archive</a>
          <?php endif; ?>
        </td>
      </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</div>

</body>
</html>
