import axios from 'axios';

// Crear un cliente de API con configuración básica
const apiClient = axios.create({
  baseURL: 'http://127.0.0.1:8000/api',  // El prefijo base para las rutas de la API
  headers: {
    'Content-Type': 'application/json', // Asegúrate de que el servidor acepte JSON
  },
  withCredentials: true,  // Si necesitas enviar cookies
});

function getAuthToken() {
  return localStorage.getItem('auth_token');
}

function setAuthToken(token) {
  if (token) {
    apiClient.defaults.headers['Authorization'] = `Bearer ${token}`;
  } else {
    delete apiClient.defaults.headers['Authorization'];
  }
}
// El manager de comunicación con los fetches
const communicationManager = {
  // Método para registrar un nuevo usuario
  registerUser(userData) {
    return apiClient.post('/register', userData)
      .then(response => {
        const { auth_token } = response.data;
        if (auth_token) {
          localStorage.setItem('auth_token', auth_token);
          setAuthToken(auth_token); // Establecemos el token en axios
        }
        return response.data;
      })
      .catch(error => {
        console.error('Error registering user:', error);
        throw error;
      });
  },

  // Método para login
  loginUser(credentials) {
    return apiClient.post('/login', credentials)
      .then(response => {
        const { auth_token } = response.data;
        if (auth_token) {
          localStorage.setItem('auth_token', auth_token);
          setAuthToken(auth_token); // Establecemos el token en axios
        }
        return response.data;
      })
      .catch(error => {
        console.error('Error logging in:', error);
        throw error;
      });
  },

  // Método para hacer logout
  logout() {
    localStorage.removeItem('auth_token');
    setAuthToken(null); // Eliminamos el token del header de axios
  },

  // Obtener sesiones de una película para una fecha específica
  getSessionsByMovieAndDate(movieId, sessionDate) {
    return apiClient.get(`/sessions/movie/${movieId}/date/${sessionDate}`)
      .then(response => response.data)
      .catch(error => {
        console.error('Error fetching sessions for movie and date:', error);
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


  // Obtener un usuario por ID
  getUserById(userId) {
    return apiClient.get(`/users/${userId}`)
      .then(response => response.data)
      .catch(error => {
        console.error('Error fetching user:', error);
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

  

 

  // Obtener todas las películas
  getMovies() {
    return apiClient.get('/movies')
      .then(response => response.data)
      .catch(error => {
        console.error('Error fetching movies:', error);
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


  // Obtener una sesión de cine por ID
  getSessionById(sessionId) {
    return apiClient.get(`/sessions/${sessionId}`)
      .then(response => response.data)
      .catch(error => {
        console.error('Error fetching session:', error);
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

  // Obtener compra por ID
  getCompraById(compraId) {
    return apiClient.get(`/compras/${compraId}`)
      .then(response => response.data)
      .catch(error => {
        console.error('Error fetching purchase:', error);
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



// Método para reservar una butaca
reservarButaca(movieSessionId, butacaId) {
  const token = getAuthToken();
  if (!token) {
    throw new Error('No se encontró el token de autenticación. Por favor, inicia sesión.');
  }

  return apiClient.post('/reservar-butaca', {
    movie_session_id: movieSessionId,
    butaca_id: butacaId,
  }, {
    headers: {
      'Authorization': `Bearer ${token}`,
    },
  })
  .then(response => response.data)
  .catch(error => {
    console.error('Error reservando la butaca:', error);
    throw error;
  });
},

  // Liberar una butaca reservada
  liberarButaca(sessionId, butacaId) {
    const token = getAuthToken();
    if (!token) {
      throw new Error('No se encontró el token de autenticación. Inicia sesión.');
    }

    return apiClient.delete(`/${sessionId}/${butacaId}/liberar`, {
      headers: { 'Authorization': `Bearer ${token}` },
    })
      .then(response => response.data)
      .catch(error => {
        console.error('Error al liberar la butaca:', error);
        throw error;
      });
  },

  // Comprar una reserva de butaca
  comprarReserva(reservaId) {
    const token = getAuthToken();
    if (!token) {
      throw new Error('No se encontró el token de autenticación. Inicia sesión.');
    }

    return apiClient.post(`/comprar-reserva/${reservaId}`, {}, {
      headers: { 'Authorization': `Bearer ${token}` },
    })
      .then(response => response.data)
      .catch(error => {
        console.error('Error al comprar la reserva:', error);
        throw error;
      });
  },

  //  Realizar un pago
  realizarPago(datosPago) {
    const token = getAuthToken();
    if (!token) {
        throw new Error('No se encontró el token de autenticación. Inicia sesión.');
    }

    if (!datosPago.numero_tarjeta || !datosPago.fecha_vencimiento || !datosPago.cvv || !datosPago.compra_id) {
        console.error("❌ Error: Falta información en el pago.", datosPago);
        throw new Error("Datos de pago incompletos.");
    }

    return apiClient.post('/realizar-pago', datosPago, {
        headers: { 'Authorization': `Bearer ${token}` },
    })
    .then(response => response.data)
    .catch(error => {
        console.error('❌ Error al realizar el pago:', error.response ? error.response.data : error);
        throw error;
    });
},

  
  // Agregar una butaca al carrito
  agregarAlCarrito(datosCarrito) {
    const token = getAuthToken();
    if (!token) {
      throw new Error('No se encontró el token de autenticación. Inicia sesión.');
    }

    return apiClient.post('/agregar-al-carrito', datosCarrito, {
      headers: { 'Authorization': `Bearer ${token}` },
    })
      .then(response => response.data)
      .catch(error => {
        console.error('Error al agregar al carrito:', error);
        throw error;
      });
  },
  getCarrito() {
    const token = getAuthToken();
    if (!token) {
      throw new Error('No se encontró el token de autenticación. Por favor, inicia sesión.');
    }
  
    return apiClient.get('/ver-carrito', {
      headers: {
        'Authorization': `Bearer ${token}`,
      },
    })
    .then(response => response.data)
    .catch(error => {
      console.error('Error obteniendo el carrito:', error);
      throw error;
    });
  },
  
  // Ver el contenido del carrito
  verCarrito() {
    const token = getAuthToken();
    if (!token) {
      throw new Error('No se encontró el token de autenticación. Inicia sesión.');
    }

    return apiClient.get('/ver-carrito', {
      headers: { 'Authorization': `Bearer ${token}` },
    })
      .then(response => response.data)
      .catch(error => {
        console.error('Error al ver el carrito:', error);
        throw error;
      });
  },

  // Confirmar compra del carrito
  confirmarCompra() {
    const token = getAuthToken();
    if (!token) {
      throw new Error('No se encontró el token de autenticación. Inicia sesión.');
    }

    return apiClient.post('/confirmar-compra', {}, {
      headers: { 'Authorization': `Bearer ${token}` },
    })
      .then(response => response.data)
      .catch(error => {
        console.error('Error al confirmar la compra:', error);
        throw error;
      });
  },

};

export default communicationManager;
