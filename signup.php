<?php
include 'includes/db.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    //  validate inputs.
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
    
    // If no errors, insert the user into the database.
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
<div class="container mt-5">
  <div class="row justify-content-center">
    <div class="col-md-6">
      <div class="card shadow-sm">
        <div class="card-header bg-accent text-white text-center">
          <h3>Sign Up</h3>
        </div>
        <div class="card-body">
          <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
              <ul>
                <?php foreach ($errors as $error): ?>
                  <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
              </ul>
            </div>
          <?php endif; ?>
          <form id="signupForm" action="signup.php" method="post">
            <div class="mb-3">
              <label for="username" class="form-label">Username:</label>
              <input type="text" class="form-control" id="username" name="username" required>
              <small class="hint text-muted" id="usernameHint" style="display:none;">Choose a fun and unique username!</small>
            </div>
            <div class="mb-3">
              <label for="email" class="form-label">Email:</label>
              <input type="email" class="form-control" id="email" name="email" required>
              <small class="hint text-muted" id="emailHint" style="display:none;">Enter your email address (Ask a parent help!)</small>
            </div>
            <div class="mb-3">
              <label for="confirmEmail" class="form-label">Confirm Email:</label>
              <input type="email" class="form-control" id="confirmEmail" name="confirmEmail" required>
              <small class="hint text-muted" id="confirmEmailHint" style="display:none;">Re-enter your email to confirm (Ask a parent help!)</small>
            </div>
            <div class="mb-3">
              <label for="password" class="form-label">Password:</label>
              <input type="password" class="form-control" id="password" name="password" required>
              <small class="hint text-muted" id="passwordHint" style="display:none;">Pick a secret password (minimum 6 characters).</small>
            </div>
            <div class="mb-3">
              <label for="confirmPassword" class="form-label">Confirm Password:</label>
              <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" required>
              <small class="hint text-muted" id="confirmPasswordHint" style="display:none;">Re-enter your password for confirmation.</small>
            </div>
            <button type="submit" class="btn btn-primary btn-lg w-100">Sign Up</button>
          </form>
          <p class="mt-3 text-center">Already have an account? <a href="login.php">Log In</a></p>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
// Function to attach focus and blur events for hints.
function addHint(inputId, hintId) {
    document.getElementById(inputId).addEventListener('focus', function() {
        document.getElementById(hintId).style.display = 'block';
    });
    document.getElementById(inputId).addEventListener('blur', function() {
        document.getElementById(hintId).style.display = 'none';
    });
}

// Attach hints for each field.
addHint('username', 'usernameHint');
addHint('email', 'emailHint');
addHint('confirmEmail', 'confirmEmailHint');
addHint('password', 'passwordHint');
addHint('confirmPassword', 'confirmPasswordHint');
</script>

<?php include 'includes/footer.php'; ?>
