<?php 
$currentPage = 'practice';
include 'includes/auth_check.php'; ?>
<?php include 'includes/header.php'; ?>

<div class="container mt-5">
  <h2 class="mb-4">Practice</h2>
  
  <!-- Section A: Two Big Cards -->
  <div class="row mb-4">
    <div class="col-md-6 mb-3">
      <a href="quiz.php?type=ai" class="text-decoration-none text-dark">
        <div class="card h-100">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-start">
              <h5 class="card-title fw-bold">AI Practice Tests</h5>
              <img src="images/ai-icon.png" alt="AI Icon" style="width:40px; height:40px;">
            </div>
            <p class="card-text fst-italic">Engage with AI-driven practice tests.</p>
          </div>
        </div>
      </a>
    </div>
    <div class="col-md-6 mb-3">
      <a href="quiz.php?type=mock" class="text-decoration-none text-dark">
        <div class="card h-100">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-start">
              <h5 class="card-title fw-bold">Mock Exams</h5>
              <img src="images/mock-icon.png" alt="Mock Exams Icon" style="width:40px; height:40px;">
            </div>
            <p class="card-text fst-italic">Simulate the exam experience with full-length tests.</p>
          </div>
        </div>
      </a>
    </div>
  </div>
  
  <!-- Section B: Four Smaller Subject Cards -->
  <div class="row row-cols-2 row-cols-md-4 g-3">
    <!-- Mathematics -->
    <div class="col">
      <a href="quiz.php?subject=mathematics" class="text-decoration-none text-dark">
        <div class="card h-100">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-start">
              <h6 class="card-title fw-bold">Mathematics</h6>
              <img src="images/math-icon.png" alt="Mathematics Icon" style="width:25px; height:25px;">
            </div>
            <p class="card-text fst-italic" style="font-size: 0.85rem;">Test your mathematical skills.</p>
          </div>
        </div>
      </a>
    </div>
    <!-- English -->
    <div class="col">
      <a href="quiz.php?subject=english" class="text-decoration-none text-dark">
        <div class="card h-100">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-start">
              <h6 class="card-title fw-bold">English</h6>
              <img src="images/english-icon.png" alt="English Icon" style="width:25px; height:25px;">
            </div>
            <p class="card-text fst-italic" style="font-size: 0.85rem;">Improve your language skills.</p>
          </div>
        </div>
      </a>
    </div>
    <!-- Verbal Reasoning -->
    <div class="col">
      <a href="quiz.php?subject=verbal" class="text-decoration-none text-dark">
        <div class="card h-100">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-start">
              <h6 class="card-title fw-bold">Verbal Reasoning</h6>
              <img src="images/verbal-icon.png" alt="Verbal Reasoning Icon" style="width:25px; height:25px;">
            </div>
            <p class="card-text fst-italic" style="font-size: 0.85rem;">Challenge your verbal logic.</p>
          </div>
        </div>
      </a>
    </div>
    <!-- Non-Verbal Reasoning -->
    <div class="col">
      <a href="quiz.php?subject=nonverbal" class="text-decoration-none text-dark">
        <div class="card h-100">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-start">
              <h6 class="card-title fw-bold">Non-Verbal Reasoning</h6>
              <img src="images/nonverbal-icon.png" alt="Non-Verbal Reasoning Icon" style="width:25px; height:25px;">
            </div>
            <p class="card-text fst-italic" style="font-size: 0.85rem;">Test your pattern recognition skills.</p>
          </div>
        </div>
      </a>
    </div>
  </div>
  
</div>
<?php include 'includes/footer.php'; ?>
