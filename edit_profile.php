<?php
//session_start();
include 'includes/auth_check.php';
include 'includes/db.php';

// Ensure user is logged in.
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
// Fetch the current user details.
$stmt = $pdo->prepare("SELECT full_name, username, email FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve and trim form data.
    $full_name = trim($_POST['full_name']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Basic validation.
    if (empty($full_name)) {
        $errors[] = "Full Name is required.";
    }
    if (empty($username)) {
        $errors[] = "Username is required.";
    }
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "A valid email is required.";
    }
    // Only validate password if a new one is provided.
    if (!empty($password) || !empty($confirm_password)) {
        if ($password !== $confirm_password) {
            $errors[] = "Passwords do not match.";
        }
    }
    
    // If no errors, update the user's record.
    if (empty($errors)) {
        if (!empty($password)) {
            // Hash the new password.
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmtUpdate = $pdo->prepare("UPDATE users SET full_name = ?, username = ?, email = ?, password = ? WHERE id = ?");
            $updated = $stmtUpdate->execute([$full_name, $username, $email, $hashedPassword, $user_id]);
        } else {
            $stmtUpdate = $pdo->prepare("UPDATE users SET full_name = ?, username = ?, email = ? WHERE id = ?");
            $updated = $stmtUpdate->execute([$full_name, $username, $email, $user_id]);
        }
        
        if ($updated) {
            $success = "Profile updated successfully.";
            // Update session variable if username changed.
            $_SESSION['username'] = $username;
            // Reload user details.
            $stmt = $pdo->prepare("SELECT full_name, username, email FROM users WHERE id = ?");
            $stmt->execute([$user_id]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            $errors[] = "Error updating profile. Please try again.";
        }
    }
}
?>
<?php include 'includes/header.php'; ?>
<div class="wrapper">
  <div class="content">
        <div class="container mt-5">
            <h2>My Account - Edit Profile</h2>
            
            <!-- Display errors if any -->
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            
            <!-- Display success message -->
            <?php if ($success): ?>
                <div class="alert alert-success">
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>
            
            <!-- My Details Card -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Edit My Details</span>
                    <!-- A cancel button linking back to the profile page -->
                    <a href="profile.php" class="btn btn-sm btn-outline-secondary">Cancel</a>
                </div>
                <div class="card-body">
                    <form method="post" action="">
                        <div class="mb-3">
                            <label for="full_name" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="full_name" name="full_name" value="<?php echo htmlspecialchars($user['full_name'] ?? $user['username']); ?>">
                        </div>
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>">
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>">
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">New Password</label>
                            <input type="password" class="form-control" id="password" name="password" placeholder="Leave blank to keep current password">
                        </div>
                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Confirm New Password</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Leave blank to keep current password">
                        </div>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php include 'includes/footer.php'; ?>
</div>