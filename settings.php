<?php 
$currentPage = 'settings';
include 'includes/auth_check.php';
include 'includes/header.php';
include 'includes/db.php';
?>

<div class="wrapper">
  <div class="content">
    <div class="container mt-5">
      <h2>Settings</h2>
      <p>Customize your accessibility preferences below:</p>
      <form id="settings-form">
        <div class="form-check form-switch mb-3">
          <input class="form-check-input" type="checkbox" id="darkModeToggle">
          <label class="form-check-label" for="darkModeToggle">Dark Mode</label>
        </div>
        <div class="form-check form-switch mb-3">
          <input class="form-check-input" type="checkbox" id="colorBlindToggle">
          <label class="form-check-label" for="colorBlindToggle">Color Blind Mode</label>
        </div>
        <div class="form-check form-switch mb-3">
          <input class="form-check-input" type="checkbox" id="largerTextToggle">
          <label class="form-check-label" for="largerTextToggle">Larger Text</label>
        </div>
        <button type="button" id="saveSettings" class="btn btn-primary">Save Settings</button>
      </form>
    </div>

    <script>
    // On page load, retrieve saved settings from localStorage and apply classes
    document.addEventListener("DOMContentLoaded", function() {
      const darkMode = localStorage.getItem('darkMode') === 'true';
      const colorBlind = localStorage.getItem('colorBlind') === 'true';
      const largerText = localStorage.getItem('largerText') === 'true';

      document.getElementById('darkModeToggle').checked = darkMode;
      document.getElementById('colorBlindToggle').checked = colorBlind;
      document.getElementById('largerTextToggle').checked = largerText;

      if(darkMode) {
        document.body.classList.add('dark-mode');
      }
      if(colorBlind) {
        document.body.classList.add('color-blind-mode');
      }
      if(largerText) {
        document.body.classList.add('larger-text');
      }
    });

    // Save settings when "Save Settings" button is clicked
    document.getElementById('saveSettings').addEventListener('click', function() {
      const darkMode = document.getElementById('darkModeToggle').checked;
      const colorBlind = document.getElementById('colorBlindToggle').checked;
      const largerText = document.getElementById('largerTextToggle').checked;

      localStorage.setItem('darkMode', darkMode);
      localStorage.setItem('colorBlind', colorBlind);
      localStorage.setItem('largerText', largerText);

      // Remove existing classes
      document.body.classList.remove('dark-mode', 'color-blind-mode', 'larger-text');

      // Apply new classes based on settings
      if(darkMode) {
        document.body.classList.add('dark-mode');
      }
      if(colorBlind) {
        document.body.classList.add('color-blind-mode');
      }
      if(largerText) {
        document.body.classList.add('larger-text');
      }
      
      alert('Settings saved!');
    });
    </script>
  </div>
  <?php include 'includes/footer.php'; ?>
</div>  