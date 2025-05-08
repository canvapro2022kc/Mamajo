<?php

include 'db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$from = $_GET['from'] ?? '';
$to = $_GET['to'] ?? '';

$query = "SELECT * FROM orders";
if ($from && $to) {
    $query .= " WHERE DATE(created_at) BETWEEN '$from' AND '$to'";
}

$query .= " ORDER BY created_at DESC";
$result = $conn->query($query);

$total_sales = 0;
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
    .report-container {
      background: white;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 0 8px rgba(0, 0, 0, 0.1);
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

<div class="container mt-5 report-container">
  <h3>Sales Report</h3>

  <form method="GET" class="row g-3 mb-4">
    <div class="col-md-4">
      <label for="from" class="form-label">From:</label>
      <input type="date" id="from" name="from" class="form-control" value="<?= $from ?>">
    </div>
    <div class="col-md-4">
      <label for="to" class="form-label">To:</label>
      <input type="date" id="to" name="to" class="form-control" value="<?= $to ?>">
    </div>
    <div class="col-md-4 d-flex align-items-end">
      <button type="submit" class="btn btn-custom-red w-100">Filter</button>
    </div>
  </form>

  <table class="table table-bordered">
    <thead class="table-light">
      <tr>
        <th>Order ID</th>
        <th>Customer</th>
        <th>Total Amount</th>
        <th>Date</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
          <td><?= $row['id'] ?></td>
          <td><?= htmlspecialchars($row['customer_name']) ?></td>
          <td>₱<?= number_format($row['total_amount'], 2) ?></td>
          <td><?= $row['created_at'] ?></td>
        </tr>
        <?php $total_sales += $row['total_amount']; ?>
      <?php endwhile; ?>
    </tbody>
  </table>

  <div class="text-end mt-3">
    <strong>Total Sales: ₱<?= number_format($total_sales, 2) ?></strong>
  </div>
</div>

</body>
</html>
