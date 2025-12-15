// Hash router for static prototype
window.Router = (function () {
  const routes = {
    "": "dashboard",
    "dashboard": "dashboard",
    "games": "games-list",
    "game": "game-detail",
    "tournaments": "tournaments",
    "profile": "profile",
    "admin": "admin",
    "auth/login": "auth-login",
    "auth/register": "auth-register",
    "reviews": "reviews",
    "play": "play",
    "play/ai": "play-ai",
    "play/human": "play-human",
    "challenge": "challenge"
  };
  async function load(route, params) {
    // Handle nested routes like "auth/login"
    let viewFile = routes[route];

    // If route not found, try to find it as nested route
    if (!viewFile && route.includes('/')) {
      viewFile = routes[route];
    }

    // Default to dashboard if route not found
    if (!viewFile) {
      viewFile = "dashboard";
    }

    const path = `./views/${viewFile}.html`;
    try {
      const res = await fetch(path, { cache: "no-cache" });
      if (!res.ok) {
        throw new Error(`Failed to load ${path}: ${res.status}`);
      }
      return await res.text();
    } catch (error) {
      console.error('Error loading view:', error);
      return `<div class="alert alert-danger">Error loading page: ${error.message}</div>`;
    }
  }
  function parseHash() {
    const hash = (location.hash || "#/dashboard").slice(2);
    const parts = hash.split("/");
    const route = parts.join("/"); // Keep full path for nested routes like "auth/login"
    const params = parts.slice(1); // Everything after first part
    return { route, params };
  }
  return { load, parseHash };
})();
