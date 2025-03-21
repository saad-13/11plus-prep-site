<?php 
$currentPage = 'leaderboard';
include 'includes/auth_check.php';
include 'includes/header.php';
include 'includes/db.php';

// Get the time filter from the URL, defaulting to overall if not set.
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'overall';
// Get the quiz type filter from the URL, defaulting to 'ai' if not set.
$qtype = isset($_GET['qtype']) ? $_GET['qtype'] : 'ai';

// Define the game types (from the game_scores table)
$gameTypes = ['verbal-challenge','math-blitz','logical-reasoning','pattern-recognition'];

if (in_array($qtype, $gameTypes)) {
    // Query from game_scores table.
    $sql = "SELECT u.username, u.profile_picture, MAX(gs.score) AS best_score
            FROM game_scores gs
            JOIN users u ON gs.user_id = u.id
            WHERE gs.game = ?";
    $params = [$qtype];
    
    // Add time-based conditions using gs.created_at.
    switch($filter) {
        case 'daily':
            $sql .= " AND DATE(gs.created_at) = CURDATE()";
            break;
        case 'weekly':
            $sql .= " AND YEARWEEK(gs.created_at, 1) = YEARWEEK(CURDATE(), 1)";
            break;
        case 'monthly':
            $sql .= " AND YEAR(gs.created_at) = YEAR(CURDATE()) AND MONTH(gs.created_at) = MONTH(CURDATE())";
            break;
        case 'yearly':
            $sql .= " AND YEAR(gs.created_at) = YEAR(CURDATE())";
            break;
        case 'overall':
        default:
            break;
    }
    
    $sql .= " GROUP BY u.id, u.username, u.profile_picture
              ORDER BY best_score DESC
              LIMIT 10";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $leaderboard = $stmt->fetchAll();
    
} else {
    // Otherwise, query from quiz_results table.
    $whereClause = "WHERE 1";  // no user_id filter for global leaderboard
    $params = [];
    
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
    
    if ($qtype == 'ai' || $qtype == 'mock') {
        $whereClause .= " AND qr.quiz_type = ?";
        $params[] = $qtype;
    } elseif (in_array($qtype, ['mathematics','english','verbal','nonverbal'])) {
        $whereClause .= " AND qr.quiz_type = 'subject' AND qr.subject = ?";
        $params[] = $qtype;
    }
    
    $sql = "SELECT u.username, u.profile_picture, MAX((qr.score/qr.total_questions)*100) AS best_percentage
            FROM quiz_results AS qr
            JOIN users u ON qr.user_id = u.id
            $whereClause
            GROUP BY u.id, u.username, u.profile_picture
            ORDER BY best_percentage DESC
            LIMIT 10";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $leaderboard = $stmt->fetchAll();
}

// If the leaderboard is not empty and the top user is the current user, award "Top of the Class".
if (!empty($leaderboard) && isset($leaderboard[0]['username']) && $leaderboard[0]['username'] === $_SESSION['username']) {
    awardAchievement($pdo, $_SESSION['user_id'], "Top of the Class");
}

/** Award an achievement to the user if not already awarded. */
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
<div class="wrapper">
<div class="content">
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
  
  <!-- Quiz Type Dropdown -->
  <div class="mb-3">
    <label for="quizTypeSelect" class="form-label">Select Quiz/Game Type:</label>
    <select id="quizTypeSelect" class="form-select">
      <option value="ai" <?php if($qtype=='ai') echo 'selected'; ?>>AI Practice Tests</option>
      <option value="mock" <?php if($qtype=='mock') echo 'selected'; ?>>Mock Exams</option>
      <option value="mathematics" <?php if($qtype=='mathematics') echo 'selected'; ?>>Mathematics</option>
      <option value="english" <?php if($qtype=='english') echo 'selected'; ?>>English</option>
      <option value="verbal" <?php if($qtype=='verbal') echo 'selected'; ?>>Verbal</option>
      <option value="nonverbal" <?php if($qtype=='nonverbal') echo 'selected'; ?>>Non-Verbal</option>
      <option value="verbal-challenge" <?php if($qtype=='verbal-challenge') echo 'selected'; ?>>Verbal Challenge</option>
      <option value="math-blitz" <?php if($qtype=='math-blitz') echo 'selected'; ?>>Math Blitz</option>
      <option value="logical-reasoning" <?php if($qtype=='logical-reasoning') echo 'selected'; ?>>Logical Reasoning</option>
      <option value="pattern-recognition" <?php if($qtype=='pattern-recognition') echo 'selected'; ?>>Pattern Recognition</option>
    </select>
  </div>
  
  <table class="table table-striped">
    <thead>
      <tr>
        <th>Rank</th>
        <th>Profile</th>
        <th>Username</th>
        <?php if (in_array($qtype, $gameTypes)) : ?>
          <th>Highest Score</th>
        <?php else: ?>
          <th>Highest Score (%)</th>
        <?php endif; ?>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($leaderboard as $index => $user): ?>
        <tr>
          <td><?php echo $index + 1; ?></td>
          <td>
            <img src="images/profile/<?php echo htmlspecialchars($user['profile_picture'] ? $user['profile_picture'] : 'default.png'); ?>" 
                 alt="Profile Picture" width="40" height="40" style="border-radius:50%;">
          </td>
          <td><?php echo htmlspecialchars($user['username']); ?></td>
          <td>
            <?php 
              if (in_array($qtype, $gameTypes)) {
                  // For game leaderboard, display the raw score.
                  echo htmlspecialchars($user['best_score']);
              } else {
                  // Otherwise, display the percentage.
                  echo number_format($user['best_percentage'], 2) . '%';
              }
            ?>
          </td>
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

</div>
<?php include 'includes/footer.php'; ?>
</div>