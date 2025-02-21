<?php
//session_start();
include 'includes/auth_check.php';
include 'includes/header.php';
include 'includes/db.php';

// Get the filter from the URL, default to 'week' if not provided.
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'week';

// Determine the start date based on the filter.
switch ($filter) {
    case 'week':
        $startDate = date('Y-m-d H:i:s', strtotime('-1 week'));
        $title = "Past Week";
        break;
    case 'month':
        $startDate = date('Y-m-d H:i:s', strtotime('-1 month'));
        $title = "Past Month";
        break;
    case 'quarter':
        $startDate = date('Y-m-d H:i:s', strtotime('-3 months'));
        $title = "Past Quarter";
        break;
    case 'year':
        $startDate = date('Y-m-d H:i:s', strtotime('-1 year'));
        $title = "Past Year";
        break;
    default:
        $startDate = date('Y-m-d H:i:s', strtotime('-1 week'));
        $title = "Past Week";
}
$endDate = date('Y-m-d H:i:s');

// Fetch quiz results for the current user in the selected time range.
$stmt = $pdo->prepare("SELECT * FROM quiz_results WHERE user_id = ? AND created_at BETWEEN ? AND ? ORDER BY created_at ASC");
$stmt->execute([$_SESSION['user_id'], $startDate, $endDate]);
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate analytics.
$totalTests = count($results);
$sumPercentage = 0;
$dates = [];
$percentages = [];
foreach ($results as $r) {
    $percentage = ($r['total_questions'] > 0) ? round(($r['score'] / $r['total_questions']) * 100, 2) : 0;
    $sumPercentage += $percentage;
    $dates[] = date('M d', strtotime($r['created_at']));
    $percentages[] = $percentage;
}
$avgPercentage = $totalTests > 0 ? round($sumPercentage / $totalTests, 2) : 0;
?>

<div class="container mt-5">
    <h2>Progress Reports</h2>
    <p class="fst-italic">Overview of your progress for the <?php echo htmlspecialchars($title); ?>.</p>
    
    <!-- Filter Buttons -->
    <div class="mb-3">
        <a href="progress_reports.php?filter=week" class="btn btn-outline-primary <?php echo ($filter=='week') ? 'active' : ''; ?>">Past Week</a>
        <a href="progress_reports.php?filter=month" class="btn btn-outline-primary <?php echo ($filter=='month') ? 'active' : ''; ?>">Past Month</a>
        <a href="progress_reports.php?filter=quarter" class="btn btn-outline-primary <?php echo ($filter=='quarter') ? 'active' : ''; ?>">Past Quarter</a>
        <a href="progress_reports.php?filter=year" class="btn btn-outline-primary <?php echo ($filter=='year') ? 'active' : ''; ?>">Past Year</a>
    </div>
    
    <!-- Summary Analytics Card -->
    <div class="card mb-4">
        <div class="card-body">
            <h4>Summary</h4>
            <p><strong>Total Tests Taken:</strong> <?php echo $totalTests; ?></p>
            <p><strong>Average Score:</strong> <?php echo $avgPercentage; ?>%</p>
        </div>
    </div>
    
    <!-- Progress Over Time Chart -->
    <div class="card mb-4">
        <div class="card-header">Progress Over Time</div>
        <div class="card-body">
            <canvas id="progressChart" width="400" height="200"></canvas>
        </div>
    </div>
    
    <!-- Detailed Test Results Table -->
    <div class="card mb-4">
        <div class="card-header">Detailed Test Results</div>
        <div class="card-body">
            <?php if ($totalTests > 0): ?>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Score (%)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($results as $r): 
                        $percentage = ($r['total_questions'] > 0) ? round(($r['score'] / $r['total_questions']) * 100, 2) : 0;
                    ?>
                    <tr>
                        <td><?php echo date('M d, Y', strtotime($r['created_at'])); ?></td>
                        <td><?php echo $percentage; ?>%</td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
            <p>No test results for this period.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Chart.js for rendering the progress chart -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
var ctx = document.getElementById('progressChart').getContext('2d');
var progressChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: <?php echo json_encode($dates); ?>,
        datasets: [{
            label: 'Score (%)',
            data: <?php echo json_encode($percentages); ?>,
            borderColor: 'orange',
            borderWidth: 2,
            fill: false
        }]
    },
    options: {
        scales: {
            y: {
                min: 0,
                max: 100,
                ticks: {
                    stepSize: 10
                }
            }
        },
        plugins: {
            legend: {
                display: false
            }
        }
    }
});
</script>

<?php include 'includes/footer.php'; ?>
