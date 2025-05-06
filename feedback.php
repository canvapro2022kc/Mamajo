<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$feedbacks = $conn->query("SELECT * FROM feedback ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Customer Feedback</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="container mt-5">
  <h3>Customer Feedback</h3>

  <table class="table table-bordered mt-3">
    <thead>
      <tr>
        <th>Name</th>
        <th>Message</th>
        <th>Date</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($fb = $feedbacks->fetch_assoc()): ?>
      <tr>
        <td><?= htmlspecialchars($fb['name']) ?></td>
        <td><?= nl2br(htmlspecialchars($fb['message'])) ?></td>
        <td><?= $fb['created_at'] ?></td>
      </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</div>

</body>
</html>
