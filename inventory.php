<?php
session_start(); // Add this at the very top

include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    echo "Redirecting...<br>"; // Add debug message
    header("Location: login.php");
    exit();
}
// Add or update product
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? '';
    $name = $_POST['name'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];

    if ($id) {
        $conn->query("UPDATE products SET name='$name', price=$price, stock=$stock WHERE id=$id");
    } else {
        $conn->query("INSERT INTO products (name, price, stock) VALUES ('$name', $price, $stock)");
    }
    header("Location: inventory.php");
    exit();
}

// Archive/unarchive
if (isset($_GET['archive'])) {
    $id = $_GET['archive'];
    $conn->query("UPDATE products SET archived = 1 WHERE id = $id");
    header("Location: inventory.php");
    exit();
}
if (isset($_GET['unarchive'])) {
    $id = $_GET['unarchive'];
    $conn->query("UPDATE products SET archived = 0 WHERE id = $id");
    header("Location: inventory.php");
    exit();
}

$products = $conn->query("SELECT * FROM products ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Inventory - Mamajo's POS</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="container mt-5">
  <h3>Inventory</h3>

  <!-- Add/Edit Form -->
  <form method="POST" class="row g-2 mb-4">
    <input type="hidden" name="id" id="product_id">
    <div class="col-md-3">
      <input type="text" name="name" id="name" class="form-control" placeholder="Product Name" required>
    </div>
    <div class="col-md-2">
      <input type="number" name="price" id="price" step="0.01" class="form-control" placeholder="Price" required>
    </div>
    <div class="col-md-2">
      <input type="number" name="stock" id="stock" class="form-control" placeholder="Stock" required>
    </div>
    <div class="col-md-2">
      <button type="submit" class="btn btn-success w-100">Save</button>
    </div>
    <div class="col-md-2">
      <button type="reset" onclick="clearForm()" class="btn btn-secondary w-100">Clear</button>
    </div>
  </form>

  <!-- Products Table -->
  <table class="table table-bordered">
    <thead>
      <tr>
        <th>ID</th><th>Name</th><th>Price</th><th>Stock</th><th>Status</th><th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($row = $products->fetch_assoc()): ?>
      <tr>
        <td><?= $row['id'] ?></td>
        <td><?= $row['name'] ?></td>
        <td>₱<?= number_format($row['price'], 2) ?></td>
        <td><?= $row['stock'] ?></td>
        <td><?= $row['archived'] ? 'Archived' : 'Active' ?></td>
        <td>
          <button class="btn btn-sm btn-primary" onclick="editProduct(<?= htmlspecialchars(json_encode($row)) ?>)">Edit</button>
          <?php if ($row['archived']): ?>
            <a href="?unarchive=<?= $row['id'] ?>" class="btn btn-sm btn-success">Unarchive</a>
          <?php else: ?>
            <a href="?archive=<?= $row['id'] ?>" class="btn btn-sm btn-danger">Archive</a>
          <?php endif; ?>
        </td>
      </tr>
      <?php endwhile; ?>
    </tbody>
  </table>

  <h4 class="mt-5">Archived Products</h4>
<table class="table table-striped">
  <thead><tr><th>ID</th><th>Name</th><th>Status</th><th>Action</th></tr></thead>
  <tbody>
    <?php
    $archived = $conn->query("SELECT * FROM products WHERE archived = 1");
    while ($row = $archived->fetch_assoc()):
    ?>
    <tr>
      <td><?= $row['id'] ?></td>
      <td><?= $row['name'] ?></td>
      <td><?= $row['archived'] ? 'Archived' : 'Active' ?></td>
      <td>
        <a href="archive_product.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-success">Unarchive</a>
      </td>
    </tr>
    <?php endwhile; ?>
  </tbody>
</table>

</div>

<script>
function editProduct(data) {
  document.getElementById('product_id').value = data.id;
  document.getElementById('name').value = data.name;
  document.getElementById('price').value = data.price;
  document.getElementById('stock').value = data.stock;
}
function clearForm() {
  document.getElementById('product_id').value = '';
}
</script>

</body>
</html>
