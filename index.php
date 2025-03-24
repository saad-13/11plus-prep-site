<?php 
$currentPage = 'home';
include 'includes/auth_check.php';

// Check if user is logged in; if not, redirect to login.
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

include 'includes/header.php';
include 'includes/db.php';

// Fetch the latest quiz result for the logged-in user.
$stmtLatest = $pdo->prepare("SELECT * FROM quiz_results WHERE user_id = ? ORDER BY created_at DESC LIMIT 1");
$stmtLatest->execute([$_SESSION['user_id']]);
$latestQuiz = $stmtLatest->fetch();

if($latestQuiz) {
    $score = $latestQuiz['score'];
    $total = $latestQuiz['total_questions'];
    // Calculate percentage. Protect against division by zero.
    $percentage = ($total > 0) ? round(($score / $total) * 100) : 0;
    
    // Determine the quiz type. For subject-specific quizzes, use the subject name.
    if($latestQuiz['quiz_type'] == 'subject' && !empty($latestQuiz['subject'])) {
        $quizType = ucfirst($latestQuiz['subject']) . " Practice";
    } else {
        $quizType = ucfirst($latestQuiz['quiz_type']);
    }
} else {
    $quizType = "N/A";
    $percentage = 0;
}

// Determine graph filter; default to "all".
$graphFilter = isset($_GET['graphFilter']) ? $_GET['graphFilter'] : 'all';

// Build query for graph data based on $graphFilter.
if($graphFilter == 'all'){
    $stmtGraph = $pdo->prepare("SELECT created_at, score, total_questions FROM quiz_results WHERE user_id = ? ORDER BY created_at ASC");
    $stmtGraph->execute([$_SESSION['user_id']]);
} elseif($graphFilter == 'practice'){
    $stmtGraph = $pdo->prepare("SELECT created_at, score, total_questions FROM quiz_results WHERE user_id = ? AND quiz_type IN ('mock','ai') ORDER BY created_at ASC");
    $stmtGraph->execute([$_SESSION['user_id']]);
} elseif(in_array($graphFilter, ['mathematics','english','verbal','nonverbal'])){
    $stmtGraph = $pdo->prepare("SELECT created_at, score, total_questions FROM quiz_results WHERE user_id = ? AND quiz_type = 'subject' AND subject = ? ORDER BY created_at ASC");
    $stmtGraph->execute([$_SESSION['user_id'], $graphFilter]);
} else {
    // Default to all.
    $stmtGraph = $pdo->prepare("SELECT created_at, score, total_questions FROM quiz_results WHERE user_id = ? ORDER BY created_at ASC");
    $stmtGraph->execute([$_SESSION['user_id']]);
}
$quizResults = $stmtGraph->fetchAll(PDO::FETCH_ASSOC);

$chartLabels = [];
$chartData = [];
foreach ($quizResults as $result) {
    // Format the date label (e.g., "Mar 15").
    $chartLabels[] = date('M d', strtotime($result['created_at']));
    // Compute the percentage for each quiz.
    $perc = ($result['total_questions'] > 0) ? round(($result['score'] / $result['total_questions']) * 100) : 0;
    $chartData[] = $perc;
}
?>

