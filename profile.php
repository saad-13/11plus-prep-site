<?php 
$currentPage = 'profile';
include 'includes/auth_check.php';

// Check if user is logged in; if not, redirect to login.
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

include 'includes/db.php';

$stmt = $pdo->prepare("SELECT full_name, username, email, progress, badges FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();
?>
<?php include 'includes/header.php'; ?>

<div class="container mt-5">
  <h2 style="color: #F26419;">My Account</h2>
  
  <!-- My Details Section -->
  <div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
      <span>My Details</span>
      <a href="edit_profile.php" class="btn btn-sm btn-outline-primary">Edit</a>
    </div>
    <div class="card-body">
      <div class="row mb-2">
        <div class="col-sm-3"><strong>Full Name:</strong></div>
        <div class="col-sm-9"><?php echo htmlspecialchars($user['full_name'] ?? $user['username']); ?></div>
      </div>
      <div class="row mb-2">
        <div class="col-sm-3"><strong>Username:</strong></div>
        <div class="col-sm-9"><?php echo htmlspecialchars($user['username']); ?></div>
      </div>
      <div class="row mb-2">
        <div class="col-sm-3"><strong>Email:</strong></div>
        <div class="col-sm-9"><?php echo htmlspecialchars($user['email']); ?></div>
      </div>
    </div>
  </div>
  
  <!-- Learning Progress Section -->
  <div class="card mb-4">
    <div class="card-header">
      Learning Progress
    </div>
    <div class="card-body">
      <p>Your learning progress is <strong><?php echo htmlspecialchars($user['progress']); ?>%</strong>.</p>
      <div class="progress">
        <div class="progress-bar" role="progressbar" style="width: <?php echo htmlspecialchars($user['progress']); ?>%;" aria-valuenow="<?php echo htmlspecialchars($user['progress']); ?>" aria-valuemin="0" aria-valuemax="100">
          <?php echo htmlspecialchars($user['progress']); ?>%
        </div>
      </div>
    </div>
  </div>
  
  <!-- Achievements Section -->
  <div class="card mb-4">
    <div class="card-header">
      Badges/Achievements
    </div>
    <div class="card-body">
      <p><?php echo htmlspecialchars($user['badges']); ?></p>
    </div>
  </div>
  
  
  
</div>

<?php include 'includes/footer.php'; ?>
