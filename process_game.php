<?php
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

if (!$data || !isset($data['game']) || !isset($data['score'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid input.']);
    exit;
}

$user_id = $_SESSION['user_id'];
$game = $data['game'];
$score = (int)$data['score'];

// Insert the game score into the database.
$stmt = $pdo->prepare("INSERT INTO game_scores (user_id, game, score) VALUES (?, ?, ?)");
$result = $stmt->execute([$user_id, $game, $score]);

if ($result) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Database error.']);
}
?>
