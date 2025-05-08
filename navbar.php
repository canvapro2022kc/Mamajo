<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<style>
  .navbar-mamajo {
    background-color: #055b00;
  }

  .navbar-mamajo .nav-link,
  .navbar-mamajo .navbar-brand,
  .navbar-mamajo .navbar-text {
    color: #fff;
  }

  /* Regular nav links from Home to Users */
  .navbar-mamajo .nav-link {
    opacity: 0.9;
    transition: all 0.3s ease;
  }

  /* Hover effect: full opacity + highlight color */
  .navbar-mamajo .nav-link:hover {
    opacity: 1;
    color: #ff9533;
  }

  /* Active (current page) link styling */
  .navbar-mamajo .nav-link.active {
    color: #ff9533 !important;
    opacity: 1 !important;
    font-weight: 500;
  }

  /* Logout button */
  .navbar-mamajo .btn-outline-light {
    border-color: #fff;
    color: #fff;
  }

  .navbar-mamajo .btn-outline-light:hover {
    background-color: #fff;
    color: #055b00;
  }
</style>

<nav class="navbar navbar-expand-lg navbar-mamajo">
  <div class="container-fluid">
    <a class="navbar-brand fw-bold" href="home.php">Mamajo's</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="mainNavbar">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item"><a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'home.php' ? 'active' : '' ?>" href="home.php">Home</a></li>
        <li class="nav-item"><a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'sales.php' ? 'active' : '' ?>" href="sales.php">Point of Sales</a></li>
        <li class="nav-item"><a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'orders.php' ? 'active' : '' ?>" href="orders.php">Orders</a></li>
        <li class="nav-item"><a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'sales_report.php' ? 'active' : '' ?>" href="sales_report.php">Sales Report</a></li>
        <li class="nav-item"><a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'inventory.php' ? 'active' : '' ?>" href="inventory.php">Inventory</a></li>
        <li class="nav-item"><a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'feedback.php' ? 'active' : '' ?>" href="feedback.php">Feedback</a></li>
        <li class="nav-item"><a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'users.php' ? 'active' : '' ?>" href="users.php">Users</a></li>
      </ul>
      <ul class="navbar-nav">
        <li class="nav-item">
          <span class="navbar-text text-light me-3">
            Logged in as: <?= $_SESSION['username'] ?? 'Guest'; ?>
          </span>
        </li>
        <li class="nav-item">
          <a class="btn btn-outline-light btn-sm" href="logout.php">Logout</a>
        </li>
      </ul>
    </div>
  </div>
</nav>
