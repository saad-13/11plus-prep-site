<?php
// process_quiz.php
session_start();
header('Content-Type: application/json');
include 'includes/db.php';

// Check that the user is logged in.
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated.']);
    exit;
}

// Read the JSON payload.
$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!$data) {
    echo json_encode(['success' => false, 'message' => 'Invalid input.']);
    exit;
}

// Extract payload data.
$user_id = $_SESSION['user_id'];
$quiz_type = isset($data['quiz_type']) ? $data['quiz_type'] : '';
$subject = isset($data['subject']) ? $data['subject'] : null; // Use this as quiz_type for subject quizzes.
$score = isset($data['score']) ? (int)$data['score'] : 0;
$total_questions = isset($data['total_questions']) ? (int)$data['total_questions'] : 0;
$details = json_encode($data['details']);  // Store details as JSON

// Insert quiz result into the database.
$stmt = $pdo->prepare("INSERT INTO quiz_results (user_id, quiz_type, subject, score, total_questions, details) VALUES (?, ?, ?, ?, ?, ?)");
$result = $stmt->execute([$user_id, $quiz_type, $subject, $score, $total_questions, $details]);

if ($result) {
    // Get the last inserted id.
    $result_id = $pdo->lastInsertId();

    // Calculate the percentage score.
    if ($total_questions > 0) {
        $percentage = ($score / $total_questions) * 100;
        
        // Use the subject (if available) as the quiz_type for difficulty; otherwise, use quiz_type.
        $currentQuizType = $subject ? $subject : $quiz_type;
        
        // Retrieve the current difficulty level for this user and quiz type from the user_difficulty table.
        $stmt2 = $pdo->prepare("SELECT difficulty_level FROM user_difficulty WHERE user_id = ? AND quiz_type = ?");
        $stmt2->execute([$user_id, $currentQuizType]);
        $row = $stmt2->fetch(PDO::FETCH_ASSOC);
        
        // If no row exists, create one.
        if (!$row) {
            $stmtInsert = $pdo->prepare("INSERT INTO user_difficulty (user_id, quiz_type, difficulty_level) VALUES (?, ?, ?)");
            $stmtInsert->execute([$user_id, $currentQuizType, 1]);
            $currentDifficulty = 1;
        } else {
            $currentDifficulty = $row['difficulty_level'];
        }
        
        // If the user scored 80% or higher and their difficulty level is less than 10, increase it.
        if ($percentage >= 80 && $currentDifficulty < 10) {
            $newDifficulty = $currentDifficulty + 1;
            $stmt3 = $pdo->prepare("UPDATE user_difficulty SET difficulty_level = ? WHERE user_id = ? AND quiz_type = ?");
            $stmt3->execute([$newDifficulty, $user_id, $currentQuizType]);
        }
    }
    
    echo json_encode(['success' => true, 'result_id' => $result_id]);
} else {
    echo json_encode(['success' => false, 'message' => 'Database error.']);
}
?>
