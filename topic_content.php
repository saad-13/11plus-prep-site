<?php
include 'includes/header.php';
include 'includes/db.php';

// Get subject and topic from query parameters
$subject = isset($_GET['subject']) ? $_GET['subject'] : '';
$topicSlug = isset($_GET['topic']) ? $_GET['topic'] : '';

// Query database for the topic details.
// !!!!! Need to make sure database has a 'topics' table with appropriate columns.
$stmt = $pdo->prepare("SELECT * FROM topics WHERE subject = ? AND slug = ?");
$stmt->execute([$subject, $topicSlug]);
$topic = $stmt->fetch();

if (!$topic) {
    echo "<div class='container mt-5'><p>Topic not found.</p></div>";
    include 'includes/footer.php';
    exit;
}
?>
<div class="container mt-4">
  <div class="d-flex align-items-center mb-3">
    <a href="topic_selection.php?subject=<?php echo urlencode($subject); ?>" class="btn btn-secondary me-3">Back</a>
    <h2 class="mb-0"><?php echo htmlspecialchars($topic['title']); ?></h2>
  </div>
  <div class="mt-3">
    <?php
    // Display the content of the topic
    echo $topic['content'];
    ?>
  </div>
</div>
<?php include 'includes/footer.php'; ?>
