<?php
// Show session status early for debugging (remove after fixing)
//echo "Session status BEFORE anything: " . session_status() . "<br>";

error_reporting(E_ALL);
ini_set('display_errors', 1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include DB connection
include 'db_connect.php';

// Initialize error variable
$error = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
        if (password_verify($password, $user['password'])) {
            // Store user info in session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            // Redirect to homepage
            header("Location: home.php");
            exit();
        }
    }

    $error = "Invalid username or password.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login - Mamajo's POS</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="login.css" />
</head>
<body>

<img src="images/Your paragraph text (2).png" class="background-overlay" alt="background image">

<header class="page-header">
  <div class="header-text">
    <span class="mamajos-title">Mamajo's | </span>
    <span class="subtitle"> Point of Sales & Inventory Management System</span>
  </div>
</header>

<div class="custom-body">
<div class="main-content">
  <!-- Login card first (now on the left) -->
  <div class="card shadow-sm p-4 login-container" style="width: 20rem;">
    <h4 class="text-center mb-4">Hello there!</h4>

    <?php if (!empty($error)): ?>
      <div class="alert alert-danger text-center"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST">
      <div class="mb-3">
        <input type="text" name="username" class="form-control" placeholder="Username" required>
      </div>
      <div class="mb-3">
        <input type="password" name="password" class="form-control" placeholder="Password" required>
      </div>
      <button type="submit" class="btn">Login</button>
      <p class="mt-3 text-center">
        <a href="forgot_password.php">Forgot Password?</a>
      </p>

    </form>
  </div>

  <!-- Mascot second (now on the right) -->
  <div class="image-container">
    <img src="images/Hello Mamajo's NO BG (2).png" class="Mascot" alt="Mamajo Mascot">
  </div>
</div>

</body>
</html>