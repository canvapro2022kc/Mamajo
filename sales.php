<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}
require 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    $id = $_POST['product_id'];
    $_SESSION['cart'][$id] = ($_SESSION['cart'][$id] ?? 0) + 1;
}

$products = $conn->query("SELECT * FROM products WHERE archived = 0");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Mamajo's POS</title>
  <link rel="stylesheet" href="pos.css" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    body {
      background-color: #ff9533 !important;
    }

    .order-summary {
      background-color: #fff;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }

    .checkout-button {
      background-color: #ff220c !important;
      border-color: #ff220c !important;
      color: #fff !important;
    }

    .checkout-button:hover {
      background-color: #e61c00 !important;
      border-color: #e61c00 !important;
    }

    .product-grid {
      display: flex;
      flex-wrap: wrap;
      gap: 1rem;
    }

    .product {
      flex: 1 1 calc(25% - 1rem);
      max-width: calc(25% - 1rem);
      box-shadow: 0 0 8px rgba(0, 0, 0, 0.1);
      border-radius: 8px;
      padding: 10px;
      text-align: center;
    }

    @media (max-width: 768px) {
      .product {
        flex: 1 1 calc(50% - 1rem);
        max-width: calc(50% - 1rem);
      }
    }

    @media (max-width: 480px) {
      .product {
        flex: 1 1 100%;
        max-width: 100%;
      }
    }

    .product img {
      max-width: 100%;
      max-height: 150px;
      object-fit: contain;
      margin-bottom: 10px;
    }

    .product.disabled {
      opacity: 0.5;
      pointer-events: none;
    }

    #datetime-container {
      font-weight: bold;
      color: #055b00;
      font-size: 15px;
    }
  </style>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="container mt-4">
  <header class="mb-3">
  <div class="d-flex justify-content-between align-items-center mb-3">
  <h3 class="mb-0">Available Products</h3>
  <div id="datetime-container" class="text-end" style="min-width: 850px;">
  <div id="date"></div>
    <div id="time"></div>
    </div>
  </div>
  </header>

  <div class="row">
    <div class="col-md-8">
      <div class="product-grid">
        <?php while ($row = $products->fetch_assoc()): ?>
          <?php if ($row['stock'] > 0): ?>
            <div class="product" data-id="<?= $row['id'] ?>" data-name="<?= htmlspecialchars($row['name']) ?>" data-price="<?= $row['price'] ?>">
              <img src="<?= htmlspecialchars($row['image']) ?>" alt="<?= htmlspecialchars($row['name']) ?>">
              <p class="product-name fw-bold"><?= htmlspecialchars($row['name']) ?></p>
              <p class="product-price">₱<?= number_format($row['price'], 2) ?></p>
            </div>
          <?php else: ?>
            <div class="product disabled">
              <img src="<?= htmlspecialchars($row['image']) ?>" alt="<?= htmlspecialchars($row['name']) ?>">
              <p class="product-name text-muted"><?= htmlspecialchars($row['name']) ?></p>
              <p class="product-price text-muted">₱<?= number_format($row['price'], 2) ?> (Unavailable)</p>
            </div>
          <?php endif; ?>
        <?php endwhile; ?>
      </div>
    </div>

    <div class="col-md-4">
      <div class="order-summary">
        <h3 id="order-id">Order ID: #</h3>
        <table class="table">
          <thead>
            <tr>
              <th>Product</th>
              <th>Price</th>
              <th>Qty</th>
              <th>Amount</th>
            </tr>
          </thead>
          <tbody id="order-items"></tbody>
          <tfoot>
            <tr>
              <td colspan="3"><strong>Total</strong></td>
              <td><strong>₱0.00</strong></td>
            </tr>
          </tfoot>
        </table>

        <div class="mb-2">
          <label for="customerName">Customer Name:</label>
          <input type="text" id="customerName" class="form-control" required />
        </div>

        <div class="mb-2">
          <label for="customerContact">Contact Number:</label>
          <input type="tel" id="customerContact" class="form-control" required />
        </div>

        <div class="mb-2">
          <label for="amountGiven">Amount Given:</label>
          <input type="number" id="amountGiven" class="form-control" min="0" />
        </div>

        <div class="mb-2">
          <label for="change">Change:</label>
          <input type="text" id="change" class="form-control" disabled />
        </div>

        <button class="btn btn-primary w-100 checkout-button">Checkout</button>
      </div>
    </div>
  </div>
