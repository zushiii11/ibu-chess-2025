// global SPA bootstrapper
window.IBUChess = (function(){
  const app = document.getElementById('app');
  async function render(){
    app.classList.remove('show');
    const {route, params} = Router.parseHash();
    const html = await Router.load(route, params);
    app.innerHTML = html;
    requestAnimationFrame(()=>app.classList.add('show'));
  }
  function init(){
    window.addEventListener('hashchange', render);
    render();
  }
  return { init };
})();