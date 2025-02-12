// js/main.js
document.addEventListener('DOMContentLoaded', () => {
    const signupForm = document.getElementById('signupForm');
    if (signupForm) {
      signupForm.addEventListener('submit', (e) => {
        const email = document.getElementById('email').value;
        const confirmEmail = document.getElementById('confirmEmail').value;
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('confirmPassword').value;
        
        if (email !== confirmEmail) {
          alert("Emails do not match!");
          e.preventDefault();
        }
        if (password !== confirmPassword) {
          alert("Passwords do not match!");
          e.preventDefault();
        }
      });
    }
  });
  