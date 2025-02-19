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
         'description' => 'Unscramble the word correctly!',
         'image' => 'images/verbal-challenge.jpg'
    ],
    'math-blitz' => [
         'title' => 'Math Blitz',
         'description' => 'Answer math questions quickly!',
         'image' => 'images/math-blitz.jpg'
    ],
    'logical-reasoning' => [
         'title' => 'Logical Reasoning',
         'description' => 'Solve the sequence puzzles!',
         'image' => 'images/logical-reasoning.jpg'
    ],
    'pattern-recognition' => [
         'title' => 'Pattern Recognition',
         'description' => 'Identify the missing pattern!',
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

// Query the database for game questions for the selected game (limit 5 questions).
$stmt = $pdo->prepare("SELECT * FROM game_questions WHERE game = ? ORDER BY RAND() LIMIT 5");
$stmt->execute([$gameSlug]);
$questionsFromDB = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
    
    <!-- Game Area -->
    <div id="game-container" class="card">
        <div class="card-body text-center">
            <!-- The game UI will be dynamically injected here -->
        </div>
    </div>
</div>

<script>
// Debugging: log the questions array to the console.
console.log("Fetched questions:", <?php echo json_encode($questionsFromDB); ?>);

var questions = <?php echo json_encode($questionsFromDB); ?>;
if (!questions || questions.length === 0) {
    // If no questions are available, display a message.
    document.getElementById('game-container').querySelector('.card-body').innerHTML = "<h4>No questions available for this game.</h4>";
    throw new Error("No questions available");
}

var current = 0;
var score = 0;
var container = document.getElementById('game-container').querySelector('.card-body');
var gameSlug = '<?php echo $gameSlug; ?>';

// Use container-level event listeners for consistency.
function addSubmitListener(callback) {
    container.addEventListener('click', function handler(e) {
        if(e.target && e.target.id === 'submitAnswer') {
            // Remove listener to prevent duplicate.
            container.removeEventListener('click', handler);
            callback();
        }
    });
}

// Common function to submit score (unchanged).
function submitScore() {
    var data = {
        game: gameSlug,
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
            window.location.href = "game_preview.php?game=" + encodeURIComponent(gameSlug);
        } else {
            alert('Error saving game score.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error saving game score.');
    });
}

/*----------------------------------
  Verbal Challenge
  (Unscramble the word)
----------------------------------*/
function runVerbalChallenge() {
    function showQuestion() {
        if(current < questions.length) {
            container.innerHTML = `
                <h4>Unscramble the word:</h4>
                <div id="wordDisplay" style="font-size:2em; margin:20px;">${questions[current].question_text}</div>
                <input type="text" id="answerInput" class="form-control" placeholder="Your answer">
                <button id="submitAnswer" class="btn btn-primary mt-3">Submit</button>
                <div id="feedback" class="mt-3"></div>
                <div id="gameScore" class="mt-3">Score: ${score}</div>
            `;
            // Add listener on container.
            addSubmitListener(function() {
                var answer = document.getElementById('answerInput').value.trim().toUpperCase();
                if(answer === questions[current].answer.toUpperCase()) {
                    score++;
                    document.getElementById('feedback').innerText = 'Correct!';
                } else {
                    document.getElementById('feedback').innerText = 'Incorrect. Correct answer: ' + questions[current].answer;
                }
                current++;
                setTimeout(showQuestion, 1000);
            });
        } else {
            container.innerHTML = `<h4>Game Over!</h4><p>Your final score is ${score} out of ${questions.length}</p>`;
            submitScore();
        }
    }
    showQuestion();
}

/*----------------------------------
  Math Blitz
  (Multiple-choice math questions)
----------------------------------*/
function runMathBlitz() {
    function showQuestion() {
        if(current < questions.length) {
            var q = questions[current];
            var opts = [];
            try {
                opts = JSON.parse(q.options);
            } catch(e) {
                opts = ["Option A", "Option B", "Option C", "Option D"];
            }
            var html = `<h4>${q.question_text}</h4>`;
            opts.forEach(function(opt, i) {
                html += `<div class="form-check">
                            <input class="form-check-input" type="radio" name="option" id="option${i}" value="${opt}">
                            <label class="form-check-label" for="option${i}">${opt}</label>
                         </div>`;
            });
            html += `<button id="submitAnswer" class="btn btn-primary mt-3">Submit</button>
                     <div id="feedback" class="mt-3"></div>`;
            container.innerHTML = html;
            addSubmitListener(function(){
                var options = document.getElementsByName('option');
                var selected;
                for(var i = 0; i < options.length; i++){
                    if(options[i].checked) {
                        selected = options[i].value;
                        break;
                    }
                }
                if(selected === questions[current].answer) {
                    score++;
                    document.getElementById('feedback').innerText = 'Correct!';
                } else {
                    document.getElementById('feedback').innerText = 'Incorrect. The correct answer was ' + questions[current].answer;
                }
                current++;
                setTimeout(showQuestion, 1000);
            });
        } else {
            container.innerHTML = `<h4>Game Over!</h4><p>Your final score is ${score} out of ${questions.length}</p>`;
            submitScore();
        }
    }
    showQuestion();
}

