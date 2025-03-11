import axios from 'axios';

// Crear un cliente de API con configuración básica
const apiClient = axios.create({
  baseURL: 'http://127.0.0.1:8000/api',  // El prefijo base para las rutas de la API
  headers: {
    'Content-Type': 'application/json', // Asegúrate de que el servidor acepte JSON
  },
  withCredentials: true,  // Si necesitas enviar cookies
});

// El manager de comunicación con los fetches
const communicationManager = {
  // Obtener sesiones de una película para una fecha específica
  getSessionsByMovieAndDate(movieId, sessionDate) {
    return apiClient.get(`/sessions/movie/${movieId}/date/${sessionDate}`)
      .then(response => response.data)
      .catch(error => {
        console.error('Error fetching sessions for movie and date:', error);
        throw error;
      });
  },

  // Obtener butacas por sesión
  getButacasPorSesion(sessionId) {
    return apiClient.get(`/butacas/sesion/${sessionId}`)
      .then(response => response.data)
      .catch(error => {
        console.error('Error fetching seats for session:', error);
        throw error;
      });
  },

  // Obtener las sesiones de una película (por película, sin la fecha)
  getSessionsByMovie(movieId) {
    return apiClient.get(`/sessions/movie/${movieId}`)
      .then(response => response.data)
      .catch(error => {
        console.error('Error fetching sessions for movie:', error);
        throw error;
      });
  },

  // Obtener todos los usuarios
  getUsers() {
    return apiClient.get('/users')
      .then(response => response.data)
      .catch(error => {
        console.error('Error fetching users:', error);
        throw error;
      });
  },

  // Crear un nuevo usuario
  createUser(userData) {
    return apiClient.post('/users', userData)
      .then(response => response.data)
      .catch(error => {
        console.error('Error creating user:', error);
        throw error;
      });
  },

  // Obtener un usuario por ID
  getUserById(userId) {
    return apiClient.get(`/users/${userId}`)
      .then(response => response.data)
      .catch(error => {
        console.error('Error fetching user:', error);
        throw error;
      });
  },

  // Actualizar usuario por ID
  updateUser(userId, userData) {
    return apiClient.put(`/users/${userId}`, userData)
      .then(response => response.data)
      .catch(error => {
        console.error('Error updating user:', error);
        throw error;
      });
  },

  // Obtener información de una película por ID
  getMovieById(movieId) {
    return apiClient.get(`/movies/${movieId}`)
      .then(response => response.data)
      .catch(error => {
        console.error('Error fetching movie:', error);
        throw error;
      });
  },

  // Obtener entradas
  getEntradas() {
    return apiClient.get('/entradas')
      .then(response => response.data)
      .catch(error => {
        console.error('Error fetching entries:', error);
        throw error;
      });
  },

  // Eliminar usuario por ID
  deleteUser(userId) {
    return apiClient.delete(`/users/${userId}`)
      .then(response => response.data)
      .catch(error => {
        console.error('Error deleting user:', error);
        throw error;
      });
  },

  // Obtener todas las películas
  getMovies() {
    return apiClient.get('/movies')
      .then(response => response.data)
      .catch(error => {
        console.error('Error fetching movies:', error);
        throw error;
      });
  },

  // Crear una nueva película
  createMovie(movieData) {
    return apiClient.post('/movies', movieData)
      .then(response => response.data)
      .catch(error => {
        console.error('Error creating movie:', error);
        throw error;
      });
  },

  // Actualizar película por ID
  updateMovie(movieId, movieData) {
    return apiClient.put(`/movies/${movieId}`, movieData)
      .then(response => response.data)
      .catch(error => {
        console.error('Error updating movie:', error);
        throw error;
      });
  },

  // Eliminar película por ID
  deleteMovie(movieId) {
    return apiClient.delete(`/movies/${movieId}`)
      .then(response => response.data)
      .catch(error => {
        console.error('Error deleting movie:', error);
        throw error;
      });
  },

  // Obtener todas las sesiones de cine
  getSessions() {
    return apiClient.get('/sessions')
      .then(response => response.data)
      .catch(error => {
        console.error('Error fetching sessions:', error);
        throw error;
      });
  },

  // Crear una nueva sesión de cine
  createSession(sessionData) {
    return apiClient.post('/sessions', sessionData)
      .then(response => response.data)
      .catch(error => {
        console.error('Error creating session:', error);
        throw error;
      });
  },

  // Obtener una sesión de cine por ID
  getSessionById(sessionId) {
    return apiClient.get(`/sessions/${sessionId}`)
      .then(response => response.data)
      .catch(error => {
        console.error('Error fetching session:', error);
        throw error;
      });
  },

  // Actualizar sesión de cine por ID
  updateSession(sessionId, sessionData) {
    return apiClient.put(`/sessions/${sessionId}`, sessionData)
      .then(response => response.data)
      .catch(error => {
        console.error('Error updating session:', error);
        throw error;
      });
  },

  // Eliminar sesión de cine por ID
  deleteSession(sessionId) {
    return apiClient.delete(`/sessions/${sessionId}`)
      .then(response => response.data)
      .catch(error => {
        console.error('Error deleting session:', error);
        throw error;
      });
  },

  // Obtener compras de un usuario por ID
  getComprasPorUsuario(userId) {
    return apiClient.get(`/compras/usuario/${userId}`)
      .then(response => response.data)
      .catch(error => {
        console.error('Error fetching purchases for user:', error);
        throw error;
      });
  },

  // Crear una nueva compra
  createCompra(compraData) {
    return apiClient.post('/compras', compraData)
      .then(response => response.data)
      .catch(error => {
        console.error('Error creating purchase:', error);
        throw error;
      });
  },

  // Obtener compra por ID
  getCompraById(compraId) {
    return apiClient.get(`/compras/${compraId}`)
      .then(response => response.data)
      .catch(error => {
        console.error('Error fetching purchase:', error);
        throw error;
      });
  },

  // Actualizar compra por ID
  updateCompra(compraId, compraData) {
    return apiClient.put(`/compras/${compraId}`, compraData)
      .then(response => response.data)
      .catch(error => {
        console.error('Error updating purchase:', error);
        throw error;
      });
  },

  // Eliminar compra por ID
  deleteCompra(compraId) {
    return apiClient.delete(`/compras/${compraId}`)
      .then(response => response.data)
      .catch(error => {
        console.error('Error deleting purchase:', error);
        throw error;
      });
  },

  // Obtener butacas por sesión
  getButacasPorSesion(sessionId) {
    return apiClient.get(`/butacas/sesion/${sessionId}`)
      .then(response => response.data)
      .catch(error => {
        console.error('Error fetching seats for session:', error);
        throw error;
      });
  },

  // Actualizar el estado de la butaca por ID
  updateButaca(butacaId, estado) {
    return apiClient.put(`/butacas/${butacaId}/estado`, { estado })
      .then(response => response.data)
      .catch(error => {
        console.error('Error updating seat:', error);
        throw error;
      });
  },

  // Eliminar butaca por ID
  deleteButaca(butacaId) {
    return apiClient.delete(`/butacas/${butacaId}`)
      .then(response => response.data)
      .catch(error => {
        console.error('Error deleting seat:', error);
        throw error;
      });
  },
  getButacasPorSesion(sessionId) {
    return apiClient.get(`/butacas/sesion/${sessionId}`)
      .then(response => response.data)
      .catch(error => {
        console.error('Error fetching seats for session:', error);
        throw error;
      });
  },

  updateButaca(butacaId, estado) {
    return apiClient.put(`/butacas/${butacaId}/estado`, { estado })
      .then(response => response.data)
      .catch(error => {
        console.error('Error updating seat:', error);
        throw error;
      });
  },

  deleteButaca(butacaId) {
    return apiClient.delete(`/butacas/${butacaId}`)
      .then(response => response.data)
      .catch(error => {
        console.error('Error deleting seat:', error);
        throw error;
      });
  },
// Método para reservar una butaca
reservarButaca(userId, movieSessionId, butacaId) {
  return apiClient.post('/butacas/reservar-butaca', {
    user_id: userId,
    movie_session_id: movieSessionId,
    butaca_id: butacaId
  })
  .then(response => response.data)
  .catch(error => {
    console.error('Error reservando la butaca:', error);
    throw error;
  });
},
  
};

export default communicationManager;
