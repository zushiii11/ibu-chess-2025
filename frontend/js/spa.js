// global SPA bootstrapper
window.IBUChess = (function(){
  const app = document.getElementById('app');
  async function render(){
    if (!app) {
      console.error('App element not found');
      return;
    }
    try {
      app.classList.remove('show');
      const {route, params} = Router.parseHash();
      const html = await Router.load(route, params);
      app.innerHTML = html;
      requestAnimationFrame(()=>app.classList.add('show'));
      
      // Re-initialize auth handlers after route change
      if (window.AuthHandler) {
        window.AuthHandler.initLogin();
        window.AuthHandler.initRegister();
      }
    } catch (error) {
      console.error('Error rendering route:', error);
      app.innerHTML = '<div class="alert alert-danger">Error loading page. Please check console.</div>';
    }
  }
  function init(){
    if (!app) {
      console.error('Cannot initialize: app element not found');
      return;
    }
    window.addEventListener('hashchange', render);
    // Initial render
    render();
  }
  return { init };
})();