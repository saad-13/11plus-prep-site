<?php
// quiz_results.php
session_start();
include 'includes/db.php';

// Check if user is logged in.
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Check if result_id is provided.
if (!isset($_GET['result_id'])) {
    echo "No result specified.";
    exit;
}

$result_id = $_GET['result_id'];
$user_id = $_SESSION['user_id'];

// Fetch the quiz result record ensuring it belongs to the logged-in user.
$stmt = $pdo->prepare("SELECT * FROM quiz_results WHERE id = ? AND user_id = ?");
$stmt->execute([$result_id, $user_id]);
$result = $stmt->fetch();

if (!$result) {
    echo "Result not found or you do not have permission to view this result.";
    exit;
}

// Decode the JSON details stored in the 'details' column.
$details = json_decode($result['details'], true);
?>
  
<?php include 'includes/header.php'; ?>

<div class="wrapper">
  <div class="content">
    <div class="container mt-4">
      <div class="d-flex align-items-center mb-3">
        <a href="practice.php" class="btn btn-secondary me-3">Back</a>
        <h2 class="mb-0">Quiz Results</h2>
      </div>
      <p class="fst-italic">Review your answers below. Correct answers are marked accordingly.</p>
      
      <!-- Quiz Summary -->
      <div class="card mb-3">
        <div class="card-body">
          <h5 class="card-title">Score: <?php echo htmlspecialchars($result['score']); ?> / <?php echo htmlspecialchars($result['total_questions']); ?></h5>
          <p class="card-text">
            Quiz Type: <?php echo htmlspecialchars($result['quiz_type']); ?> 
            <?php if (!empty($result['subject'])): ?>
              - <?php echo htmlspecialchars(ucfirst($result['subject'])); ?>
            <?php endif; ?>
          </p>
          <p class="card-text">
            <small class="text-muted">Taken on <?php echo htmlspecialchars($result['created_at']); ?></small>
          </p>
        </div>
      </div>
      
      <!-- Detailed Question Review -->
      <?php if (!empty($details) && is_array($details)): ?>
        <?php foreach ($details as $index => $question): ?>
          <div class="card mb-3">
            <div class="card-body">
              <h5 class="card-title">Question <?php echo $index + 1; ?>: <?php echo htmlspecialchars($question['question_text']); ?></h5>
              <p class="card-text">
                <strong>Your Answer:</strong> <?php echo htmlspecialchars($question['selected_answer'] ? $question['selected_answer'] : 'No answer'); ?><br>
                <strong>Correct Answer:</strong> <?php echo htmlspecialchars($question['correct_answer']); ?><br>
                <strong>Status:</strong> <?php echo $question['is_correct'] ? '<span class="text-success">Correct</span>' : '<span class="text-danger">Incorrect</span>'; ?>
              </p>
              <p class="card-text">
                <small class="fst-italic"><?php echo htmlspecialchars($question['explanation']); ?></small>
              </p>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p>No question details available.</p>
      <?php endif; ?>
    </div>
  </div>
  <?php include 'includes/footer.php'; ?>
</div>