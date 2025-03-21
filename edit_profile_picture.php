<?php
//session_start();
include 'includes/auth_check.php';
include 'includes/db.php';

$message = '';
$error = '';

// Process the form submission.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve the selected profile picture.
    $selected = isset($_POST['profile_picture']) ? $_POST['profile_picture'] : '';
    
    // Define allowed options.
    $allowed = [];
    for ($i = 1; $i <= 8; $i++) {
        $allowed[] = "profile-$i.png";
    }
    
    // Validate selection.
    if (in_array($selected, $allowed)) {
        $stmt = $pdo->prepare("UPDATE users SET profile_picture = ? WHERE id = ?");
        if ($stmt->execute([$selected, $_SESSION['user_id']])) {
            $message = "Profile picture updated successfully.";
        } else {
            $error = "Error updating profile picture.";
        }
    } else {
        $error = "Invalid selection.";
    }
}

// Prepare the list of preset profile pictures.
$profilePics = [];
for ($i = 1; $i <= 8; $i++) {
    $profilePics[] = "profile-$i.png";
}
?>

<?php include 'includes/header.php'; ?>

<div class="container mt-5">
    <h2>Change Profile Picture</h2>
    <p>Select a new profile picture from the options below and then press save.</p>
    
    <?php if ($message): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>
    
    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <div class="mt-3">
        <a href="profile.php" class="btn btn-secondary">Back to Profile</a>
    </div>
    
    <form action="edit_profile_picture.php" method="post">
        <div class="row">
            <?php foreach ($profilePics as $pic): ?>
                <div class="col-md-3 mb-3">
                    <div class="card shadow-sm">
                        <label class="m-0" style="cursor:pointer;">
                            <img src="images/profile/<?php echo $pic; ?>" class="card-img-top img-fluid" alt="<?php echo $pic; ?>">
                            <div class="card-body text-center p-2">
                                <input type="radio" name="profile_picture" value="<?php echo $pic; ?>" style="margin-right: 5px;">
                                <?php echo ucfirst(str_replace('.png', '', $pic)); ?>
                            </div>
                        </label>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <button type="submit" class="btn btn-primary">Save Profile Picture</button>
    </form>
    
</div>

<?php include 'includes/footer.php'; ?>
