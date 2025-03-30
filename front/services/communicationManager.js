import axios from 'axios';

// Crear un cliente de API con configuración básica
const apiClient = axios.create({
  //baseURL: 'http://mdvd.daw.inspedralbes.cat/back/public/api',  
  baseURL: 'http://mdvdback.daw.inspedralbes.cat/api',  

  headers: {
    'Content-Type': 'application/json', 
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

  getCurrentUser() {
    return apiClient.get('/user', { // Suponiendo que el backend tiene una ruta "/user" que devuelve el usuario autenticado
      headers: { 'Authorization': `Bearer ${getAuthToken()}` }
    })
    .then(response => response.data)
    .catch(error => {
      console.error('Error obteniendo usuario autenticado:', error);
      throw error;
    });
  },
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


  // Método para crear una película
  createMovie(movieData) {
    const token = getAuthToken();
    if (!token) {
      throw new Error('No se encontró el token de autenticación. Inicia sesión.');
    }

    // Hacemos el request POST a la API
    return apiClient
      .post('/movies', movieData, {
        headers: {
          'Authorization': `Bearer ${token}`,
        },
      })
      .then(response => {
        return response.data;  // Devuelve los datos de la película creada
      })
      .catch(error => {
        console.error('Error al crear la película:', error);
        throw error; // Lanza el error para que lo manejes donde llames a la función
      });
  },
// Obtener todas las películas
getMovies() {
  const token = getAuthToken();  // Obtener el token de autenticación
  if (!token) {
    throw new Error('No se encontró el token de autenticación. Inicia sesión.');
  }

  return apiClient.get('/movies', {
    headers: {
      'Authorization': `Bearer ${token}`,  // Añadir el token en los headers
    },
  })
  .then(response => response.data)
  .catch(error => {
    console.error('Error al obtener las películas:', error);
    throw error;
  });
},


deleteMovie(movieId) {
  const token = getAuthToken();
  if (!token) {
    return Promise.reject(new Error('No se encontró el token de autenticación. Inicia sesión.'));
  }

  return apiClient.delete(`/movies/${movieId}`, {
    headers: { 
      'Authorization': `Bearer ${token}`,
      'Content-Type': 'application/json'
    },
  })
  .then(response => {
    if (response.status !== 200) {
      throw new Error(response.data.message || 'Error al eliminar la película');
    }
    return response.data;
  })
  .catch(error => {
    console.error('Error en deleteMovie:', error);
    throw error;
  });
},
// Método para actualizar una película
updateMovie(movieId, movieData) {
  const token = getAuthToken();
  if (!token) {
    throw new Error('No se encontró el token de autenticación. Inicia sesión.');
  }

  return apiClient
    .put(`/movies/${movieId}`, movieData, {
      headers: {
        'Authorization': `Bearer ${token}`,
      },
    })
    .then(response => response.data)
    .catch(error => {
      console.error('Error al actualizar la película:', error);
      throw error; // Lanza el error para que lo manejes donde llames a la función
    });
},
// Modificar esta función para actualizar el estado de streaming
updateStreamingStatus(movieId, data) {
  const token = getAuthToken();
  if (!token) {
    throw new Error('No se encontró el token de autenticación. Inicia sesión.');
  }

  return apiClient.patch(`/movies/${movieId}/update-streaming-status`, data, {
    headers: {
      'Authorization': `Bearer ${token}`,
    },
  })
  .then(response => response.data)
  .catch(error => {
    console.error('Error al actualizar el estado de streaming:', error);
    throw error;
  });
},


getAllMovies() {
  const token = getAuthToken();  // Obtener el token de autenticación desde el localStorage
  if (!token) {
    throw new Error('No se encontró el token de autenticación. Inicia sesión.');
  }

  // Hacer la solicitud GET a la API para obtener todas las películas
  return apiClient.get('/movies/all', {  // Esto supone que la ruta es '/movies/all'
    headers: {
      'Authorization': `Bearer ${token}`,  // Incluir el token de autenticación en la cabecera
    },
  })
  .then(response => {
    console.log("Películas obtenidas:", response.data);
    return response.data;  // Devuelve las películas
  })
  .catch(error => {
    console.error("Error al obtener las películas:", error);
    // Si el error es 401, el token podría ser inválido o haber expirado
    if (error.response && error.response.status === 401) {
      console.error('Token inválido o expirado. Inicia sesión nuevamente.');
    }
    throw error;  // Lanza el error para que pueda ser manejado en el frontend
  });

},
createSession(sessionData) {
  const token = getAuthToken();
  if (!token) {
    throw new Error('No se encontró el token de autenticación. Inicia sesión.');
  }

  return apiClient.post('/sessions', sessionData, {
    headers: {
      'Authorization': `Bearer ${token}`,
    },
  })
  .then(response => response.data)
  .catch(error => {
    console.error('Error al crear la sesión:', error);
    throw error;
  });
},
getSessionsByMovie(movieId) {
  const token = localStorage.getItem('token'); // O el método que uses para almacenar el token
  return apiClient.get(`/sessions/movie/${movieId}`, {
    headers: {
      Authorization: `Bearer ${token}`
    }
  })
  .then(response => response.data)
  .catch(error => {
    console.error('Error obteniendo sesiones para la película:', error);
    throw error;
  });
},

updateSession(sessionId, sessionData) {
  const token = getAuthToken();
  if (!token) {
    throw new Error('No se encontró el token de autenticación. Inicia sesión.');
  }

  return apiClient.put(`/sessions/${sessionId}`, sessionData, {
    headers: {
      'Authorization': `Bearer ${token}`,
    },
  })
  .then(response => response.data)
  .catch(error => {
    console.error('Error al actualizar la sesión:', error);
    throw error;
  });
},
deleteSession(sessionId) {
  const token = getAuthToken();
  if (!token) {
    throw new Error('No se encontró el token de autenticación. Inicia sesión.');
  }

  return apiClient.delete(`/sessions/${sessionId}`, {
    headers: { 'Authorization': `Bearer ${token}` },
  })
  .then(response => response.data)
  .catch(error => {
    console.error('Error al eliminar la sesión:', error);
    throw error;
  });
},
// Método para añadir una nueva sesión con el movieId en la URL
addSession(movieId, sessionData) {
  const token = getAuthToken();
  if (!token) {
    throw new Error('No se encontró el token de autenticación. Por favor, inicia sesión.');
  }

  // Modificar la URL para incluir el movieId
  return apiClient.post(`/sessions/${movieId}`, sessionData, {
    headers: {
      'Authorization': `Bearer ${token}`,
    },
  })
    .then(response => response.data)
    .catch(error => {
      console.error('Error añadiendo la sesión:', error);
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
reservarButaca(movieSessionId, butacaIds) {
  const token = getAuthToken();
  if (!token) {
    throw new Error('No se encontró el token de autenticación. Por favor, inicia sesión.');
  }

  return apiClient.post('/reservar-butaca', {
    movie_session_id: movieSessionId,
    butaca_ids: butacaIds,
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


  
 // Eliminar una reserva
 eliminarReserva(reservaId) {
  const token = getAuthToken();
  if (!token) {
    throw new Error('No se encontró el token de autenticación. Inicia sesión.');
  }

  return apiClient.delete(`/reservas/${reservaId}`, {
    headers: { 'Authorization': `Bearer ${token}` },
  })
  .then(response => response.data)
  .catch(error => {
    console.error('Error al eliminar la reserva:', error);
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

  realizarPago(pagoData) {
    const token = getAuthToken();
    if (!token) {
      throw new Error('No se encontró el token de autenticación. Por favor, inicia sesión.');
    }
  
    return apiClient.post('/realizar-pago', pagoData, {
      headers: {
        'Authorization': `Bearer ${token}`,
      },
    })
    .then(response => response.data)
    .catch(error => {
      console.error('Error realizando el pago:', error);
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
  getOcupacionByDate(movieId, sessionDate, sessionTime) {
    const token = getAuthToken(); // Obtener token de autenticación
    if (!token) {
      console.error('No se encontró el token de autenticación. Inicia sesión.');
      throw new Error('No se encontró el token de autenticación. Inicia sesión.');
    }
  
    if (!movieId || !sessionDate || !sessionTime) {
      console.error('Parámetros inválidos para obtener la ocupación.');
      throw new Error('Faltan parámetros necesarios (movieId, sessionDate, sessionTime).');
    }
  
    // Formatear la fecha a YYYY-MM-DD
    const formattedDate = new Date(sessionDate).toISOString().split('T')[0];
  
    // Asegurar que sessionTime esté en formato HH:MM:SS
    const formattedTime = sessionTime.length === 5 ? `${sessionTime}:00` : sessionTime;
  
    return apiClient.get(`/sesion/resumen?session_date=${formattedDate}&session_time=${formattedTime}`, {
      headers: { 'Authorization': `Bearer ${token}` },
    })
      .then(response => {
        const { entradas_normal, entradas_vip, recaudacion_normal, recaudacion_vip, recaudacion_total, butacas } = response.data;
  
        console.log("Resumen de sesión:", {
          entradas_normal,
          entradas_vip,
          recaudacion_normal,
          recaudacion_vip,
          recaudacion_total,
          butacas
        });
  
        return {
          entradasNormal: entradas_normal,
          entradasVIP: entradas_vip,
          recaudacionNormal: recaudacion_normal,
          recaudacionVIP: recaudacion_vip,
          recaudacionTotal: recaudacion_total,
          butacas
        };
      })
      .catch(error => {
        console.error('Error obteniendo ocupación para la fecha y película:', error.response?.data?.message || error.message);
        throw error;
      });
  },

  // Método para obtener el rol del usuario autenticado
getUserRole() {
  const token = getAuthToken();
  if (!token) {
    throw new Error('No se encontró el token de autenticación. Inicia sesión.');
  }

  return apiClient.get('/user-role', {
    headers: { 'Authorization': `Bearer ${token}` },
  })
  .then(response => response.data.role) // Suponiendo que el backend devuelve { role: "admin" }
  .catch(error => {
    console.error('Error obteniendo el rol del usuario:', error);
    throw error;
  });
},

};

export default communicationManager;
