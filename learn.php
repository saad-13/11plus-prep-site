<?php 
$currentPage = 'learn';
include 'includes/auth_check.php';
include 'includes/db.php';
include 'includes/header.php';
?>
<div class="wrapper">
  <div class="content">
    <div class="container mt-5">
      <h2 style="color: #F26419;">Learn</h2>
      <p class="lead">Select a subject to start learning.</p>
      
      <!-- Subject Navigation as Cards -->
    <div class="d-flex justify-content-around mb-4 flex-wrap">
      <!-- Mathematics -->
      <a href="learn.php?subject=mathematics" class="text-decoration-none">
        <div class="card text-center mb-3 subject-card <?php echo (isset($_GET['subject']) && $_GET['subject'] == 'mathematics') ? 'border border-3 border-warning' : ''; ?>">
          <div class="card-body">
            <h5 class="card-title fw-bold">Mathematics</h5>
            <img src="images/math-icon.png" alt="Math Icon" class="img-fluid" style="width:50px; height:50px;">
          </div>
        </div>
      </a>
      <!-- English -->
      <a href="learn.php?subject=english" class="text-decoration-none">
        <div class="card text-center mb-3 subject-card <?php echo (isset($_GET['subject']) && $_GET['subject'] == 'english') ? 'border border-3 border-warning' : ''; ?>">
          <div class="card-body">
            <h5 class="card-title fw-bold">English</h5>
            <img src="images/english-icon.png" alt="English Icon" class="img-fluid" style="width:50px; height:50px;">
          </div>
        </div>
      </a>
      <!-- Verbal Reasoning -->
      <a href="learn.php?subject=verbal" class="text-decoration-none">
        <div class="card text-center mb-3 subject-card <?php echo (isset($_GET['subject']) && $_GET['subject'] == 'verbal') ? 'border border-3 border-warning' : ''; ?>">
          <div class="card-body">
            <h5 class="card-title fw-bold">Verbal Reasoning</h5>
            <img src="images/verbal-icon.png" alt="Verbal Reasoning Icon" class="img-fluid" style="width:50px; height:50px;">
          </div>
        </div>
      </a>
      <!-- Non-Verbal Reasoning -->
      <a href="learn.php?subject=nonverbal" class="text-decoration-none">
        <div class="card text-center mb-3 subject-card <?php echo (isset($_GET['subject']) && $_GET['subject'] == 'nonverbal') ? 'border border-3 border-warning' : ''; ?>">
          <div class="card-body">
            <h5 class="card-title fw-bold">Non-Verbal Reasoning</h5>
            <img src="images/nonverbal-icon.png" alt="Non-Verbal Icon" class="img-fluid" style="width:50px; height:50px;">
          </div>
        </div>
      </a>
    </div>

  
      <!-- Topics/Subtopics Section -->
      <?php
      if (isset($_GET['subject'])) {
          $selected = $_GET['subject'];
          // Query the topics table for this subject.
          $stmtTopics = $pdo->prepare("SELECT * FROM topics WHERE subject = ? ORDER BY title ASC");
          $stmtTopics->execute([$selected]);
          $topicsFromDB = $stmtTopics->fetchAll(PDO::FETCH_ASSOC);
          
          if ($topicsFromDB) {
              echo '<div class="mt-5">';
              echo '<h3>Topics for ' . ucfirst($selected) . '</h3>';
              echo '<div class="row row-cols-1 row-cols-md-4 g-4">';
              foreach ($topicsFromDB as $topic) {
                  // Each topic card links to topic_content.php?topic_id=...
                  echo '<div class="col">';
                  echo '<a href="topic_content.php?topic_id=' . urlencode($topic['id']) . '" class="text-decoration-none text-dark">';
                  echo '<div class="card h-100">';
                  echo '<div class="card-body text-center">';
                  echo '<h6 class="card-title fw-bold">' . htmlspecialchars($topic['title']) . '</h6>';
                  echo '</div></div></a></div>';
              }
              echo '</div></div>';
          } else {
              echo '<p class="mt-4">No topics found for this subject.</p>';
          }
      }
      ?>
  
    </div>
  </div>
  <?php include 'includes/footer.php'; ?>
</div>


