<?php 
$currentPage = 'leaderboard';
include 'includes/auth_check.php';
include 'includes/header.php';
include 'includes/db.php';

// Get the filter from the URL, defaulting to overall if not set.
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'overall';

// Build the WHERE clause based on the filter.
$whereClause = "";
switch($filter) {
    case 'daily':
        $whereClause = "WHERE DATE(qr.created_at) = CURDATE()";
        break;
    case 'weekly':
        $whereClause = "WHERE YEARWEEK(qr.created_at, 1) = YEARWEEK(CURDATE(), 1)";
        break;
    case 'monthly':
        $whereClause = "WHERE YEAR(qr.created_at) = YEAR(CURDATE()) AND MONTH(qr.created_at) = MONTH(CURDATE())";
        break;
    case 'yearly':
        $whereClause = "WHERE YEAR(qr.created_at) = YEAR(CURDATE())";
        break;
    case 'overall':
    default:
        // No time filter for overall.
        $whereClause = "";
        break;
}

// Query to get the average percentage (score/total_questions * 100) for each user from the quiz_results table.
$sql = "SELECT u.username, AVG((qr.score/qr.total_questions)*100) AS avg_percentage
        FROM quiz_results AS qr
        JOIN users u ON qr.user_id = u.id
        $whereClause
        GROUP BY u.id, u.username
        ORDER BY avg_percentage DESC
        LIMIT 10";

$stmt = $pdo->query($sql);
$leaderboard = $stmt->fetchAll();
?>

<div class="container mt-5">
  <h2>Leaderboard</h2>
  <div class="mb-3">
    <a href="leaderboard.php?filter=daily" class="btn btn-outline-primary <?php echo ($filter == 'daily') ? 'active' : ''; ?>">Today</a>
    <a href="leaderboard.php?filter=weekly" class="btn btn-outline-primary <?php echo ($filter == 'weekly') ? 'active' : ''; ?>">This Week</a>
    <a href="leaderboard.php?filter=monthly" class="btn btn-outline-primary <?php echo ($filter == 'monthly') ? 'active' : ''; ?>">This Month</a>
    <a href="leaderboard.php?filter=yearly" class="btn btn-outline-primary <?php echo ($filter == 'yearly') ? 'active' : ''; ?>">This Year</a>
    <a href="leaderboard.php?filter=overall" class="btn btn-outline-primary <?php echo ($filter == 'overall') ? 'active' : ''; ?>">All Time</a>
  </div>
  
  <table class="table table-striped">
    <thead>
      <tr>
        <th>Rank</th>
        <th>Username</th>
        <th>Average Score (%)</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($leaderboard as $index => $user): ?>
        <tr>
          <td><?php echo $index + 1; ?></td>
          <td><?php echo htmlspecialchars($user['username']); ?></td>
          <td><?php echo number_format($user['avg_percentage'], 2); ?>%</td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<?php include 'includes/footer.php'; ?>