/*----------------------------------
  Logical Reasoning
  (Sequence puzzle)
----------------------------------*/
function runLogicalReasoning() {
    function showQuestion() {
        if(current < questions.length) {
            var p = questions[current];
            var opts = [];
            try {
                opts = JSON.parse(p.options);
            } catch(e) {
                opts = ["Option A", "Option B", "Option C", "Option D"];
            }
            var html = `<h4>${p.question_text}</h4>`;
            opts.forEach(function(opt, i) {
                html += `<div class="form-check">
                            <input class="form-check-input" type="radio" name="option" id="option${i}" value="${opt}">
                            <label class="form-check-label" for="option${i}">${opt}</label>
                         </div>`;
            });
            html += `<button id="submitAnswer" class="btn btn-primary mt-3">Submit</button>
                     <div id="feedback" class="mt-3"></div>`;
            container.innerHTML = html;
            addSubmitListener(function(){
                var options = document.getElementsByName('option');
                var selected;
                for(var i = 0; i < options.length; i++){
                    if(options[i].checked) {
                        selected = options[i].value;
                        break;
                    }
                }
                if(selected === questions[current].answer) {
                    score++;
                    document.getElementById('feedback').innerText = 'Correct!';
                } else {
                    document.getElementById('feedback').innerText = 'Incorrect. The correct answer was ' + questions[current].answer;
                }
                current++;
                setTimeout(showQuestion, 1000);
            });
        } else {
            container.innerHTML = `<h4>Game Over!</h4><p>Your final score is ${score} out of ${questions.length}</p>`;
            submitScore();
        }
    }
    showQuestion();
}

/*----------------------------------
  Pattern Recognition
  (Identify the missing pattern)
----------------------------------*/
function runPatternRecognition() {
    function showQuestion() {
        if(current < questions.length) {
            var p = questions[current];
            var opts = [];
            try {
                opts = JSON.parse(p.options);
            } catch(e) {
                opts = ["Option A", "Option B", "Option C", "Option D"];
            }
            var html = `<h4>${p.question_text}</h4>`;
            opts.forEach(function(opt, i) {
                html += `<div class="form-check">
                            <input class="form-check-input" type="radio" name="option" id="option${i}" value="${opt}">
                            <label class="form-check-label" for="option${i}">${opt}</label>
                         </div>`;
            });
            html += `<button id="submitAnswer" class="btn btn-primary mt-3">Submit</button>
                     <div id="feedback" class="mt-3"></div>`;
            container.innerHTML = html;
            addSubmitListener(function(){
                var options = document.getElementsByName('option');
                var selected;
                for(var i = 0; i < options.length; i++){
                    if(options[i].checked) {
                        selected = options[i].value;
                        break;
                    }
                }
                if(selected === questions[current].answer) {
                    score++;
                    document.getElementById('feedback').innerText = 'Correct!';
                } else {
                    document.getElementById('feedback').innerText = 'Incorrect. The correct answer was ' + questions[current].answer;
                }
                current++;
                setTimeout(showQuestion, 1000);
            });
        } else {
            container.innerHTML = `<h4>Game Over!</h4><p>Your final score is ${score} out of ${questions.length}</p>`;
            submitScore();
        }
    }
    showQuestion();
}

// Decide which game to run.
if (gameSlug === 'verbal-challenge') {
    runVerbalChallenge();
} else if (gameSlug === 'math-blitz') {
    runMathBlitz();
} else if (gameSlug === 'logical-reasoning') {
    runLogicalReasoning();
} else if (gameSlug === 'pattern-recognition') {
    runPatternRecognition();
}
</script>

<?php include 'includes/footer.php'; ?>
