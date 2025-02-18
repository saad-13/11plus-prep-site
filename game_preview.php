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

//game data (i need to store this in database).
$games = [
    'verbal-challenge' => [
         'title' => 'Verbal Challenge',
         'description' => 'Enhance your language and vocabulary skills with fun word puzzles.',
         'image' => 'images/verbal-challenge.jpg'
    ],
    'math-blitz' => [
         'title' => 'Math Blitz',
         'description' => 'Test your arithmetic and problem-solving speed in this fast-paced game.',
         'image' => 'images/math-blitz.jpg'
    ],
    'logical-reasoning' => [
         'title' => 'Logical Reasoning',
         'description' => 'Sharpen your critical thinking and logic skills with challenging puzzles.',
         'image' => 'images/logical-reasoning.jpg'
    ],
    'pattern-recognition' => [
         'title' => 'Pattern Recognition',
         'description' => 'Improve your visual and spatial reasoning with exciting pattern challenges.',
         'image' => 'images/pattern-recognition.jpg'
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

// Fetch the top 10 leaderboard entries for this game.
$stmt = $pdo->prepare("SELECT u.username, gs.score 
                       FROM game_scores gs 
                       JOIN users u ON gs.user_id = u.id 
                       WHERE gs.game = ? 
                       ORDER BY gs.score DESC 
                       LIMIT 10");
$stmt->execute([$gameSlug]);
$leaderboard = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container mt-5">
    <div class="row">
        <!-- Back button and Game Title -->
        <div class="col-md-12">
            <a href="games.php" class="btn btn-secondary mb-3">Back to Games</a>
            <h2><?php echo htmlspecialchars($gameTitle); ?> Preview</h2>
        </div>
    </div>
    
    <div class="row mb-4">
        <!-- Game Image -->
        <div class="col-md-4">
            <img src="<?php echo htmlspecialchars($gameImage); ?>" alt="<?php echo htmlspecialchars($gameTitle); ?>" class="img-fluid rounded">
        </div>
        <!-- Game Description and User's Top Score -->
        <div class="col-md-8">
            <p class="fst-italic"><?php echo htmlspecialchars($gameDescription); ?></p>
            <h5>Your Top Score: <?php echo htmlspecialchars($userTopScore); ?></h5>
        </div>
    </div>
    
    <!-- Leaderboard -->
    <div class="row mb-4">
        <div class="col-md-12">
            <h4>Leaderboard</h4>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Rank</th>
                        <th>Username</th>
                        <th>Score</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($leaderboard as $index => $entry): ?>
                    <tr>
                        <td><?php echo $index + 1; ?></td>
                        <td><?php echo htmlspecialchars($entry['username']); ?></td>
                        <td><?php echo htmlspecialchars($entry['score']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Start Game Button -->
    <div class="row">
        <div class="col-md-12 text-center">
            <a href="play_game.php?game=<?php echo urlencode($gameSlug); ?>" class="btn btn-primary btn-lg">Start Game</a>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
