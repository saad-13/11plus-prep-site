<?php 
$currentPage = 'games';
include 'includes/auth_check.php';
include 'includes/header.php';
include 'includes/db.php';
?>
<div class="wrapper">
  <div class="content">
    <div class="container mt-5">
      <h2 style="color: #F26419;">Educational Games</h2>
      <p class="fst-italic">Select a game to play and improve your skills for the 11 Plus exam.</p>
      <div class="row row-cols-1 row-cols-md-2 g-4">
        <!-- Verbal Challenge Card -->
        <div class="col">
          <a href="game_preview.php?game=verbal-challenge" class="text-decoration-none text-dark">
            <div class="card h-100">
              <div class="card-body">
              <img src="images/verbal-challenge.png" class="card-img-top" alt="Verbal Challenge" class="img-fluid" style="width:40px; height:40px;">
                <h5 class="card-title fw-bold">Word Scrabble</h5>
                <p class="card-text fst-italic">Enhance your language and vocabulary skills by solving anagrams and finding the hidden words.</p>
              </div>
            </div>
          </a>
        </div>
        <!-- Math Blitz Card -->
        <div class="col">
          <a href="game_preview.php?game=math-blitz" class="text-decoration-none text-dark">
            <div class="card h-100">
              <div class="card-body">
              <img src="images/math-blitz.png" class="card-img-top" alt="Math Blitz" class="img-fluid" style="width:40px; height:40px;">
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
              <div class="card-body">
              <img src="images/logical-reasoning.png" class="card-img-top" alt="Logical Reasoning" class="img-fluid" style="width:40px; height:40px;">
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
              <div class="card-body">
              <img src="images/pattern-recognition.png" class="card-img-top" alt="Pattern Recognition" class="img-fluid" style="width:40px; height:40px;">
                <h5 class="card-title fw-bold">Pattern Recognition</h5>
                <p class="card-text fst-italic">Improve your visual and spatial reasoning.</p>
              </div>
            </div>
          </a>
        </div>
      </div>
    </div>
  </div>
<?php include 'includes/footer.php'; ?>
</div>