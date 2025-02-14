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
$stmt = $pdo->prepare("SELECT * FROM quiz_results WHERE user_id = ? ORDER BY created_at DESC LIMIT 1");
$stmt->execute([$_SESSION['user_id']]);
$latestQuiz = $stmt->fetch();

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
?>

<!-- Main Container with some side spacing -->
<div class="container mt-5">

  <!-- Welcome Message -->
  <div class="row">
    <div class="col-md-12">
      <h2 class="mb-4">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
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
              <h5 class="card-title fw-bold">Latest Achievement</h5>
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
                <h3 class="text-primary"><?php echo $percentage; ?>%</h3>
              </div>
            </div>
            <div class="progress mt-3" style="height: 20px;">
              <div class="progress-bar" role="progressbar" style="width: <?php echo $percentage; ?>%;" aria-valuenow="<?php echo $percentage; ?>" aria-valuemin="0" aria-valuemax="100">
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

  <!-- Progress Over Time Card -->
  <div class="row">
    <div class="col-md-12 mb-4">
      <div class="card">
        <div class="card-header">
          Progress Over Time
        </div>
        <div class="card-body">
          <!-- Chart.js canvas for the progress graph -->
          <canvas id="progressChart" width="400" height="200"></canvas>
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
// Sample data for the graph; in a real application, this data could be fetched via AJAX.
var ctx = document.getElementById('progressChart').getContext('2d');
var chartData = {
  labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
  datasets: [{
    label: 'Quiz Score (%)',
    data: [70, 80, 75, 85],
    borderColor: 'blue',
    borderWidth: 2,
    fill: false,
  }]
};

var progressChart = new Chart(ctx, {
  type: 'line',
  data: chartData,
  options: {
    scales: {
      y: {
        min: 0,
        max: 100,
        ticks: {
          stepSize: 10
        },
        grid: {
          drawBorder: false,
          color: function(context) {
            // Draw dotted threshold lines at specific values.
            if (context.tick.value === 50) {
              return 'orange';
            } else if (context.tick.value === 75) {
              return 'yellow';
            } else if (context.tick.value === 90) {
              return 'green';
            }
            return '#e9ecef';
          },
          borderDash: [2, 2]
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
<?php include 'includes/footer.php'; ?>
