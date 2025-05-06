<?php
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $message = $_POST['message'];
    $conn->query("INSERT INTO feedback (name, message) VALUES ('$name', '$message')");
    $success = true;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Feedback</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">

  <h3 class="mb-4">We'd love your feedback</h3>

  <?php if (isset($success)): ?>
    <div class="alert alert-success">Thank you for your feedback!</div>
  <?php endif; ?>

  <form method="POST" class="row g-3">
    <div class="col-md-6">
      <input type="text" name="name" class="form-control" placeholder="Your Name" required>
    </div>
    <div class="col-12">
      <textarea name="message" rows="5" class="form-control" placeholder="Your Feedback" required></textarea>
    </div>
    <div class="col-12">
      <button type="submit" class="btn btn-primary">Submit Feedback</button>
    </div>
  </form>

</body>
</html>
