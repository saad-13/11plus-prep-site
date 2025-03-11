<?php 
$currentPage = 'practice';
include 'includes/auth_check.php'; ?>
<?php include 'includes/header.php'; ?>

<div class="wrapper">
  <div class="content">
    <div class="container mt-5">
      <h2 style="color: #F26419;">Practice</h2> 
          <p>Practice makes perfect! Engage with our practice tests to improve your skills and knowledge. Choose from a variety of subjects and test types.</p>
      <!-- Section A: Two Big Cards -->
      <div class="row mb-4">
        <div class="col-md-6 mb-3">
          <a href="quiz.php?type=ai" class="text-decoration-none text-dark">
            <div class="card h-100">
              <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                  <h5 class="card-title fw-bold">Practice Tests</h5>
                  <img src="images/ai-icon.png" alt="AI Icon" style="width:40px; height:40px;">
                </div>
                <p class="card-text fst-italic">Engage with AI-developed practice tests, each test is formed wih 100 questions.</p>
              </div>
            </div>
          </a>
        </div>
        <div class="col-md-6 mb-3">
          <a href="quiz.php?type=mock" class="text-decoration-none text-dark">
            <div class="card h-100">
              <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                  <h5 class="card-title fw-bold">Past Papers</h5>
                  <img src="images/exam-icon.png" alt="Mock Exams Icon" style="width:40px; height:40px;">
                </div>
                <p class="card-text fst-italic">Simulate the exam experience with full-length 100 Question test.</p>
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
                <p class="card-text fst-italic" style="font-size: 0.85rem;">Test your mathematical skills with a 10 Question Quiz.</p>
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
                <p class="card-text fst-italic" style="font-size: 0.85rem;">Improve your language skills with a 10 Question Quiz.</p>
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
                <p class="card-text fst-italic" style="font-size: 0.85rem;">Challenge your verbal logic with a 10 Question Quiz.</p>
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
                <p class="card-text fst-italic" style="font-size: 0.85rem;">Test your pattern recognition skills with a 10 Question Quiz.</p>
              </div>
            </div>
          </a>
        </div>
      </div>
      
    </div>
  </div>
  <?php include 'includes/footer.php'; ?>
</div>