</div>

<!-- Quantity Modal -->
<div id="quantityModal" class="quantity-popup" style="display: none;">
  <h2 id="modalProductName">Enter Quantity</h2>
  <input type="number" id="quantityInput" min="1" value="1"><br>
  <button onclick="confirmQuantity()">Add to Order</button>
</div>

<script>
  const orderItems = [];
  let selectedProductName = '';
  let selectedProductPrice = 0;
  let selectedProductId = null;

  function updateDateTime() {
    const now = new Date();
    const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
    document.getElementById('date').textContent = now.toLocaleDateString(undefined, options);
    document.getElementById('time').textContent = now.toLocaleTimeString();
  }

  function generateOrderID() {
    const now = Date.now();
    const rand = Math.floor(1000 + Math.random() * 9000);
    return `ORD-${now}-${rand}`;
  }

  document.getElementById('order-id').textContent = 'Order ID: ' + generateOrderID();
  updateDateTime();
  setInterval(updateDateTime, 1000);

  function formatCurrency(val) {
    return `₱${parseFloat(val).toFixed(2)}`;
  }

  document.querySelectorAll('.product:not(.disabled)').forEach(prod => {
    prod.addEventListener('click', () => {
      selectedProductId = parseInt(prod.dataset.id);
      selectedProductName = prod.dataset.name;
      selectedProductPrice = parseFloat(prod.dataset.price);
      document.getElementById('modalProductName').textContent = `Enter quantity for ${selectedProductName}`;
      document.getElementById('quantityInput').value = 1;
      document.getElementById('quantityModal').style.display = 'block';
    });
  });

  function confirmQuantity() {
    const qty = parseInt(document.getElementById('quantityInput').value, 10);
    if (!isNaN(qty) && qty > 0) {
      addToOrderSummary(selectedProductId, selectedProductName, selectedProductPrice, qty);
    }
    document.getElementById('quantityModal').style.display = 'none';
  }

  function addToOrderSummary(id, name, price, quantity) {
    const amount = price * quantity;
    orderItems.push({ product_id: id, product: name, price, quantity, amount });

    const tbody = document.getElementById('order-items');
    const row = document.createElement('tr');
    row.innerHTML = `
      <td>${name}</td>
      <td>${formatCurrency(price)}</td>
      <td>${quantity}</td>
      <td>${formatCurrency(amount)}</td>
    `;
    tbody.appendChild(row);
    updateTotal();
  }

  function updateTotal() {
    const total = orderItems.reduce((s, i) => s + i.amount, 0);
    document.querySelector('tfoot td:last-child').innerHTML = `<strong>${formatCurrency(total)}</strong>`;
    calculateChange();
  }

  function calculateChange() {
    const given = parseFloat(document.getElementById('amountGiven').value);
    const total = orderItems.reduce((s, i) => s + i.amount, 0);
    const changeInput = document.getElementById('change');
    changeInput.value = (!isNaN(given) && given >= total) ? formatCurrency(given - total) : '₱0.00';
  }

  document.getElementById('amountGiven').addEventListener('input', calculateChange);

  document.querySelector('.checkout-button').addEventListener('click', () => {
    const name = document.getElementById('customerName').value.trim();
    const contact = document.getElementById('customerContact').value.trim();
    if (!name || !contact) return alert('Enter customer name & contact');
    if (orderItems.length === 0) return alert('No items in the order');

    const amountGiven = parseFloat(document.getElementById('amountGiven').value) || 0;

    fetch('submit_order.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        customerName: name,
        customerContact: contact,
        amountGiven: amountGiven,
        items: orderItems.map(i => ({
          product_id: i.product_id,
          title: i.product,
          price: i.price,
          quantity: i.quantity
        }))
      })
    })
    .then(r => r.json())
    .then(res => {
      if (res.success) {
        window.open(`receipt.php?id=${res.orderId}`, '_blank');
        document.getElementById('order-items').innerHTML = '';
        orderItems.length = 0;
        updateTotal();
      } else {
        alert('Error: ' + res.message);
      }
    })
    .catch(err => {
      alert('Submission failed: ' + err.message);
    });
  });
</script>

</body>
</html>
