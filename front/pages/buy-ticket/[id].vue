<template>
  <div class="p-6 bg-gray-100 min-h-screen">
    <NuxtLink to="/" class="text-blue-500 underline hover:text-blue-700 mb-6 inline-block">
      ⬅ Volver
    </NuxtLink>

    <!-- Mostrar el nombre de la película seleccionada -->
    <div v-if="movie" class="mt-6 p-8 border rounded-lg shadow-xl bg-white max-w-4xl mx-auto">
      <h1 class="text-4xl font-bold text-gray-800 mb-6">{{ movie.title }}</h1>
    </div>

    <!-- Mostrar los días disponibles -->
    <div v-if="availableDates.length > 0" class="mt-8 p-6 border rounded-lg shadow-xl bg-white max-w-4xl mx-auto">
      <h2 class="text-3xl font-semibold text-gray-800 mb-4">Selecciona una Fecha</h2>
      <select v-model="selectedDate" @change="fetchSessionsForDate(selectedDate)"
        class="p-2 border rounded-md w-full mb-4" required>
        <option value="" disabled selected>Selecciona una Fecha</option>
        <option v-for="date in availableDates" :key="date" :value="date">
          {{ formatDate(date) }}
        </option>
      </select>
    </div>

    <!-- Mostrar las sesiones disponibles -->
    <div v-if="sessions.length > 0" class="mt-8 p-6 border rounded-lg shadow-xl bg-white max-w-4xl mx-auto">
      <h2 class="text-3xl font-semibold text-gray-800 mb-4">Selecciona una Sesión</h2>
      <select v-model="selectedSession" @change="fetchSeats" class="p-2 border rounded-md w-full mb-4" required>
        <option value="" disabled selected>Selecciona una Sesión</option>
        <option v-for="session in sessions" :key="session.id" :value="session.id">
          {{ formatTime(session.session_time) }}
        </option>
      </select>
    </div>

    <!-- Mostrar las butacas disponibles -->
    <div v-if="seatsData.length > 0" class="mt-8 p-6 border rounded-lg shadow-xl bg-white max-w-4xl mx-auto">
      <h2 class="text-3xl font-semibold text-gray-800 mb-4">Selecciona tus Butacas</h2>
      <div class="grid grid-cols-5 gap-4">
        <button v-for="butaca in seatsData" :key="butaca.id" :class="{
          'bg-green-500': butaca.estado === 'disponible' && !selectedSeats.includes(butaca.id),
          'bg-blue-500': selectedSeats.includes(butaca.id),
          'bg-red-500': butaca.estado === 'confirmada',
          'bg-gray-500': butaca.estado === 'reservada' || butaca.estado === 'confirmada'
        }" class="w-full text-white p-2 rounded" :disabled="butaca.estado === 'confirmada' || butaca.estado === 'reservada'" @click="selectSeat(butaca)">
          {{ butaca.fila }}{{ butaca.columna }}
        </button>
      </div>

      <!-- Botón para confirmar selección -->
      <div v-if="selectedSeats.length > 0" class="mt-6 text-center">
        <!-- Botón de reserva -->
        <button @click="reservarAsientos"
          class="mt-6 bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
          :disabled="selectedSeats.length === 0">
          Reservar Entrada
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
const availableDates = ref([]);
const selectedSession = ref(null);
const selectedDate = ref('');
const selectedSeats = ref([]);

// Obtener la película
async function fetchMovie() {
  try {
    movie.value = await communicationManager.getMovieById(movieId);
  } catch (error) {
    console.error('Error fetching movie details:', error);
  }
}

// Obtener las sesiones de la película y extraer las fechas disponibles
async function fetchSessions() {
  try {
    const sessionData = await communicationManager.getSessionsByMovie(movieId);
    availableDates.value = Array.from(new Set(sessionData.map(session => session.session_date)));
  } catch (error) {
    console.error('Error fetching sessions:', error);
  }
}

// Formatear la fecha para mostrarla en formato legible
function formatDate(date) {
  return new Date(date).toLocaleDateString('es-ES', { year: 'numeric', month: 'long', day: 'numeric' });
}

// Seleccionar una fecha y obtener las sesiones correspondientes
async function fetchSessionsForDate(date) {
  try {
    sessions.value = await communicationManager.getSessionsByMovieAndDate(movieId, date);
  } catch (error) {
    console.error('Error fetching sessions for date:', error);
  }
}

// Formatear la hora de la sesión
function formatTime(sessionTime) {
  return new Date(`1970-01-01T${sessionTime}Z`).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
}

function selectSeat(butaca) {
  if (butaca.estado === 'confirmada' || butaca.estado === 'reservada') return; // No permitir seleccionar asientos ya reservados

  const index = selectedSeats.value.findIndex(s => s.id === butaca.id);
  if (index === -1) {
    selectedSeats.value.push(butaca); // Agregar butaca si no está seleccionada
  } else {
    selectedSeats.value.splice(index, 1); // Quitar butaca si ya está seleccionada
  }
}

async function reservarAsientos() {
  if (!selectedSession.value) {
    console.error('No hay sesión seleccionada');
    return;
  }

  if (selectedSeats.value.length === 0) {
    console.error('No hay asientos seleccionados');
    alert('Selecciona al menos un asiento antes de reservar.');
    return;
  }

  console.log("Asientos seleccionados para reserva:", selectedSeats.value);

  try {
    for (const butaca of selectedSeats.value) {
      // Verifica que butaca.butaca_id esté presente y sea válido
      if (!butaca || !butaca.butaca_id) {
        console.error("Error: Butaca inválida detectada", butaca);
        continue;
      }

      // Usa butaca.butaca_id para realizar la reserva
      await communicationManager.reservarButaca(1, selectedSession.value, butaca.butaca_id); // Suponiendo que user_id = 1
    }

    alert('Reserva exitosa');
    fetchSeats(); // Recargar las butacas
    selectedSeats.value = []; // Limpiar selección

  } catch (error) {
    console.error('Error reservando butacas:', error);
    alert('Hubo un error al reservar las butacas.');
  }
}


// Obtener las butacas de la sesión seleccionada
async function fetchSeats() {
  try {
    if (selectedSession.value) {
      seatsData.value = await communicationManager.getButacasPorSesion(selectedSession.value);
      selectedSeats.value = []; // Limpiar selección anterior
    }
  } catch (error) {
    console.error('Error fetching seats:', error);
  }
}

onMounted(() => {
  fetchMovie();
  fetchSessions();
});
</script>
