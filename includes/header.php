<!-- includes/header.php -->
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>11 Plus Prep</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Custom CSS -->
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
  <header>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
      <div class="container-fluid">
        <!-- Logo -->
        <a class="navbar-brand" href="index.php">
          <img src="images/logo.png" alt="Logo" width="30" height="30" class="d-inline-block align-text-top">
          11 Plus Prep
        </a>
        <!-- Toggle button for mobile view -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
          aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <!-- Navbar links -->
        <div class="collapse navbar-collapse" id="navbarNav">
          <ul class="navbar-nav mx-auto">
            <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
            <li class="nav-item"><a class="nav-link" href="learn.php">Learn</a></li>
            <li class="nav-item"><a class="nav-link" href="practice.php">Practice</a></li>
            <li class="nav-item"><a class="nav-link" href="games.php">Games</a></li>
            <li class="nav-item"><a class="nav-link" href="leaderboard.php">Leaderboard</a></li>
          </ul>
          <ul class="navbar-nav">
            <?php if (isset($_SESSION['user_id'])): ?>
                <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="profileDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="images/profile-icon.png" alt="Profile" width="30" height="30">
                </a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
                    <li><a class="dropdown-item" href="profile.php">My Account</a></li>
                    <li><a class="dropdown-item" href="settings.php">Settings</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                </ul>
                </li>
            <?php else: ?>
                <!-- If for some reason a non‑logged-in user can see the navbar, show a Login link -->
                <li class="nav-item">
                <a class="nav-link" href="login.php">Login</a>
                </li>
            <?php endif; ?>
          </ul>

        </div>
      </div>
    </nav>
  </header>
