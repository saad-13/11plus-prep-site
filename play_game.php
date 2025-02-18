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

// Defined game data (i could also store it in a database).
$games = [
    'verbal-challenge' => [
         'title' => 'Verbal Challenge',
         'description' => 'Enhance your language and vocabulary skills with fun challenges.',
         'image' => 'images/verbal-challenge.jpg'
    ],
    'math-blitz' => [
         'title' => 'Math Blitz',
         'description' => 'Test your arithmetic and problem-solving speed in a fast-paced game.',
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
?>

<div class="container mt-5">
    <div class="row">
        <!-- Back Button and Game Title -->
        <div class="col-md-12">
            <a href="game_preview.php?game=<?php echo urlencode($gameSlug); ?>" class="btn btn-secondary mb-3">Back to Preview</a>
            <h2><?php echo htmlspecialchars($gameTitle); ?></h2>
            <p class="fst-italic"><?php echo htmlspecialchars($gameDescription); ?></p>
        </div>
    </div>
    
    <!-- Game Interface -->
    <div class="row">
        <div class="col-md-12">
            <div id="game-area" class="card">
                <div class="card-body text-center">
                    <!-- A simple click game to test the page: click as many times as you can in 10 seconds -->
                    <h4>Click the button as many times as you can in 10 seconds!</h4>
                    <button id="clickBtn" class="btn btn-primary btn-lg">Click Me!</button>
                    <h3 class="mt-3">Score: <span id="score">0</span></h3>
                    <h4 class="mt-3">Time Remaining: <span id="timer">10</span> seconds</h4>
                    <button id="finishBtn" class="btn btn-success mt-3" style="display:none;">Finish Game</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Simple game score logic
let score = 0;
let timeLeft = 10;
let timerInterval;
let gameStarted = false;

document.getElementById('clickBtn').addEventListener('click', function() {
    if (!gameStarted) {
        startGame();
    }
    score++;
    document.getElementById('score').innerText = score;
});

function startGame() {
    gameStarted = true;
    timerInterval = setInterval(function() {
        timeLeft--;
        document.getElementById('timer').innerText = timeLeft;
        if (timeLeft <= 0) {
            clearInterval(timerInterval);
            endGame();
        }
    }, 1000);
}

function endGame() {
    document.getElementById('clickBtn').disabled = true;
    document.getElementById('finishBtn').style.display = 'inline-block';
}

// When the Finish Game button is clicked, send the score to the server.
document.getElementById('finishBtn').addEventListener('click', function() {
    let data = {
        game: '<?php echo $gameSlug; ?>',
        score: score
    };
    fetch('process_game.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(result => {
        if(result.success) {
            alert('Game score saved! Your score: ' + score);
            // redirect back to the preview page.
            window.location.href = "game_preview.php?game=<?php echo urlencode($gameSlug); ?>";
        } else {
            alert('Error saving game score.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error saving game score.');
    });
});
</script>

<?php include 'includes/footer.php'; ?>
