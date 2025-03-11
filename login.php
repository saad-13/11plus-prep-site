<?php
// login.php
session_start();
include 'includes/db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve and sanitize the username and password inputs.
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if ($username && $password) {
        // Prepare a statement to select the user by username.
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        // Verify the password using password_verify()
        if ($user && password_verify($password, $user['password'])) {
            // Set session variables and redirect to the homepage.
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            header("Location: index.php");
            exit;
        } else {
            $error = "Invalid username or password.";
        }
    } else {
        $error = "Please enter both username and password.";
    }
}
?>


<?php include 'includes/header.php'; ?>
<div class="wrapper">
  <div class="content">
    <div class="container mt-5">
      <h2>Log In</h2>
      <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
      <?php endif; ?>
      <form action="login.php" method="post">
        <div class="mb-3">
          <label for="username" class="form-label">Username:</label>
          <input type="text" class="form-control" id="username" name="username" required>
        </div>
        <div class="mb-3">
          <label for="password" class="form-label">Password:</label>
          <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <button type="submit" class="btn btn-primary">Log In</button>
      </form>
      <p class="mt-3">Don't have an account? <a href="signup.php">Sign Up</a></p>
    </div>
  </div>  
  <?php include 'includes/footer.php'; ?>
</div>
