// deluxe hash router (static stage)
window.Router = (function(){
  const routes = {
    "": "dashboard",
    "dashboard": "dashboard",
    "games": "games-list",
    "game": "game-detail",
    "tournaments": "tournaments",
    "profile": "profile",
    "admin": "admin",
    "auth/login":"auth-login",
    "auth/register":"auth-register",
    "reviews":"reviews"
  };
  async function load(route, params){
    const viewFile = routes[route] || "dashboard";
    const path = `./views/${viewFile}.html`;
    const res = await fetch(path, {cache: "no-cache"});
    return await res.text();
  }
  function parseHash(){
    const hash = (location.hash || "#/dashboard").slice(2);
    const [route, ...rest] = hash.split("/");
    return { route, params: rest };
  }
  return { load, parseHash };
})();