<?php include 'includes/header.php'; ?>
<div class="container mt-5">
  <h2>Practice</h2>
  <div class="d-grid gap-3">
    <button onclick="location.href='mock_exam.php'" class="btn btn-warning btn-lg">Practice Papers (Mock Exam)</button>
    <button onclick="location.href='subject_practice.php?subject=maths'" class="btn btn-secondary btn-lg">Maths Practice</button>
    <button onclick="location.href='subject_practice.php?subject=english'" class="btn btn-secondary btn-lg">English Practice</button>
    <button onclick="location.href='subject_practice.php?subject=verbal'" class="btn btn-secondary btn-lg">Verbal Reasoning</button>
    <button onclick="location.href='subject_practice.php?subject=nonverbal'" class="btn btn-secondary btn-lg">Non-Verbal Reasoning</button>
  </div>
</div>
<?php include 'includes/footer.php'; ?>
