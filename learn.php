<?php 
$currentPage = 'learn';
include 'includes/auth_check.php'; ?>
<?php include 'includes/header.php'; ?>


<div class="container mt-5">
  <h2 class="mb-4">Learn</h2>
  <div class="row row-cols-1 row-cols-md-2 g-4">
    <!-- Mathematics Card -->
    <div class="col">
      <a href="topic_selection.php?subject=mathematics" class="text-decoration-none text-dark">
        <div class="card h-100">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-start">
              <h5 class="card-title fw-bold">Mathematics</h5>
              <img src="images/math-icon.png" alt="Math Icon" style="width:40px; height:40px;">
            </div>
            <p class="card-text fst-italic">Explore concepts and problem solving in Mathematics.</p>
          </div>
        </div>
      </a>
    </div>
    <!-- English Card -->
    <div class="col">
      <a href="topic_selection.php?subject=english" class="text-decoration-none text-dark">
        <div class="card h-100">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-start">
              <h5 class="card-title fw-bold">English</h5>
              <img src="images/english-icon.png" alt="English Icon" style="width:40px; height:40px;">
            </div>
            <p class="card-text fst-italic">Learn grammar, vocabulary, and comprehension skills.</p>
          </div>
        </div>
      </a>
    </div>
    <!-- Verbal Reasoning Card -->
    <div class="col">
      <a href="topic_selection.php?subject=verbal" class="text-decoration-none text-dark">
        <div class="card h-100">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-start">
              <h5 class="card-title fw-bold">Verbal Reasoning</h5>
              <img src="images/verbal-icon.png" alt="Verbal Reasoning Icon" style="width:40px; height:40px;">
            </div>
            <p class="card-text fst-italic">Develop logical thinking through language and words.</p>
          </div>
        </div>
      </a>
    </div>
    <!-- Non-Verbal Reasoning Card -->
    <div class="col">
      <a href="topic_selection.php?subject=nonverbal" class="text-decoration-none text-dark">
        <div class="card h-100">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-start">
              <h5 class="card-title fw-bold">Non-Verbal Reasoning</h5>
              <img src="images/nonverbal-icon.png" alt="Non-Verbal Icon" style="width:40px; height:40px;">
            </div>
            <p class="card-text fst-italic">Improve your ability to interpret visual information.</p>
          </div>
        </div>
      </a>
    </div>
  </div>
</div>


<?php include 'includes/footer.php'; ?>
