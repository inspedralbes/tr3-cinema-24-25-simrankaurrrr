<script setup>
import { ref, onMounted, watch } from 'vue';
import communicationManager from '~/services/communicationManager';

// Variables reactivas
const user = ref(null);
const movies = ref([]);
const selectedMovieId = ref(null);
const streamingStatus = ref(null);
const loading = ref(false);
const sessions = ref([]);
const newSessionDate = ref('');
const newSessionTime = ref('');
const isDiaEspectador = ref(false); // Asegúrate de incluir esta variable reactiva para el checkbox

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
    // Ahora pasas `selectedMovieId.value` como `movieId`
    const createdSession = await communicationManager.addSession(selectedMovieId.value, newSession);
    sessions.value.push(createdSession);

    // Limpiar los campos del formulario
    newSessionDate.value = '';
    newSessionTime.value = '';

    alert('Sesión añadida correctamente');
  } catch (error) {
    console.error('Error añadiendo sesión:', error);

    // Aquí verificamos si el error tiene un mensaje relacionado con la disponibilidad de streaming
    if (error.response && error.response.data.error === 'La película no está disponible para sesiones en streaming') {
      alert('No se puede añadir la sesión. Esta película no está disponible para streaming.');
    } else {
      alert('Error al añadir la sesión');
    }
  }
};

const fetchStreamingStatus = async () => {
  if (!selectedMovieId.value) return;

  const selectedMovie = movies.value.find(movie => movie.id === selectedMovieId.value);

  // Verificar si se encuentra la película y si tiene disponible_en_streaming
  if (selectedMovie && selectedMovie.hasOwnProperty('disponible_en_streaming')) {
    console.log("Estado de Streaming de la película seleccionada: ", selectedMovie.disponible_en_streaming);
    streamingStatus.value = selectedMovie.disponible_en_streaming;
  } else {
    console.error("No se encontró el estado de streaming para esta película");
    streamingStatus.value = null;  // Fallback en caso de que no se encuentre el dato
  }
};

// Actualizar el estado de streaming en el backend y en el frontend
const updateStreamingStatus = async () => {
  try {
    loading.value = true;
    const newStatus = streamingStatus.value === 1 ? 0 : 1;
    await communicationManager.updateStreamingStatus(selectedMovieId.value, { disponible_en_streaming: newStatus });
    
    // Actualizamos el estado en el frontend para reflejar el cambio
    streamingStatus.value = newStatus; // Actualiza el valor de streamingStatus para reflejar el cambio
    alert('Estado actualizado correctamente');
  } catch (error) {
    console.error('Error actualizando estado:', error);
    alert('Error al actualizar el estado');
  } finally {
    loading.value = false;
  }
};

watch(selectedMovieId, async () => {
  console.log("ID de película seleccionada:", selectedMovieId.value); // Verifica el id seleccionado

  await fetchStreamingStatus();  // Actualiza el estado de streaming cuando cambie la película
  fetchSessions();                // Asegúrate de que también se carguen las sesiones
});


// Cargar datos al montar el componente
onMounted(() => {
  checkUserRole();
});
</script>


<template>
      <Navbar />

  <NuxtLink to="/" class="text-blue-500 underline hover:text-blue-700 mb-6 inline-block">
      ⬅ Volver
    </NuxtLink>
  <NuxtLink to="crud1" class="login-link">
            CRUD 1
          </NuxtLink>
          <NuxtLink to="crud3" class="login-link">
            CRUD 3
          </NuxtLink>
  <div v-if="user?.role === 'admin'" class="p-6">
    <h1 class="text-2xl font-bold mb-4">Administrar Películas</h1>
    <div>
      <label for="movie" class="text-lg">Selecciona una Película:</label>
      <select v-model="selectedMovieId" class="border px-4 py-2 rounded w-full">
        <option v-for="movie in movies" :key="movie.id" :value="movie.id">
          {{ movie.title }}
        </option>
      </select>
    </div>

    <div v-if="selectedMovieId !== null" class="mt-4">
      <p class="text-lg">Estado actual: 
  <strong>{{ streamingStatus === 1 ? 'Disponible' : 'No Disponible' }}</strong>
</p>

      <button 
        @click="updateStreamingStatus" >
        Update status      </button>
    </div>

    <!-- Formulario para añadir una nueva sesión -->
    <div v-if="selectedMovieId !== null" class="mt-6">
      <h2 class="text-xl font-semibold">Añadir nueva sesión</h2>
      <form @submit.prevent="addSession">
        <div class="mb-4">
          <label for="sessionDate" class="text-lg">Fecha de la sesión:</label>
          <input type="date" v-model="newSessionDate" class="border px-4 py-2 rounded w-full">
        </div>
        <div class="mb-4">
          <label for="sessionTime" class="text-lg">Hora de la sesión:</label>
          <select v-model="newSessionTime" class="border px-4 py-2 rounded w-full">
            <option value="" disabled>Selecciona una hora</option>
            <option value="16:00:00">16:00</option>
            <option value="18:00:00">18:00</option>
            <option value="20:00:00">20:00</option>
          </select>
        </div>
        <div class="mb-4">
          <label for="dia_espectador">¿Es día espectador?</label>
          <input v-model="isDiaEspectador" type="checkbox" />
        </div>
        <button type="submit" class="bg-blue-500 text-white px-6 py-2 rounded hover:bg-blue-600">
          Añadir sesión
        </button>
      </form>
    </div>

    <!-- Mostrar sesiones si hay -->
    <div v-if="sessions.length > 0" class="mt-6">
      <h2 class="text-xl font-semibold">Sesiones disponibles</h2>
      <ul class="list-disc ml-6">
        <li v-for="session in sessions" :key="session.id" class="flex justify-between items-center">
          <span>{{ session.session_date }} - {{ session.session_time }}</span>
          <button 
            @click="deleteSession(session.id)" 
            class="bg-red-500 text-white px-4 py-1 rounded hover:bg-red-600"
            aria-label="Eliminar sesión"
          >
            Eliminar
          </button>
        </li>
      </ul>
    </div>

    <!-- Mostrar mensaje de carga si está cargando -->
    <div v-if="loading" class="mt-6 text-blue-500 text-lg">
      Cargando sesiones...
    </div>

    <!-- Mostrar mensaje si NO hay sesiones pero solo si hay una película seleccionada -->
    <div v-else-if="selectedMovieId !== null && sessions.length === 0 && !loading" class="mt-6 text-gray-500 text-lg">
      No hay sesiones disponibles para esta película.
    </div>
  </div>

  <div v-else class="text-center p-6 text-red-500 text-xl font-bold">
    No tienes permisos para ver esta sección.
  </div>
</template>

<style scoped>
button {
  cursor: pointer;
}

button:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}
</style>
