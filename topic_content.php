<?php
//session_start();
include 'includes/auth_check.php';
include 'includes/db.php';

// Check if topic_id is provided.
if (!isset($_GET['topic_id'])) {
    echo "No topic specified.";
    exit;
}

$topic_id = $_GET['topic_id'];

// Retrieve the topic from the database.
$stmt = $pdo->prepare("SELECT * FROM topics WHERE id = ?");
$stmt->execute([$topic_id]);
$topic = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$topic) {
    echo "Topic not found.";
    exit;
}
?>
<?php include 'includes/header.php'; ?>

<div class="container mt-5">
    <div class="d-flex align-items-center mb-3">
        <!-- Back button returns to the Learn page for the current subject -->
        <a href="learn.php?subject=<?php echo urlencode($topic['subject']); ?>" class="btn btn-secondary me-3">Back</a>
        <h2 class="mb-0"><?php echo htmlspecialchars($topic['title']); ?></h2>
    </div>
    <div class="card">
        <div class="card-body">
            <?php
            // Output the topic content.
            // If content contains HTML markup, it can be echoed directly.
            echo $topic['content'];
            ?>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
