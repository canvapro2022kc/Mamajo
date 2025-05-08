<?php
session_start();

$correct_answer = "2521119";
$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $answer = str_replace(' ', '', $_POST["answer"]);
    
    if ($answer === $correct_answer) {
        // You could redirect or display the password/reset link here
        $message = "Correct! Your password is: <strong>admin123</strong> (or proceed to reset).";
    } else {
        $message = "Incorrect code. Try again, bestie.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Forgot Password - Mamajo's POS</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
  <div class="row justify-content-center">
    <div class="col-md-6">
      <div class="card shadow">
        <div class="card-header bg-success text-white text-center">
          Forgot Password?
        </div>
        <div class="card-body">
          <p>To verify you're the admin, answer this:</p>
          <p><strong>What's your pet cat's name?</strong></p>
          <p class="text-muted">Type the <em>numeric position</em> of each letter. (A=1, B=2, ..., Z=26)</p>

          <form method="POST">
            <input type="text" name="answer" class="form-control mb-3" placeholder="Your Answer" required>
            <button type="submit" class="btn btn-success w-100">Submit</button>
          </form>

          <?php if ($message): ?>
            <div class="alert alert-info mt-3">
              <?= $message ?>
            </div>
          <?php endif; ?>

          <a href="login.php" class="d-block mt-3 text-center">Back to Login</a>
        </div>
      </div>
    </div>
  </div>
</div>

</body>
</html>
