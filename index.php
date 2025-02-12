<?php
// index.php
session_start();
// Check if user is logged in; if not, redirect to login.
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>
<?php include 'includes/header.php'; ?>
<?php include 'includes/auth_check.php'; ?>

<div class="container mt-5">
  <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
  <div class="row mt-4">
    <div class="col-md-4">
      <div class="card text-center">
        <div class="card-body">
          <h5 class="card-title">Progress</h5>
          <p class="card-text">50% Complete</p>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card text-center">
        <div class="card-body">
          <h5 class="card-title">Recent Activity</h5>
          <p class="card-text">Completed a practice paper</p>
        </div>
      </div>
    </div>
    <!-- Add more cards as needed -->
  </div>
  <div class="mt-4">
    <a href="learn.php" class="btn btn-success">Go to Learn</a>
    <a href="practice.php" class="btn btn-primary">Start Practice</a>
  </div>
</div>
<?php include 'includes/footer.php'; ?>
