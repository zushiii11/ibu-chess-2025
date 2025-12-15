// Main app initialization and navigation updates
window.App = (function () {
  let isRedirecting = false; // Prevent multiple redirects

  function updateNavigation() {
    // Check if API is available
    if (!window.API || !window.API.auth) {
      return; // API not loaded yet
    }

    const user = API.auth.getUser();
    const isAuthenticated = API.auth.isAuthenticated();
    const navLinks = document.querySelectorAll('.navbar-nav .nav-link');
    const authButtons = document.querySelector('#auth-buttons');

    // Update active navigation state
    const currentHash = window.location.hash.slice(2) || 'dashboard';
    navLinks.forEach(link => {
      const href = link.getAttribute('href');
      if (href) {
        const route = href.slice(2); // Remove #/
        if (currentHash.startsWith(route) && route !== '') {
          link.classList.add('active');
        } else if (currentHash === '' && route === 'dashboard') {
          link.classList.add('active');
        } else {
          link.classList.remove('active');
        }
      }
    });

    // Show/hide profile link based on authentication
    const profileLink = document.querySelector('a[href="#/profile"]');
    if (profileLink) {
      const profileNavItem = profileLink.closest('.nav-item');
      if (isAuthenticated && user) {
        profileNavItem.style.display = '';
      } else {
        profileNavItem.style.display = 'none';
      }
    }

    // Show/hide admin link based on role
    const adminLink = document.querySelector('a[href="#/admin"]');
    if (adminLink) {
      const adminNavItem = adminLink.closest('.nav-item');
      if (user && user.role === 'admin') {
        adminNavItem.style.display = '';
      } else {
        adminNavItem.style.display = 'none';
      }
    }

    // Update auth buttons
    if (authButtons) {
      if (isAuthenticated && user) {
        authButtons.innerHTML = `
          <span class="text-light me-2"><i class="bi bi-person-circle"></i> ${user.username}</span>
          <button class="btn btn-outline-light btn-sm" onclick="API.auth.logout(); window.location.hash='#/auth/login';">
            <i class="bi bi-box-arrow-right"></i> Logout
          </button>
        `;
      } else {
        authButtons.innerHTML = `
          <a class="btn btn-outline-light btn-sm" href="#/auth/login"><i class="bi bi-box-arrow-in-right"></i> Login</a>
          <a class="btn btn-primary btn-sm glow" href="#/auth/register"><i class="bi bi-person-plus"></i> Register</a>
        `;
      }
    }
  }

  function checkAuth() {
    if (!window.API || !window.API.auth) {
      return; // API not loaded yet
    }

    if (isRedirecting) {
      return; // Already redirecting, prevent flash
    }

    const hash = window.location.hash;
    const route = hash.slice(2); // Remove #/

    // Public routes that don't require authentication
    const publicRoutes = ['auth/login', 'auth/register', 'play'];

    // Check if current route is public
    const isPublicRoute = publicRoutes.some(publicRoute => route.startsWith(publicRoute));

    // If not public and not authenticated, redirect
    if (!isPublicRoute && route !== '' && !window.API.auth.isAuthenticated()) {
      isRedirecting = true;

      // Smooth redirect without showing flash
      const appContent = document.getElementById('app');
      if (appContent) {
        appContent.style.opacity = '0';
      }

      setTimeout(() => {
        window.location.hash = '#/auth/login';
        setTimeout(() => {
          isRedirecting = false;
          if (appContent) {
            appContent.style.opacity = '1';
          }
        }, 100);
      }, 50);
    }
  }

  function init() {
    // Wait a bit for API to be ready
    setTimeout(() => {
      updateNavigation();
      checkAuth(); // Check auth on init
    }, 100);

    // Update navigation when route changes
    window.addEventListener('hashchange', () => {
      updateNavigation();

      // Check auth after navigation updates
      setTimeout(() => {
        checkAuth();
      }, 50);
    });
  }

  return { init, updateNavigation };
})();

// Initialize app when DOM is ready
// Don't auto-init - let index.html handle it
// This prevents double initialization

