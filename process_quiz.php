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
$subject = isset($data['subject']) ? $data['subject'] : null;
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
        
        // If the user scored 80% or higher, update their difficulty level (max level 10).
        if ($percentage >= 80) {
            $stmt2 = $pdo->prepare("SELECT difficulty_level FROM users WHERE id = ?");
            $stmt2->execute([$user_id]);
            $row = $stmt2->fetch(PDO::FETCH_ASSOC);
            if ($row && isset($row['difficulty_level']) && $row['difficulty_level'] < 10) {
                $newDifficulty = $row['difficulty_level'] + 1;
                $stmt3 = $pdo->prepare("UPDATE users SET difficulty_level = ? WHERE id = ?");
                $stmt3->execute([$newDifficulty, $user_id]);
            }
        }
    }
    
    echo json_encode(['success' => true, 'result_id' => $result_id]);
} else {
    echo json_encode(['success' => false, 'message' => 'Database error.']);
}
?>
