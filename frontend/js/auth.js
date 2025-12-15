// Authentication handlers for login and register pages
window.AuthHandler = (function () {
  function initLogin() {
    const form = document.querySelector('#login-form');
    if (!form) return;

    form.addEventListener('submit', async (e) => {
      e.preventDefault();
      const submitBtn = form.querySelector('button[type="submit"]');
      const errorDiv = document.getElementById('login-error');

      const username = form.querySelector('input[name="username"]').value;
      const password = form.querySelector('input[name="password"]').value;

      submitBtn.disabled = true;
      submitBtn.textContent = 'Logging in...';
      if (errorDiv) errorDiv.style.display = 'none';

      try {
        await API.auth.login(username, password);
        window.location.hash = '#/dashboard';
      } catch (error) {
        console.error('Login error:', error);
        const errorMessage = error.message || 'Login failed. Please check if backend is running.';
        if (errorDiv) {
          errorDiv.textContent = errorMessage;
          errorDiv.style.display = 'block';
        } else {
          alert('Login failed: ' + errorMessage);
        }
      } finally {
        submitBtn.disabled = false;
        submitBtn.textContent = 'Login';
      }
    });
  }

  function initRegister() {
    const form = document.querySelector('#register-form');
    if (!form) return;

    form.addEventListener('submit', async (e) => {
      e.preventDefault();
      const submitBtn = form.querySelector('button[type="submit"]');
      const errorDiv = document.getElementById('register-error');

      const username = form.querySelector('input[name="username"]').value;
      const email = form.querySelector('input[name="email"]').value;
      const password = form.querySelector('input[name="password"]').value;
      const confirmPassword = form.querySelector('input[name="confirmPassword"]').value;

      if (password !== confirmPassword) {
        if (errorDiv) {
          errorDiv.textContent = 'Passwords do not match';
          errorDiv.style.display = 'block';
        }
        return;
      }

      submitBtn.disabled = true;
      submitBtn.textContent = 'Creating account...';
      if (errorDiv) errorDiv.style.display = 'none';

      try {
        await API.auth.register({ username, email, password, role: 'player' });
        window.location.hash = '#/dashboard';
      } catch (error) {
        console.error('Registration error:', error);
        let errorMessage = error.message || 'Registration failed. Please check if backend is running.';

        // Parse user-friendly error messages
        if (errorMessage.includes('Duplicate entry') && errorMessage.includes('email')) {
          errorMessage = 'This email is already in use. Please use a different email or login.';
        } else if (errorMessage.includes('Duplicate entry') && errorMessage.includes('username')) {
          errorMessage = 'This username is already taken. Please choose a different username.';
        } else if (errorMessage.includes('1062')) {
          errorMessage = 'This email or username is already in use. Please try a different one.';
        }

        if (errorDiv) {
          errorDiv.innerHTML = `<i class="bi bi-exclamation-triangle"></i> ${errorMessage}`;
          errorDiv.style.display = 'block';
        } else {
          alert('Registration failed: ' + errorMessage);
        }
      } finally {
        submitBtn.disabled = false;
        submitBtn.textContent = 'Create account';
      }
    });
  }

  return {
    initLogin,
    initRegister
  };
})();

