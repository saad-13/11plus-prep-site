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

        // ==== START DIFFICULTY UPDATE LOGIC ====
        // Determine which key to use for difficulty tracking:
            $diffType = ($quiz_type === 'subject' && !empty($subject))
            ? $subject
            : $quiz_type;

        // 1) Fetch current difficulty level (if any).
        $stmtDiff = $pdo->prepare("
            SELECT difficulty_level
                FROM user_difficulty
            WHERE user_id = ?
                AND quiz_type = ?
        ");
        $stmtDiff->execute([$user_id, $diffType]);
        $currentLevel = (int)$stmtDiff->fetchColumn();

        // 2) If no record exists, insert at level 1.
        if ($currentLevel < 1) {
            $currentLevel = 1;
            $stmtInsert = $pdo->prepare("
                INSERT INTO user_difficulty
                    (user_id, quiz_type, difficulty_level)
                VALUES (?, ?, ?)
            ");
            $stmtInsert->execute([$user_id, $diffType, $currentLevel]);
        }

        // 3) If user scored ≥80% and level <10, increase by 1.
        if ($percentage >= 80 && $currentLevel < 10) {
            $newLevel = $currentLevel + 1;
            $stmtUpdate = $pdo->prepare("
                UPDATE user_difficulty
                    SET difficulty_level = ?
                WHERE user_id = ?
                    AND quiz_type = ?
            ");
            $stmtUpdate->execute([$newLevel, $user_id, $diffType]);
        }
        // ==== END DIFFICULTY UPDATE LOGIC ====

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
