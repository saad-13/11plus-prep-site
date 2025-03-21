<?php 
  $currentPage = 'profile';
  include 'includes/auth_check.php';
  include 'includes/db.php';
  include 'includes/header.php'; 
  // Check if user is logged in; if not, redirect to login.
  if (!isset($_SESSION['user_id'])) {
      header("Location: login.php");
      exit;
  }


  // Fetch user details including profile picture.
  $stmt = $pdo->prepare("SELECT full_name, username, email, progress, badges, profile_picture FROM users WHERE id = ?");
  $stmt->execute([$_SESSION['user_id']]);
  $user = $stmt->fetch();
?>


<div class="wrapper">
  <div class="content">
    <div class="container mt-5">
      <h2 style="color: #F26419;">My Account</h2>
      
      <!-- Profile Picture Section -->
      <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
          <span>Profile Picture</span>
          <!-- Link to the page where the user can select from available pictures -->
          <a href="edit_profile_picture.php" class="btn btn-sm btn-outline-primary">Change Picture</a>
        </div>
        <div class="card-body text-center">
          <img src="images/profile/<?php echo htmlspecialchars($user['profile_picture']); ?>" alt="Profile Picture" class="img-thumbnail" style="width:150px; height:150px;">
        </div>
      </div>
      
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
  </div>  
  <?php include 'includes/footer.php'; ?>
</div>
