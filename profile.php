<?php
// profile.php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
include 'includes/db.php';
$stmt = $pdo->prepare("SELECT username, email, progress, badges FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();
?>
<?php include 'includes/header.php'; ?>
<div class="container mt-5">
  <h2>Profile</h2>
  <ul class="list-group">
    <li class="list-group-item"><strong>Username:</strong> <?php echo htmlspecialchars($user['username']); ?></li>
    <li class="list-group-item"><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></li>
    <li class="list-group-item"><strong>Learning Progress:</strong> <?php echo htmlspecialchars($user['progress']); ?>%</li>
    <li class="list-group-item"><strong>Badges/Achievements:</strong> <?php echo htmlspecialchars($user['badges']); ?></li>
  </ul>
</div>
<?php include 'includes/footer.php'; ?>
