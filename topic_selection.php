<?php
include 'includes/header.php';
include 'includes/db.php';

// Get subject from query parameter
$subject = isset($_GET['subject']) ? $_GET['subject'] : '';

// !!!!!!!! sample topics for each subject. Need to fetch these from the database.
$topics = [];

if ($subject == 'mathematics') {
    $topics = [
        ['slug' => 'algebra', 'title' => 'Algebra', 'description' => 'Learn about equations and variables.'],
        ['slug' => 'geometry', 'title' => 'Geometry', 'description' => 'Understand shapes and spaces.'],
        ['slug' => 'arithmetic', 'title' => 'Arithmetic', 'description' => 'Basic number operations.'],
        ['slug' => 'statistics', 'title' => 'Statistics', 'description' => 'Data and probability.'],
        
    ];
} elseif ($subject == 'english') {
    $topics = [
        ['slug' => 'grammar', 'title' => 'Grammar', 'description' => 'Sentence structure and rules.'],
        ['slug' => 'vocabulary', 'title' => 'Vocabulary', 'description' => 'Expand your word bank.'],
        ['slug' => 'comprehension', 'title' => 'Comprehension', 'description' => 'Understand and interpret text.'],
        ['slug' => 'spelling', 'title' => 'Spelling', 'description' => 'Correct spelling of words.'],
    ];
} elseif ($subject == 'verbal') {
    $topics = [
        ['slug' => 'synonyms', 'title' => 'Synonyms', 'description' => 'Words with similar meanings.'],
        ['slug' => 'antonyms', 'title' => 'Antonyms', 'description' => 'Words with opposite meanings.'],
        ['slug' => 'analogies', 'title' => 'Analogies', 'description' => 'Relationships between words.'],
        ['slug' => 'sentence-completion', 'title' => 'Sentence Completion', 'description' => 'Complete the sentence with the right word.'],
    ];
} elseif ($subject == 'nonverbal') {
    $topics = [
        ['slug' => 'pattern', 'title' => 'Pattern Recognition', 'description' => 'Identify sequences and designs.'],
        ['slug' => 'shapes', 'title' => 'Shapes and Figures', 'description' => 'Visual analysis of shapes.'],
        ['slug' => 'series', 'title' => 'Number Series', 'description' => 'Find the missing number in a sequence.'],
        ['slug' => 'analogy', 'title' => 'Analogy', 'description' => 'Find the relationship between shapes.'],
    ];
}
?>
<div class="wrapper">
<div class="content">
    <div class="container mt-4">
      <div class="d-flex align-items-center mb-3">
        <a href="learn.php" class="btn btn-secondary me-3">Back</a>
        <h2 class="mb-0 text-capitalize"><?php echo htmlspecialchars($subject); ?></h2>
      </div>
      <p class="fst-italic">Please select a Topic you want to learn.</p>
      
      <div class="row row-cols-2 row-cols-md-4 g-3">
        <?php foreach ($topics as $topic): ?>
          <div class="col">
            <a href="topic_content.php?subject=<?php echo urlencode($subject); ?>&topic=<?php echo urlencode($topic['slug']); ?>" class="text-decoration-none text-dark">
              <div class="card h-100">
                <div class="card-body">
                  <div class="d-flex justify-content-between align-items-start">
                    <h6 class="card-title fw-bold"><?php echo htmlspecialchars($topic['title']); ?></h6>
                    <!-- include a small icon if available -->
                    <img src="images/<?php echo htmlspecialchars($topic['slug']); ?>-icon.png" alt="<?php echo htmlspecialchars($topic['title']); ?> Icon" style="width:25px; height:25px;">
                  </div>
                  <p class="card-text fst-italic" style="font-size: 0.85rem;"><?php echo htmlspecialchars($topic['description']); ?></p>
                </div>
              </div>
            </a>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>  
  <?php include 'includes/footer.php'; ?>
</div>