<?php
// signup.php
include 'includes/db.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate inputs
    $username = trim($_POST['username']);
    $email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
    $confirmEmail = filter_var(trim($_POST['confirmEmail']), FILTER_VALIDATE_EMAIL);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];

    if (!$username) {
        $errors[] = "Username is required.";
    }
    if (!$email || !$confirmEmail || $email !== $confirmEmail) {
        $errors[] = "Emails do not match or are invalid.";
    }
    if ($password !== $confirmPassword) {
        $errors[] = "Passwords do not match.";
    }

    // If no errors, insert the user into the database
    if (empty($errors)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        if ($stmt->execute([$username, $email, $hashedPassword])) {
            header("Location: login.php");
            exit;
        } else {
            $errors[] = "An error occurred. Please try again.";
        }
    }
}
?>

<?php include 'includes/header.php'; ?>
<div class="wrapper">
  <div class="content">
    <div class="container mt-5">
      <h2>Sign Up</h2>
      <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
          <?php foreach ($errors as $error): ?>
              <p><?php echo htmlspecialchars($error); ?></p>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
      <form id="signupForm" action="signup.php" method="post">
        <div class="mb-3">
          <label for="username" class="form-label">Username:</label>
          <input type="text" class="form-control" id="username" name="username" required>
        </div>
        <div class="mb-3">
          <label for="email" class="form-label">Email:</label>
          <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <div class="mb-3">
          <label for="confirmEmail" class="form-label">Confirm Email:</label>
          <input type="email" class="form-control" id="confirmEmail" name="confirmEmail" required>
        </div>
        <div class="mb-3">
          <label for="password" class="form-label">Password:</label>
          <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <div class="mb-3">
          <label for="confirmPassword" class="form-label">Confirm Password:</label>
          <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" required>
        </div>
        <button type="submit" class="btn btn-primary">Sign Up</button>
      </form>
      <p class="mt-3">Already have an account? <a href="login.php">Log In</a></p>
    </div>
  </div>
  <?php include 'includes/footer.php'; ?>
</div>