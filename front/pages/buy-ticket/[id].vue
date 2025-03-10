<template>
  <div class="p-6 bg-gray-100 min-h-screen">
    <NuxtLink to="/" class="text-blue-500 underline hover:text-blue-700 mb-6 inline-block">
      ⬅ Volver
    </NuxtLink>

    <!-- Mostrar el nombre de la película seleccionada -->
    <div v-if="movie" class="mt-6 p-8 border rounded-lg shadow-xl bg-white max-w-4xl mx-auto">
      <h1 class="text-4xl font-bold text-gray-800 mb-6">{{ movie.title }}</h1>
    </div>

    <!-- Selector de fecha -->
    <div class="mt-8 p-6 border rounded-lg shadow-xl bg-white max-w-4xl mx-auto">
      <h2 class="text-3xl font-semibold text-gray-800 mb-4">Selecciona la Fecha</h2>
      <input
        type="date"
        v-model="selectedDate"
        class="p-2 border rounded-md w-full mb-4"
        required
        @change="fetchSessions"
      />
    </div>

    <!-- Mostrar las sesiones disponibles -->
    <div v-if="sessions.length > 0" class="mt-8 p-6 border rounded-lg shadow-xl bg-white max-w-4xl mx-auto">
      <h2 class="text-3xl font-semibold text-gray-800 mb-4">Selecciona una Sesión</h2>
      <select v-model="selectedSession" class="p-2 border rounded-md w-full mb-4" required>
        <option v-for="session in sessions" :key="session.id" :value="session.id">
          {{ session.session_time }}
        </option>
      </select>

      <!-- Botón para ver las butacas disponibles -->
      <div v-if="selectedSession" class="mt-4">
        <button @click="fetchSeats" class="bg-green-500 text-white font-bold py-2 px-6 rounded-lg hover:bg-green-700">
          Ver Butacas Disponibles
        </button>
      </div>
    </div>

    <!-- Mostrar las butacas disponibles -->
    <div v-if="seatsData.length > 0" class="mt-8 grid grid-cols-5 gap-4">
      <div v-for="butaca in seatsData" :key="butaca.id" class="p-2">
        <button 
          :class="{
            'bg-green-500': butaca.estado === 'disponible',
            'bg-red-500': butaca.estado === 'confirmada'
          }"
          class="w-full text-white p-2 rounded"
          :disabled="butaca.estado === 'confirmada'"
          @click="selectSeat(butaca)">
          {{ butaca.fila }}{{ butaca.columna }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import communicationManager from '../../services/communicationManager';

const route = useRoute();
const movieId = route.params.id;

const movie = ref(null);
const sessions = ref([]);
const seatsData = ref([]);
const selectedSession = ref(null);
const selectedDate = ref('');

// Obtener la película
async function fetchMovie() {
  try {
    movie.value = await communicationManager.getMovieById(movieId);
  } catch (error) {
    console.error('Error fetching movie details:', error);
  }
}

// Obtener las sesiones de la película para un día específico
async function fetchSessions() {
  try {
    if (selectedDate.value) {
      const sessionData = await communicationManager.getSessionsByMovieAndDate(movieId, selectedDate.value);
      sessions.value = sessionData;
    }
  } catch (error) {
    console.error('Error fetching sessions:', error);
  }
}

// Obtener las butacas de la sesión seleccionada
async function fetchSeats() {
  try {
    if (selectedSession.value) {
      seatsData.value = await communicationManager.getButacasPorSesion(selectedSession.value);
    }
  } catch (error) {
    console.error('Error fetching seats:', error);
  }
}

// Función para seleccionar un asiento
function selectSeat(butaca) {
  console.log('Butaca seleccionada:', butaca);
}

onMounted(() => {
  fetchMovie();
});
</script>
