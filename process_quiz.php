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

// Read the JSON.
$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!$data) {
    echo json_encode(['success' => false, 'message' => 'Invalid input.']);
    exit;
}

// Extract data.
$user_id = $_SESSION['user_id'];
$quiz_type = isset($data['quiz_type']) ? $data['quiz_type'] : '';
$subject = isset($data['subject']) ? $data['subject'] : null; // For subject tests.
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
        
        // Award "Mastery" if full marks.
        if ($percentage == 100) {
            awardAchievement($pdo, $user_id, "Mastery");
        }
        
        // For subject tests, award "Subject Master: [Subject]" if user gets full marks.
        if ($quiz_type === 'subject' && !empty($subject) && $percentage == 100) {
            awardAchievement($pdo, $user_id, "Subject Master: " . ucfirst($subject));
        }
    }
    
    echo json_encode(['success' => true, 'result_id' => $result_id]);
} else {
    echo json_encode(['success' => false, 'message' => 'Database error.']);
}

/**
 * Award an achievement to the user if not already awarded.
 */
function awardAchievement($pdo, $user_id, $achievement) {
    // Fetch the current achievements (badges) for the user.
    $stmtBadge = $pdo->prepare("SELECT badges FROM users WHERE id = ?");
    $stmtBadge->execute([$user_id]);
    $badges = $stmtBadge->fetchColumn();
    $badgesArr = $badges ? json_decode($badges, true) : [];
    
    // If the achievement isn't already in the array, add it.
    if (!in_array($achievement, $badgesArr)) {
        $badgesArr[] = $achievement;
        $newBadges = json_encode($badgesArr);
        $stmtUpdateBadge = $pdo->prepare("UPDATE users SET badges = ? WHERE id = ?");
        $stmtUpdateBadge->execute([$newBadges, $user_id]);
    }
}
?>
