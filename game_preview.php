<?php
$currentPage = 'games';
include 'includes/auth_check.php';
include 'includes/header.php';
include 'includes/db.php';

// Get the game slug from the query string.
$gameSlug = isset($_GET['game']) ? $_GET['game'] : '';
if (empty($gameSlug)) {
    header("Location: games.php");
    exit;
}

// Game metadata.
$games = [
    'verbal-challenge' => [
         'title' => 'Verbal Challenge',
         'description' => 'Enhance your language and vocabulary skills with fun word puzzles.',
         'image' => 'images/verbal-challenge.png'
    ],
    'math-blitz' => [
         'title' => 'Math Blitz',
         'description' => 'Test your arithmetic and problem-solving speed in this fast-paced game.',
         'image' => 'images/math-blitz.png'
    ],
    'logical-reasoning' => [
         'title' => 'Logical Reasoning',
         'description' => 'Sharpen your critical thinking and logic skills with challenging puzzles.',
         'image' => 'images/logical-reasoning.png'
    ],
    'pattern-recognition' => [
         'title' => 'Pattern Recognition',
         'description' => 'Improve your visual and spatial reasoning with exciting pattern challenges.',
         'image' => 'images/pattern-recognition.png'
    ]
];

if (!isset($games[$gameSlug])) {
    echo "Invalid game selected.";
    exit;
}

$gameTitle = $games[$gameSlug]['title'];
$gameDescription = $games[$gameSlug]['description'];
$gameImage = $games[$gameSlug]['image'];

// Fetch the logged-in user's top score for this game.
$stmt = $pdo->prepare("SELECT MAX(score) AS top_score FROM game_scores WHERE user_id = ? AND game = ?");
$stmt->execute([$_SESSION['user_id'], $gameSlug]);
$userScoreRow = $stmt->fetch();
$userTopScore = $userScoreRow && $userScoreRow['top_score'] ? $userScoreRow['top_score'] : 0;

// Fetch the logged-in user's previous scores for this game.
$stmtMyScores = $pdo->prepare("SELECT score, created_at FROM game_scores WHERE user_id = ? AND game = ? ORDER BY created_at DESC");
$stmtMyScores->execute([$_SESSION['user_id'], $gameSlug]);
$myScores = $stmtMyScores->fetchAll();
?>

<div class="wrapper">
    <div class="content">
        <div class="container mt-5">
            <!-- Header Row with Back Button and Start Game Button -->
            <div class="row align-items-center mb-3">
                <div class="col-md-6">
                    <a href="games.php" class="btn btn-secondary">Back to Games</a>
                </div>
                <div class="col-md-6 text-end">
                    <a href="play_game.php?game=<?php echo urlencode($gameSlug); ?>" class="btn btn-success btn-lg">Start Game</a>
                </div>
            </div>
            
            <!-- Game Title -->
            <div class="row mb-3">
                <div class="col-md-12">
                    <h2><?php echo htmlspecialchars($gameTitle); ?> Preview</h2>
                </div>
            </div>
            
            <!-- Game Info Section -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <img src="<?php echo htmlspecialchars($gameImage); ?>" alt="<?php echo htmlspecialchars($gameTitle); ?>" class="img-fluid rounded">
                </div>
                <div class="col-md-8">
                    <p class="fst-italic"><?php echo htmlspecialchars($gameDescription); ?></p>
                    <h5>Your Top Score: <?php echo htmlspecialchars($userTopScore); ?></h5>
                </div>
            </div>
            
            <!-- User's Previous Scores -->
            <div class="row mb-4">
                <div class="col-md-12">
                    <h4>Your Previous Scores</h4>
                    <?php if (!empty($myScores)): ?>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Score</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($myScores as $scoreEntry): ?>
                                    <tr>
                                        <td><?php echo date('M d, Y', strtotime($scoreEntry['created_at'])); ?></td>
                                        <td><?php echo htmlspecialchars($scoreEntry['score']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p>You haven't played this game yet. Try it out!</p>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Link to Global Leaderboard -->
            <div class="row mb-4">
                <div class="col-md-12 text-center">
                    <a href="leaderboard.php?filter=overall&qtype=<?php echo urlencode($gameSlug); ?>" class="btn btn-primary btn-lg">View Leaderboard</a>
                </div>
            </div>
        </div>
    </div>
    <?php include 'includes/footer.php'; ?>
</div>
