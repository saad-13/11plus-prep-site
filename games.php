<?php 
$currentPage = 'games';
include 'includes/auth_check.php';
include 'includes/header.php';
include 'includes/db.php';
?>

<div class="container mt-5">
  <h2>Educational Games</h2>
  <p class="fst-italic">Select a game to play and improve your skills for the 11 Plus exam.</p>
  <div class="row row-cols-1 row-cols-md-2 g-4">
    <!-- Verbal Challenge Card -->
    <div class="col">
      <a href="game_preview.php?game=verbal-challenge" class="text-decoration-none text-dark">
        <div class="card h-100">
          <img src="images/verbal-challenge.jpg" class="card-img-top" alt="Verbal Challenge">
          <div class="card-body">
            <h5 class="card-title fw-bold">Verbal Challenge</h5>
            <p class="card-text fst-italic">Enhance your language and vocabulary skills.</p>
          </div>
        </div>
      </a>
    </div>
    <!-- Math Blitz Card -->
    <div class="col">
      <a href="game_preview.php?game=math-blitz" class="text-decoration-none text-dark">
        <div class="card h-100">
          <img src="images/math-blitz.jpg" class="card-img-top" alt="Math Blitz">
          <div class="card-body">
            <h5 class="card-title fw-bold">Math Blitz</h5>
            <p class="card-text fst-italic">Test your arithmetic and problem-solving speed.</p>
          </div>
        </div>
      </a>
    </div>
    <!-- Logical Reasoning Card -->
    <div class="col">
      <a href="game_preview.php?game=logical-reasoning" class="text-decoration-none text-dark">
        <div class="card h-100">
          <img src="images/logical-reasoning.jpg" class="card-img-top" alt="Logical Reasoning">
          <div class="card-body">
            <h5 class="card-title fw-bold">Logical Reasoning</h5>
            <p class="card-text fst-italic">Sharpen your critical thinking and logic skills.</p>
          </div>
        </div>
      </a>
    </div>
    <!-- Pattern Recognition Card -->
    <div class="col">
      <a href="game_preview.php?game=pattern-recognition" class="text-decoration-none text-dark">
        <div class="card h-100">
          <img src="images/pattern-recognition.jpg" class="card-img-top" alt="Pattern Recognition">
          <div class="card-body">
            <h5 class="card-title fw-bold">Pattern Recognition</h5>
            <p class="card-text fst-italic">Improve your visual and spatial reasoning.</p>
          </div>
        </div>
      </a>
    </div>
  </div>
</div>

<?php include 'includes/footer.php'; ?>
