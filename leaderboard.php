<?php include 'includes/header.php'; ?>
<div class="container mt-5">
  <h2>Leaderboard</h2>
  <div class="mb-3">
    <a href="leaderboard.php?filter=daily" class="btn btn-outline-primary">Daily</a>
    <a href="leaderboard.php?filter=weekly" class="btn btn-outline-primary">Weekly</a>
    <a href="leaderboard.php?filter=overall" class="btn btn-outline-primary">Overall</a>
  </div>
  <?php
    include 'includes/db.php';
    // For this example, we simply fetch the top 10 users by overall score.
    $stmt = $pdo->query("SELECT username, score FROM users ORDER BY score DESC LIMIT 10");
    $leaderboard = $stmt->fetchAll();
  ?>
  <table class="table table-striped">
    <thead>
      <tr>
        <th>Rank</th>
        <th>Username</th>
        <th>Score</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($leaderboard as $index => $user): ?>
        <tr>
          <td><?php echo $index + 1; ?></td>
          <td><?php echo htmlspecialchars($user['username']); ?></td>
          <td><?php echo htmlspecialchars($user['score']); ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php include 'includes/footer.php'; ?>
