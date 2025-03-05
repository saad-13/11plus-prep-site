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
        $titlePeriod = "Past Week";
        break;
    case 'month':
        $startDate = date('Y-m-d H:i:s', strtotime('-1 month'));
        $titlePeriod = "Past Month";
        break;
    case 'quarter':
        $startDate = date('Y-m-d H:i:s', strtotime('-3 months'));
        $titlePeriod = "Past Quarter";
        break;
    case 'year':
        $startDate = date('Y-m-d H:i:s', strtotime('-1 year'));
        $titlePeriod = "Past Year";
        break;
    default:
        $startDate = date('Y-m-d H:i:s', strtotime('-1 week'));
        $titlePeriod = "Past Week";
}
$endDate = date('Y-m-d H:i:s');

// ------------------------------
// Section 1: Mock Exams & Practice Papers
// ------------------------------
$stmtMock = $pdo->prepare("SELECT * FROM quiz_results 
                           WHERE user_id = ? 
                             AND quiz_type IN ('mock', 'ai')
                             AND created_at BETWEEN ? AND ? 
                           ORDER BY created_at ASC");
$stmtMock->execute([$_SESSION['user_id'], $startDate, $endDate]);
$mockResults = $stmtMock->fetchAll(PDO::FETCH_ASSOC);

$totalMockTests = count($mockResults);
$sumMockPercentage = 0;
$mockDates = [];
$mockPercentages = [];

foreach ($mockResults as $r) {
    $perc = ($r['total_questions'] > 0) ? round(($r['score'] / $r['total_questions']) * 100, 2) : 0;
    $sumMockPercentage += $perc;
    $mockDates[] = date('M d', strtotime($r['created_at']));
    $mockPercentages[] = $perc;
}
$avgMockPercentage = $totalMockTests > 0 ? round($sumMockPercentage / $totalMockTests, 2) : 0;
?>

<div class="container mt-5">
    <h2>Progress Reports</h2>
    <p class="fst-italic">Overview of your progress for the <?php echo htmlspecialchars($titlePeriod); ?>.</p>
    
    <!-- Filter Buttons -->
    <div class="mb-3">
        <a href="progress_reports.php?filter=week" class="btn btn-outline-primary <?php echo ($filter=='week') ? 'active' : ''; ?>">Past Week</a>
        <a href="progress_reports.php?filter=month" class="btn btn-outline-primary <?php echo ($filter=='month') ? 'active' : ''; ?>">Past Month</a>
        <a href="progress_reports.php?filter=quarter" class="btn btn-outline-primary <?php echo ($filter=='quarter') ? 'active' : ''; ?>">Past Quarter</a>
        <a href="progress_reports.php?filter=year" class="btn btn-outline-primary <?php echo ($filter=='year') ? 'active' : ''; ?>">Past Year</a>
    </div>
    
    <!-- Section 1: Mock Exams & Practice Papers -->
    <div class="card mb-4">
        <div class="card-header"><strong>Mock Exams & Practice Papers</strong></div>
        <div class="card-body">
            <h4>Summary</h4>
            <p><strong>Total Tests Taken:</strong> <?php echo $totalMockTests; ?></p>
            <p><strong>Average Score:</strong> <?php echo $avgMockPercentage; ?>%</p>
        </div>
    </div>
    
    <div class="card mb-4">
        <div class="card-header">Progress Over Time (Mock Exams & Practice Papers)</div>
        <div class="card-body">
            <canvas id="mockProgressChart" width="400" height="200"></canvas>
        </div>
    </div>
    
    <div class="card mb-4">
        <div class="card-header">Detailed Test Results (Mock Exams & Practice Papers)</div>
        <div class="card-body">
            <?php if ($totalMockTests > 0): ?>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Score (%)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($mockResults as $r): 
                        $perc = ($r['total_questions'] > 0) ? round(($r['score'] / $r['total_questions']) * 100, 2) : 0;
                    ?>
                    <tr>
                        <td><?php echo date('M d, Y', strtotime($r['created_at'])); ?></td>
                        <td><?php echo $perc; ?>%</td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
            <p>No test results for this period.</p>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Section 2: Subject Test Results -->
    <?php
    // Define an array of subjects for subject tests.
    $subjects = ['mathematics', 'english', 'verbal', 'nonverbal'];
    ?>
    <div class="card mb-4">
        <div class="card-header"><strong>Subject Test Results</strong></div>
        <div class="card-body">
            <?php foreach ($subjects as $subj): 
                // For subject tests, we consider only results with quiz_type 'subject'
                $stmtSub = $pdo->prepare("SELECT * FROM quiz_results 
                                          WHERE user_id = ? 
                                            AND quiz_type = 'subject'
                                            AND subject = ?
                                            AND created_at BETWEEN ? AND ?
                                          ORDER BY created_at ASC");
                $stmtSub->execute([$_SESSION['user_id'], $subj, $startDate, $endDate]);
                $subjResults = $stmtSub->fetchAll(PDO::FETCH_ASSOC);
                $totalSubjTests = count($subjResults);
                $sumSubjPercentage = 0;
                $subjDates = [];
                $subjPercentages = [];
                foreach ($subjResults as $r) {
                    $p = ($r['total_questions'] > 0) ? round(($r['score'] / $r['total_questions']) * 100, 2) : 0;
                    $sumSubjPercentage += $p;
                    $subjDates[] = date('M d', strtotime($r['created_at']));
                    $subjPercentages[] = $p;
                }
                $avgSubjPercentage = $totalSubjTests > 0 ? round($sumSubjPercentage / $totalSubjTests, 2) : 0;
            ?>
            <h4><?php echo ucfirst($subj); ?> Test Results</h4>
            <p><strong>Total Tests:</strong> <?php echo $totalSubjTests; ?>, <strong>Average Score:</strong> <?php echo $avgSubjPercentage; ?>%</p>
            <?php if ($totalSubjTests > 0): ?>
            <div class="mb-3">
                <canvas id="chart_<?php echo $subj; ?>" width="400" height="200"></canvas>
            </div>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Score (%)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($subjResults as $r): 
                        $p = ($r['total_questions'] > 0) ? round(($r['score'] / $r['total_questions']) * 100, 2) : 0;
                    ?>
                    <tr>
                        <td><?php echo date('M d, Y', strtotime($r['created_at'])); ?></td>
                        <td><?php echo $p; ?>%</td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <hr>
            <?php else: ?>
                <p>No <?php echo ucfirst($subj); ?> test results for this period.</p>
                <hr>
            <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>
    
</div>

<!-- Chart.js for rendering charts -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Chart for Mock Exams & Practice Papers
var ctxMock = document.getElementById('mockProgressChart').getContext('2d');
var mockProgressChart = new Chart(ctxMock, {
    type: 'line',
    data: {
        labels: <?php echo json_encode($mockDates); ?>,
        datasets: [{
            label: 'Score (%)',
            data: <?php echo json_encode($mockPercentages); ?>,
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
                ticks: { stepSize: 10 }
            }
        },
        plugins: { legend: { display: false } }
    }
});

// Charts for each subject test.
<?php foreach ($subjects as $subj): 
    // Prepare data for each subject.
    $stmtSub = $pdo->prepare("SELECT * FROM quiz_results 
                              WHERE user_id = ? 
                                AND quiz_type = 'subject'
                                AND subject = ?
                                AND created_at BETWEEN ? AND ?
                              ORDER BY created_at ASC");
    $stmtSub->execute([$_SESSION['user_id'], $subj, $startDate, $endDate]);
    $subjResults = $stmtSub->fetchAll(PDO::FETCH_ASSOC);
    $subjDates = [];
    $subjPercentages = [];
    foreach ($subjResults as $r) {
        $p = ($r['total_questions'] > 0) ? round(($r['score'] / $r['total_questions']) * 100, 2) : 0;
        $subjDates[] = date('M d', strtotime($r['created_at']));
        $subjPercentages[] = $p;
    }
    $chartId = "chart_" . $subj;
    ?>
var ctx_<?php echo $subj; ?> = document.getElementById('<?php echo $chartId; ?>').getContext('2d');
var chart_<?php echo $subj; ?> = new Chart(ctx_<?php echo $subj; ?>, {
    type: 'line',
    data: {
        labels: <?php echo json_encode($subjDates); ?>,
        datasets: [{
            label: 'Score (%)',
            data: <?php echo json_encode($subjPercentages); ?>,
            borderColor: 'blue',
            borderWidth: 2,
            fill: false
        }]
    },
    options: {
        scales: {
            y: {
                min: 0,
                max: 100,
                ticks: { stepSize: 10 }
            }
        },
        plugins: { legend: { display: false } }
    }
});
<?php endforeach; ?>
</script>

<?php include 'includes/footer.php'; ?>
