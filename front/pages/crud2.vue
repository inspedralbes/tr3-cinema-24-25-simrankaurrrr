<script setup>
import { ref, onMounted, watch } from 'vue';
import communicationManager from '~/services/communicationManager';
import { useRoute, useRouter } from 'vue-router';

// Variables reactivas
const router = useRouter();
const user = ref(null);
const movies = ref([]);
const selectedMovieId = ref(null);
const streamingStatus = ref(null);
const loading = ref(false);
const sessions = ref([]);
const newSessionDate = ref('');
const newSessionTime = ref('');
const isDiaEspectador = ref(false); // Asegúrate de incluir esta variable reactiva para el checkbox
const isVisible = ref(false);
const message = ref('');

// Verificar el rol del usuario
const checkUserRole = async () => {
  try {
    const currentUser = await communicationManager.getCurrentUser();
    user.value = currentUser;
    if (user.value?.role === 'admin') {
      fetchMovies();
    }
  } catch (error) {
    console.error('Error obteniendo usuario:', error);
    user.value = null;
  }
};

// Función para mostrar la alerta
const showAlert = (msg) => {
  message.value = msg;
  isVisible.value = true;
  setTimeout(() => {
    closeAlert();
  }, 3000); // Cierra la alerta después de 3 segundos
};

// Función para cerrar la alerta
const closeAlert = () => {
  isVisible.value = false;
};

// Obtener todas las películas
const fetchMovies = async () => {
  try {
    const moviesData = await communicationManager.getAllMovies();
    movies.value = moviesData;
  } catch (error) {
    console.error('Error obteniendo películas:', error);
  }
};

// Obtener sesiones para la película seleccionada
const fetchSessions = async () => {
  if (!selectedMovieId.value) return;  // Evita hacer el fetch si no se ha seleccionado una película
  loading.value = true;
  try {
    const sessionsData = await communicationManager.getSessionsByMovie(selectedMovieId.value);
    sessions.value = sessionsData.map(session => ({
      id: session.id,
      session_date: session.session_date,
      session_time: session.session_time,
    }));
  } catch (error) {
    console.error('Error obteniendo sesiones:', error);
    sessions.value = [];
  } finally {
    loading.value = false;
  }
};

const addSession = async () => {
  if (!newSessionDate.value || !newSessionTime.value) {
    alert('Por favor, ingresa una fecha y una hora válidas');
    return;
  }

  const newSession = {
    session_date: newSessionDate.value,  
    session_time: newSessionTime.value,  
    dia_espectador: isDiaEspectador.value,  // Asegúrate de usar la variable correcta
  };

  try {
    const createdSession = await communicationManager.addSession(selectedMovieId.value, newSession);
    sessions.value.push(createdSession);

    // Limpiar los campos del formulario
    newSessionDate.value = '';
    newSessionTime.value = '';

    showAlert('Sesión añadida correctamente');
  } catch (error) {
    console.error('Error añadiendo sesión:', error);

    if (error.response && error.response.data.error === 'La película no está disponible para sesiones en streaming') {
      showAlert('No se puede añadir la sesión. Esta película no está disponible para streaming.');
    } else {
      showAlert('Error al añadir la sesión');
    }
  }
};

const fetchStreamingStatus = async () => {
  if (!selectedMovieId.value) return;

  const selectedMovie = movies.value.find(movie => movie.id === selectedMovieId.value);

  if (selectedMovie && selectedMovie.hasOwnProperty('disponible_en_streaming')) {
    console.log("Estado de Streaming de la película seleccionada: ", selectedMovie.disponible_en_streaming);
    streamingStatus.value = selectedMovie.disponible_en_streaming;
  } else {
    console.error("No se encontró el estado de streaming para esta película");
    streamingStatus.value = null;
  }
};

const deleteSession = async (sessionId) => {
  try {
    await communicationManager.deleteSession(sessionId);
    sessions.value = sessions.value.filter(session => session.id !== sessionId);
    showAlert('Sesión eliminada correctamente');
  } catch (error) {
    console.error('Error eliminando sesión:', error);
    showAlert('Error al eliminar la sesión');
  }
};

const updateStreamingStatus = async () => {
  try {
    loading.value = true;
    const newStatus = streamingStatus.value === 1 ? 0 : 1;
    await communicationManager.updateStreamingStatus(selectedMovieId.value, { disponible_en_streaming: newStatus });

    streamingStatus.value = newStatus; // Actualiza el valor de streamingStatus para reflejar el cambio
    showAlert('Estado actualizado correctamente');
  } catch (error) {
    console.error('Error actualizando estado:', error);
    showAlert('Error al actualizar el estado');
  } finally {
    loading.value = false;
  }

};
function goBack() {
  router.go(-1); // Navega a la página anterior
};
watch(selectedMovieId, async () => {
  console.log("ID de película seleccionada:", selectedMovieId.value);
  await fetchStreamingStatus();
  fetchSessions();
});

// Cargar datos al montar el componente
onMounted(() => {
  checkUserRole();
});

