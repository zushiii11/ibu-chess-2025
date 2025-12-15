// API Service for IBU Chess Frontend
window.API = (function() {
  // API base URL - can be configured via window.API_BASE_URL or defaults to localhost:8000
  const BASE_URL = (window.API_BASE_URL || 'http://localhost:8000') + '/api';
  let token = localStorage.getItem('token') || null;
  let currentUser = null;

  // Helper function to get auth headers
  function getHeaders(includeAuth = true) {
    const headers = {
      'Content-Type': 'application/json'
    };
    if (includeAuth && token) {
      headers['Authorization'] = `Bearer ${token}`;
    }
    return headers;
  }

  // Helper function to handle responses
  async function handleResponse(response) {
    // Get response text first to check if it's empty
    const text = await response.text();
    
    // If response is empty, throw error
    if (!text || text.trim() === '') {
      throw new Error('Empty response from server. Check backend for errors.');
    }
    
    let data;
    try {
      data = JSON.parse(text);
    } catch (e) {
      // If response is not JSON, show the actual response
      console.error('Non-JSON response:', text);
      throw new Error(`Server returned invalid JSON. Response: ${text.substring(0, 200)}`);
    }
    
    if (!response.ok) {
      throw new Error(data.error || `Server error: ${response.status}`);
    }
    return data;
  }

  // Authentication
  const auth = {
    async register(userData) {
      try {
        const response = await fetch(`${BASE_URL}/auth/register`, {
          method: 'POST',
          headers: getHeaders(false),
          body: JSON.stringify(userData)
        });
        
        if (!response) {
          throw new Error('Failed to connect to server. Is the backend running on http://localhost:8000?');
        }
        
        const data = await handleResponse(response);
        token = data.token;
        currentUser = data.user;
        localStorage.setItem('token', token);
        localStorage.setItem('user', JSON.stringify(currentUser));
        return data;
      } catch (error) {
        if (error.message.includes('Failed to fetch') || error.message.includes('NetworkError')) {
          throw new Error('Cannot connect to backend server. Please make sure the backend is running on http://localhost:8000');
        }
        throw error;
      }
    },

    async login(username, password) {
      try {
        const response = await fetch(`${BASE_URL}/auth/login`, {
          method: 'POST',
          headers: getHeaders(false),
          body: JSON.stringify({ username, password })
        });
        
        if (!response) {
          throw new Error('Failed to connect to server. Is the backend running on http://localhost:8000?');
        }
        
        const data = await handleResponse(response);
        token = data.token;
        currentUser = data.user;
        localStorage.setItem('token', token);
        localStorage.setItem('user', JSON.stringify(currentUser));
        return data;
      } catch (error) {
        if (error.message.includes('Failed to fetch') || error.message.includes('NetworkError')) {
          throw new Error('Cannot connect to backend server. Please make sure the backend is running on http://localhost:8000');
        }
        throw error;
      }
    },

    async getCurrentUser() {
      if (!token) return null;
      try {
        const response = await fetch(`${BASE_URL}/auth/me`, {
          headers: getHeaders()
        });
        const data = await handleResponse(response);
        currentUser = data;
        localStorage.setItem('user', JSON.stringify(currentUser));
        return data;
      } catch (error) {
        this.logout();
        return null;
      }
    },

    logout() {
      token = null;
      currentUser = null;
      localStorage.removeItem('token');
      localStorage.removeItem('user');
    },

    isAuthenticated() {
      return token !== null;
    },

    getToken() {
      return token;
    },

    getUser() {
      if (!currentUser) {
        const stored = localStorage.getItem('user');
        if (stored) {
          currentUser = JSON.parse(stored);
        }
      }
      return currentUser;
    }
  };

  // Initialize token and user from localStorage
  if (localStorage.getItem('token')) {
    token = localStorage.getItem('token');
    const stored = localStorage.getItem('user');
    if (stored) {
      currentUser = JSON.parse(stored);
    }
  }

  // Generic CRUD operations
  async function get(endpoint, params = {}) {
    const queryString = new URLSearchParams(params).toString();
    const url = queryString ? `${BASE_URL}${endpoint}?${queryString}` : `${BASE_URL}${endpoint}`;
    const response = await fetch(url, {
      headers: getHeaders()
    });
    return handleResponse(response);
  }

  async function post(endpoint, data) {
    const response = await fetch(`${BASE_URL}${endpoint}`, {
      method: 'POST',
      headers: getHeaders(),
      body: JSON.stringify(data)
    });
    return handleResponse(response);
  }

  async function put(endpoint, data) {
    const response = await fetch(`${BASE_URL}${endpoint}`, {
      method: 'PUT',
      headers: getHeaders(),
      body: JSON.stringify(data)
    });
    return handleResponse(response);
  }

  async function patch(endpoint, data) {
    const response = await fetch(`${BASE_URL}${endpoint}`, {
      method: 'PATCH',
      headers: getHeaders(),
      body: JSON.stringify(data)
    });
    return handleResponse(response);
  }

  async function del(endpoint) {
    const response = await fetch(`${BASE_URL}${endpoint}`, {
      method: 'DELETE',
      headers: getHeaders()
    });
    if (response.status === 204) {
      return null;
    }
    return handleResponse(response);
  }

  // Resource-specific methods
  const users = {
    getAll: (filters) => get('/users', filters),
    getById: (id) => get(`/users/${id}`),
    create: (data) => post('/users', data),
    update: (id, data) => put(`/users/${id}`, data),
    delete: (id) => del(`/users/${id}`)
  };

  const tournaments = {
    getAll: (filters) => get('/tournaments', filters),
    getById: (id) => get(`/tournaments/${id}`),
    getWithParticipants: (id) => get(`/tournaments/${id}/participants`),
    create: (data) => post('/tournaments', data),
    update: (id, data) => put(`/tournaments/${id}`, data),
    delete: (id) => del(`/tournaments/${id}`)
  };

  const games = {
    getAll: (filters) => get('/games', filters),
    getById: (id) => get(`/games/${id}`),
    getDetails: (id) => get(`/games/${id}/details`),
    create: (data) => post('/games', data),
    update: (id, data) => put(`/games/${id}`, data),
    delete: (id) => del(`/games/${id}`)
  };

  const moves = {
    getAll: (filters) => get('/moves', filters),
    getById: (id) => get(`/moves/${id}`),
    create: (data) => post('/moves', data),
    update: (id, data) => put(`/moves/${id}`, data),
    delete: (id) => del(`/moves/${id}`)
  };

  const reviews = {
    getAll: (filters) => get('/reviews', filters),
    getById: (id) => get(`/reviews/${id}`),
    create: (data) => post('/reviews', data),
    update: (id, data) => put(`/reviews/${id}`, data),
    delete: (id) => del(`/reviews/${id}`)
  };

  const participants = {
    getAll: (filters) => get('/tournament-participants', filters),
    getById: (id) => get(`/tournament-participants/${id}`),
    create: (data) => post('/tournament-participants', data),
    update: (id, data) => put(`/tournament-participants/${id}`, data),
    delete: (id) => del(`/tournament-participants/${id}`)
  };

  return {
    auth,
    users,
    tournaments,
    games,
    moves,
    reviews,
    participants
  };
})();

