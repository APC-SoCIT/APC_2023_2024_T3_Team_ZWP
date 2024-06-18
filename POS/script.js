const loginForm = document.getElementById('login-form');
const errorMessage = document.getElementById('error-message');

loginForm.addEventListener('submit', (event) => {
  event.preventDefault();

  const formData = new FormData(loginForm);

  fetch('submit.php', {
    method: 'POST',
    body: formData
  })
  .then(response => response.text())
  .then(data => {
    if (data.includes('Invalid username or password')) {
      errorMessage.textContent = 'Invalid username or password';
    } else {
      window.location.href = 'index.php';
    }
  })
  .catch(error => {
    console.error('Error:', error);
    errorMessage.textContent = 'An error occurred';
  });
});
