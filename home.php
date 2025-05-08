<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Line chart: Sales data
$chart_data = [];
$sales_result = $conn->query("SELECT DATE(created_at) as sale_date, SUM(total_amount) as total_sales FROM orders GROUP BY DATE(created_at)");
while ($row = $sales_result->fetch_assoc()) {
    $chart_data[] = [
        'date' => $row['sale_date'],
        'total' => (float)$row['total_sales']
    ];
}

// Top 3 Most Ordered Products
$top_products_query = "
    SELECT oi.product_name, SUM(oi.quantity) as total_quantity
    FROM order_items oi
    GROUP BY oi.product_name
    ORDER BY total_quantity DESC
    LIMIT 3
";
$top_products_result = $conn->query($top_products_query);
$top_products = [];
if ($top_products_result && $top_products_result->num_rows > 0) {
    $top_products = $top_products_result->fetch_all(MYSQLI_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Mamajo's</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    .top-products-card {
      font-size: 0.9rem;
    }
    .top-products-card .list-group-item {
      font-size: 0.9rem;
    }
  </style>
</head>
<body style="background-color: #ff9533;">

<?php include 'navbar.php'; ?>

<div class="container mt-5">
  <h3>Welcome, <?= htmlspecialchars($_SESSION['username']); ?>!</h3>
  <p class="lead">Use the navigation menu to manage sales, inventory, and reports.</p>

  <div class="row">
    <!-- Sales Line Chart -->
    <div class="col-md-8 mb-4">
      <div class="card h-100">
        <div class="card-header bg-success text-white">
          <b>Sales Chart</b>
        </div>
        <div class="card-body">
          <canvas id="salesChart" height="120"></canvas>
        </div>
      </div>
    </div>

    <!-- Top 3 Most Ordered Products -->
    <div class="col-md-4 mb-4">
      <div class="card top-products-card h-100 border-success">
        <div class="card-header bg-success text-white text-center">
          <b>Top 3 Ordered Products</b>
        </div>
        <div class="card-body p-2">
          <ul class="list-group list-group-flush">
            <?php foreach ($top_products as $product): ?>
              <li class="list-group-item d-flex justify-content-between align-items-center">
                <span class="text-success"><?= htmlspecialchars($product['product_name']) ?></span>
                <span class="badge bg-success rounded-pill"><?= $product['total_quantity'] ?></span>
              </li>
            <?php endforeach; ?>
          </ul>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  // Line Chart (Sales)
  const salesData = <?= json_encode($chart_data); ?>;
  const labels = salesData.map(item => item.date);
  const data = salesData.map(item => item.total);

  const ctx = document.getElementById('salesChart').getContext('2d');
  new Chart(ctx, {
    type: 'line',
    data: {
      labels: labels,
      datasets: [{
        label: 'Total Sales',
        data: data,
        fill: false,
        borderColor: 'green',
        backgroundColor: 'green',
        tension: 0.1
      }]
    },
    options: {
      responsive: true,
      plugins: {
        legend: {
          labels: {
            color: 'green'
          }
        }
      },
      scales: {
        x: {
          title: {
            display: true,
            text: 'Date'
          }
        },
        y: {
          title: {
            display: true,
            text: 'Sales'
          }
        }
      }
    }
  });
</script>
</body>
</html>
