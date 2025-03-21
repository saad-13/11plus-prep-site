<?php 
$currentPage = 'leaderboard';
include 'includes/auth_check.php';
include 'includes/header.php';
include 'includes/db.php';

// Get the time filter from the URL, and deafault to overall if not set.
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'overall';
// Get the quiz type filter from the URL, and default to 'ai' if not set.
$qtype = isset($_GET['qtype']) ? $_GET['qtype'] : 'ai';

// Build the time-based WHERE clause.
$whereClause = "WHERE qr.user_id = ?";
$params = [$_SESSION['user_id']];

switch($filter) {
    case 'daily':
        $whereClause .= " AND DATE(qr.created_at) = CURDATE()";
        break;
    case 'weekly':
        $whereClause .= " AND YEARWEEK(qr.created_at, 1) = YEARWEEK(CURDATE(), 1)";
        break;
    case 'monthly':
        $whereClause .= " AND YEAR(qr.created_at) = YEAR(CURDATE()) AND MONTH(qr.created_at) = MONTH(CURDATE())";
        break;
    case 'yearly':
        $whereClause .= " AND YEAR(qr.created_at) = YEAR(CURDATE())";
        break;
    case 'overall':
    default:
        break;
}

// Add quiz type condition.
if ($qtype == 'ai' || $qtype == 'mock') {
    $whereClause .= " AND qr.quiz_type = ?";
    $params[] = $qtype;
} elseif (in_array($qtype, ['mathematics','english','verbal','nonverbal'])) {
    $whereClause .= " AND qr.quiz_type = 'subject' AND qr.subject = ?";
    $params[] = $qtype;
}

// Build the query: select each user's highest percentage score for the selected quiz type.
$sql = "SELECT u.username, MAX((qr.score/qr.total_questions)*100) AS best_percentage
        FROM quiz_results AS qr
        JOIN users u ON qr.user_id = u.id
        $whereClause
        GROUP BY u.id, u.username
        ORDER BY best_percentage DESC
        LIMIT 10";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$leaderboard = $stmt->fetchAll();

// If the leaderboard is not empty and the top user is the current user, award "Top of the Class".
if (!empty($leaderboard) && $leaderboard[0]['username'] === $_SESSION['username']) {
    awardAchievement($pdo, $_SESSION['user_id'], "Top of the Class");
}

/**
 * Award an achievement to the user if not already awarded.
 * (You can place this function in a shared include file.)
 */
function awardAchievement($pdo, $user_id, $achievement) {
    $stmtBadge = $pdo->prepare("SELECT badges FROM users WHERE id = ?");
    $stmtBadge->execute([$user_id]);
    $badges = $stmtBadge->fetchColumn();
    $badgesArr = $badges ? json_decode($badges, true) : [];
    if (!in_array($achievement, $badgesArr)) {
        $badgesArr[] = $achievement;
        $newBadges = json_encode($badgesArr);
        $stmtUpdateBadge = $pdo->prepare("UPDATE users SET badges = ? WHERE id = ?");
        $stmtUpdateBadge->execute([$newBadges, $user_id]);
    }
}
?>

<div class="container mt-5">
  <h2 style="color: #F26419;">Leaderboard</h2>
  <p>Check out the top performers on the leaderboard!</p>
  
  <!-- Time Period Filter Buttons -->
  <div class="mb-3">
    <a href="leaderboard.php?filter=daily&qtype=<?php echo urlencode($qtype); ?>" class="btn btn-outline-primary <?php echo ($filter == 'daily') ? 'active' : ''; ?>">Today</a>
    <a href="leaderboard.php?filter=weekly&qtype=<?php echo urlencode($qtype); ?>" class="btn btn-outline-primary <?php echo ($filter == 'weekly') ? 'active' : ''; ?>">This Week</a>
    <a href="leaderboard.php?filter=monthly&qtype=<?php echo urlencode($qtype); ?>" class="btn btn-outline-primary <?php echo ($filter == 'monthly') ? 'active' : ''; ?>">This Month</a>
    <a href="leaderboard.php?filter=yearly&qtype=<?php echo urlencode($qtype); ?>" class="btn btn-outline-primary <?php echo ($filter == 'yearly') ? 'active' : ''; ?>">This Year</a>
    <a href="leaderboard.php?filter=overall&qtype=<?php echo urlencode($qtype); ?>" class="btn btn-outline-primary <?php echo ($filter == 'overall') ? 'active' : ''; ?>">All Time</a>
  </div>
  
  <!-- Quiz Type Dropdown (No "all" option) -->
  <div class="mb-3">
    <label for="quizTypeSelect" class="form-label">Select Quiz Type:</label>
    <select id="quizTypeSelect" class="form-select">
      <option value="ai" <?php if($qtype=='ai') echo 'selected'; ?>>AI Practice Tests</option>
      <option value="mock" <?php if($qtype=='mock') echo 'selected'; ?>>Mock Exams</option>
      <option value="mathematics" <?php if($qtype=='mathematics') echo 'selected'; ?>>Mathematics</option>
      <option value="english" <?php if($qtype=='english') echo 'selected'; ?>>English</option>
      <option value="verbal" <?php if($qtype=='verbal') echo 'selected'; ?>>Verbal</option>
      <option value="nonverbal" <?php if($qtype=='nonverbal') echo 'selected'; ?>>Non-Verbal</option>
    </select>
  </div>
  
  <table class="table table-striped">
    <thead>
      <tr>
        <th>Rank</th>
        <th>Username</th>
        <th>Highest Score (%)</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($leaderboard as $index => $user): ?>
        <tr>
          <td><?php echo $index + 1; ?></td>
          <td><?php echo htmlspecialchars($user['username']); ?></td>
          <td><?php echo number_format($user['best_percentage'], 2); ?>%</td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<script>
// When the user changes the quiz type in the dropdown, reload the page with the new parameter.
document.getElementById('quizTypeSelect').addEventListener('change', function() {
    var selected = this.value;
    var currentFilter = "<?php echo $filter; ?>";
    window.location.href = "leaderboard.php?filter=" + encodeURIComponent(currentFilter) + "&qtype=" + encodeURIComponent(selected);
});
</script>

<?php include 'includes/footer.php'; ?>