defineExpose({
  showAlert
});
</script>
<template>
  <Navbar />

  <button @click="goBack" class="back-link">
      ⬅ Tornar
    </button>

  <NuxtLink to="crud1" class="crud-link">
    Consulta Administrativa
  </NuxtLink>
  <NuxtLink to="crud3" class="crud-link">
    Administrar Pel·lícules
  </NuxtLink>

  <div v-if="user?.role === 'admin'" class="p-6">
    <h1 class="text-2xl font-bold mb-4">Administrar Pel·lícules i Sessions</h1>
    <div>
      <label for="movie" class="text-lg">Selecciona una Pel·lícula:</label>
      <select v-model="selectedMovieId" class="border px-4 py-2 rounded w-full">
        <option v-for="movie in movies" :key="movie.id" :value="movie.id">
          {{ movie.title }}
        </option>
      </select>
    </div>

    <div v-if="selectedMovieId !== null" class="mt-4">
      <p class="text-lg">Estat actual: 
        <strong>{{ streamingStatus === 1 ? 'Disponible' : 'No Disponible' }}</strong>
      </p>

      <button @click="updateStreamingStatus" :disabled="loading">
        Actualitzar estat
      </button>
    </div>

    <div v-if="selectedMovieId !== null" class="mt-6">
      <h2 class="text-xl font-semibold">Afegir nova sessió</h2>
      <form @submit.prevent="addSession">
        <div class="mb-4">
          <label for="sessionDate" class="text-lg">Data de la sessió:</label>
          <input type="date" v-model="newSessionDate" class="border px-4 py-2 rounded w-full">
        </div>
        <div class="mb-4">
          <label for="sessionTime" class="text-lg">Hora de la sessió:</label>
          <select v-model="newSessionTime" class="border px-4 py-2 rounded w-full">
            <option value="" disabled>Selecciona una hora</option>
            <option value="16:00:00">16:00</option>
            <option value="18:00:00">18:00</option>
            <option value="20:00:00">20:00</option>
          </select>
        </div>
        <div class="mb-4">
          <label for="dia_espectador">És dia espectador?</label>
          <input v-model="isDiaEspectador" type="checkbox" />
        </div>
        <button type="submit">
          Afegir sessió
        </button>
      </form>
    </div>

    <div v-if="sessions.length > 0" class="mt-6">
      <h2 class="text-xl font-semibold">Sessions disponibles</h2>
      <ul class="list-disc ml-6">
        <li v-for="session in sessions" :key="session.id" class="flex justify-between items-center">
          <span>{{ session.session_date }} - {{ session.session_time }}</span>
          <button @click="deleteSession(session.id)" class="bg-red-500 text-white px-4 py-1 rounded hover:bg-red-600" aria-label="Eliminar sessió">
            Eliminar
          </button>
        </li>
      </ul>
    </div>

    <div v-if="loading" class="mt-6 text-blue-500 text-lg">
      Carregant sessions...
    </div>

    <div v-else-if="selectedMovieId !== null && sessions.length === 0 && !loading" class="mt-6 text-gray-500 text-lg">
      No hi ha sessions disponibles per a aquesta pel·lícula.
    </div>
  </div>

  <div v-else class="text-center p-6 text-red-500 text-xl font-bold">
    No tens permisos per veure aquesta secció.
  </div>

  <!-- Popup d'alerta -->
  <div v-if="isVisible" class="alert-popup">
    <div class="alert-content">
      <p class="alert-message">{{ message }}</p>
      <button @click="closeAlert" class="close-btn">Tancar</button>
    </div>
  </div>
</template>



<style scoped>
.back-link {
  display: inline-block;
  padding: 10px 20px;
  background-color: #2b2d42;
  color: #8d99ae;
  border: 2px solid #8d99ae;
  border-radius: 8px;
  text-decoration: none;
  font-weight: bold;
  margin: 20px;
  transition: all 0.3s;
}

.back-link:hover {
  color: #ef233c;
  border-color: #ef233c;
}

.crud-link {
  display: inline-block;
  padding: 10px 20px;
  background-color: #2b2d42;
  color: #edf2f4;
  border-radius: 8px;
  text-decoration: none;
  font-weight: bold;
  margin: 10px;
  transition: all 0.3s;
}

.crud-link:hover {
  background-color: #ef233c;
}

.p-6 {
  padding: 1.5rem;
  background-color: #edf2f4;
  border-radius: 8px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  margin: 20px;
  color: #2b2d42;
}

h1, h2 {
  color: #2b2d42;
  margin-bottom: 20px;
}

select, input[type="date"] {
  background-color: #edf2f4;
  border: 2px solid #8d99ae;
  padding: 10px;
  border-radius: 4px;
  width: 100%;
  margin-bottom: 15px;
  color: #2b2d42;
}

select:focus, input[type="date"]:focus {
  border-color: #ef233c;
  outline: none;
}

button {
  padding: 10px 20px;
  background-color: #2b2d42;
  color: #edf2f4;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-weight: bold;
  margin: 5px;
  transition: all 0.3s;
}

button:hover {
  background-color: #ef233c;
  transform: translateY(-2px);
}

button:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

ul {
  list-style: none;
  padding: 0;
}

li {
  padding: 10px;
  margin: 5px 0;
  background-color: #8d99ae;
  color: #edf2f4;
  border-radius: 4px;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.alert-popup {
  position: fixed;
  top: 20px;
  left: 50%;
  transform: translateX(-50%);
  background-color: #ef233c;
  color: #edf2f4;
  padding: 15px 25px;
  border-radius: 8px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
  z-index: 1000;
  animation: fadeIn 0.3s ease-out;
}

.close-btn {
  background: none;
  border: none;
  color: #edf2f4;
  font-size: 1.2rem;
  cursor: pointer;
  margin-left: 15px;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(-20px) translateX(-50%);
  }
  to {
    opacity: 1;
    transform: translateY(0) translateX(-50%);
  }
}

.text-red-500 {
  color: #d80032;
}

@media (max-width: 768px) {
  .p-6 {
    margin: 10px;
    padding: 1rem;
  }
  
  li {
    flex-direction: column;
    align-items: flex-start;
  }
  
  button {
    margin-top: 10px;
    width: 100%;
  }
}
</style>