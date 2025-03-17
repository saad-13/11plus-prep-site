<?php
session_start();
include 'includes/db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve username and password inputs.
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if ($username && $password) {
        // Prepare a statement to select the user by username.
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        // Verify the password using password_verify()
        if ($user && password_verify($password, $user['password'])) {
            // session variables and redirect to the homepage.
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
<div class="container mt-5">
  <div class="row justify-content-center">
    <div class="col-md-6">
      <div class="card shadow-sm">
        <div class="card-header bg-accent text-white text-center">
          <h3>Log In</h3>
        </div>
        <div class="card-body">
          <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
          <?php endif; ?>
          <form action="login.php" method="post" id="loginForm">
            <div class="mb-3">
              <label for="username" class="form-label">Username:</label>
              <input type="text" class="form-control" id="username" name="username" required>
              <small class="hint text-muted" id="usernameHint" style="display:none;">Enter your unique username (e.g., superhero123).</small>
            </div>
            <div class="mb-3">
              <label for="password" class="form-label">Password:</label>
              <input type="password" class="form-control" id="password" name="password" required>
              <small class="hint text-muted" id="passwordHint" style="display:none;">Enter your secret password.</small>
            </div>
            <button type="submit" class="btn btn-primary btn-lg w-100">Log In</button>
          </form>
          <p class="mt-3 text-center">Don't have an account? <a href="signup.php">Sign Up</a></p>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
// Show hint text on focus and hide on blur.
document.getElementById('username').addEventListener('focus', function() {
    document.getElementById('usernameHint').style.display = 'block';
});
document.getElementById('username').addEventListener('blur', function() {
    document.getElementById('usernameHint').style.display = 'none';
});
document.getElementById('password').addEventListener('focus', function() {
    document.getElementById('passwordHint').style.display = 'block';
});
document.getElementById('password').addEventListener('blur', function() {
    document.getElementById('passwordHint').style.display = 'none';
});
</script>

<?php include 'includes/footer.php'; ?>