<div class="wrapper">
  <div class="content">
    <!-- Main Container with some side spacing -->
    <div class="container mt-5">

      <!-- Welcome Message -->
      <div class="row">
        <div class="col-md-12">
          <h2 style="color: #F26419;">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
        </div>
      </div>

      <!-- Three Cards: AI Practice Tests, Educational Games, Latest Achievement -->
      <div class="row">
        <!-- AI Practice Tests Card -->
        <div class="col-md-4 mb-4">
          <a href="quiz.php?type=ai" class="text-decoration-none text-dark">
            <div class="card h-100">
              <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                  <h5 class="card-title fw-bold">AI Practice Tests</h5>
                  <img src="images/ai-icon.png" alt="AI Icon" style="width:40px; height:40px;">
                </div>
                <p class="card-text fst-italic">Engage with AI-driven tests.</p>
              </div>
            </div>
          </a>
        </div>
        <!-- Educational Games Card -->
        <div class="col-md-4 mb-4">
          <a href="games.php" class="text-decoration-none text-dark">
            <div class="card h-100">
              <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                  <h5 class="card-title fw-bold">Educational Games</h5>
                  <img src="images/games-icon.png" alt="Games Icon" style="width:40px; height:40px;">
                </div>
                <p class="card-text fst-italic">Learn through interactive play.</p>
              </div>
            </div>
          </a>
        </div>
        <!-- Latest Achievement Card -->
        <div class="col-md-4 mb-4">
          <a href="profile.php" class="text-decoration-none text-dark">
            <div class="card h-100">
              <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                  <h5 class="card-title fw-bold">Progress & Achievements</h5>
                  <img src="images/achievement-icon.png" alt="Achievement Icon" style="width:40px; height:40px;">
                </div>
                <p class="card-text fst-italic">See your most recent achievement.</p>
              </div>
            </div>
          </a>
        </div>
      </div>

      <!-- Latest Quiz Result Card -->
      <div class="row">
        <div class="col-md-12 mb-4">
          <div class="card">
            <div class="card-body">
              <?php if($latestQuiz): ?>
                <div class="d-flex justify-content-between align-items-center">
                  <div>
                    <h5 class="card-title">Latest Quiz Result</h5>
                    <p class="card-text"><?php echo htmlspecialchars($quizType); ?> - <?php echo $percentage; ?>% Achieved</p>
                  </div>
                  <div>
                    <h3 style="color: #F26419;"><?php echo $percentage; ?>%</h3>
                  </div>
                </div>
                <div class="progress mt-3" style="height: 20px;">
                  <div class="progress-bar" role="progressbar" style="width: <?php echo $percentage; ?>%; background-color: #F26419;" aria-valuenow="<?php echo $percentage; ?>" aria-valuemin="0" aria-valuemax="100">
                    <?php echo $percentage; ?>%
                  </div>
                </div>
              <?php else: ?>
                <div class="d-flex justify-content-between align-items-center">
                  <div>
                    <h5 class="card-title">Latest Quiz Result</h5>
                    <p class="card-text">No quiz results yet.</p>
                  </div>
                </div>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>

      <!-- Dropdown Filter Above Progress Over Time Graph -->
      <div class="row mb-3">
        <div class="col-md-4">
          <label for="graphFilterSelect" class="form-label">Select Graph Data:</label>
          <select id="graphFilterSelect" class="form-select">
            <option value="all" <?php if($graphFilter=='all') echo 'selected'; ?>>All Tests</option>
            <option value="practice" <?php if($graphFilter=='practice') echo 'selected'; ?>>Practice Tests</option>
            <option value="mathematics" <?php if($graphFilter=='mathematics') echo 'selected'; ?>>Mathematics</option>
            <option value="english" <?php if($graphFilter=='english') echo 'selected'; ?>>English</option>
            <option value="verbal" <?php if($graphFilter=='verbal') echo 'selected'; ?>>Verbal</option>
            <option value="nonverbal" <?php if($graphFilter=='nonverbal') echo 'selected'; ?>>Non-Verbal</option>
          </select>
        </div>
      </div>

      <!-- Progress Over Time Card -->
      <div class="row">
        <div class="col-md-12 mb-4">
          <div class="card">
            <div class="card-header">Progress Over Time</div>
            <div class="card-body">
              <!-- canvas size: 400x180 -->
              <canvas id="progressChart" width="400" height="280"></canvas>
            </div>
          </div>
        </div>
      </div>

      <!-- Study Resources Card -->
      <div class="row">
        <div class="col-md-12 mb-4">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Study Resources</h5>
              <p class="card-text">Explore curated study materials and resources to boost your learning.</p>
              <a href="resources.php" class="btn btn-primary">View Resources</a>
            </div>
          </div>
        </div>
      </div>

    </div>

    <!-- Chart.js for Progress Over Time Graph -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
      // When user changes the drop down, reload the page with the selected filter.
      document.getElementById('graphFilterSelect').addEventListener('change', function() {
        var selected = this.value;
        window.location.href = "index.php?graphFilter=" + encodeURIComponent(selected);
      });

      // Using the PHP-generated arrays for labels and data.
      var chartLabels = <?php echo json_encode($chartLabels); ?>;
      var chartData = <?php echo json_encode($chartData); ?>;
      
      var ctx = document.getElementById('progressChart').getContext('2d');
      var progressChart = new Chart(ctx, {
        type: 'line',
        data: {
          labels: chartLabels,
          datasets: [{
            label: 'Quiz Score (%)',
            data: chartData,
            borderColor: 'orange',
            borderWidth: 2,
            fill: false,
          }]
        },
        options: {
          maintainAspectRatio: false,
          scales: {
            x: {
              title: {
                display: true,
                text: 'Date'
              }
            },
            y: {
              min: 0,
              max: 100,
              ticks: {
                stepSize: 10
              },
              title: {
                display: true,
                text: 'Score (%)'
              }
            }
          },
          plugins: {
            legend: {
              display: false
            },
            tooltip: {
              callbacks: {
                label: function(context) {
                  return context.parsed.y + '%';
                }
              }
            }
          }
        }
      });
    </script>
  </div>
    <?php include 'includes/footer.php'; ?>
</div>