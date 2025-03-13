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
          <span v-if="selectedSeats.includes(butaca.id)" class="text-yellow-300">
            ({{ butaca.precio }}€)
          </span>
        </button>
      </div>

      <!-- Mostrar el precio total -->
      <div v-if="selectedSeats.length > 0" class="mt-6 text-center">
        <p class="text-xl font-semibold text-gray-800">Precio Total: {{ totalPrice }}€</p>
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
import { ref, onMounted, computed } from 'vue';
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
const userId = 1; // ID del usuario logueado, puedes modificarlo según sea necesario

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
  if (butaca.estado === 'confirmada' || butaca.estado === 'reservada') return;


  const index = selectedSeats.value.findIndex(s => s.butaca_id === butaca.butaca_id); // Use butaca.butaca_id
  if (index === -1) {
    selectedSeats.value.push({
      butaca_id: butaca.butaca_id, // Use butaca.butaca_id
      precio: butaca.precio,
      fila: butaca.fila,
      columna: butaca.columna,
      estado: butaca.estado
    });
  } else {
    selectedSeats.value.splice(index, 1);
  }
}

// Calcular el precio total
const totalPrice = computed(() => {
  let total = 0;
  selectedSeats.value.forEach(butaca => {
    total += butaca.precio; // El precio ya viene de la butaca seleccionada
  });

  return total.toFixed(2);
});
async function reservarAsientos() {
  if (!selectedSession.value || selectedSeats.value.length === 0) {
    alert('Por favor selecciona una sesión y asientos.');
    return;
  }

  // Filtrar las butacas disponibles antes de hacer la solicitud
  const availableSeats = selectedSeats.value.filter(butaca => butaca.estado === 'disponible');
  if (availableSeats.length === 0) {
    alert('No hay butacas disponibles.');
    return;
  }

  try {
    // Mostrar los datos de las butacas seleccionadas para depuración
    console.log('Butacas seleccionadas:', selectedSeats.value);

    // Realizamos una solicitud individual por cada butaca seleccionada
    for (let butaca of availableSeats) {
      if (!butaca.butaca_id) {
        console.error('Butaca sin ID:', butaca);
        throw new Error('Butaca sin ID');
      }

      const reservationData = {
        user_id: userId, // Usar la variable userId definida en el componente
        movie_session_id: selectedSession.value,
        butaca_id: butaca.butaca_id, // Asegurarse de que butaca.butaca_id esté definido
      };

      // Mostrar los datos que se están enviando en la consola para depuración
      console.log('Datos de reserva que se están enviando:', reservationData);

      // Enviar la solicitud para reservar cada butaca
      const response = await communicationManager.reservarButaca(
        reservationData.user_id,
        reservationData.movie_session_id,
        reservationData.butaca_id
      );

      // Mostrar la respuesta completa en la consola para depuración
      console.log('Respuesta del servidor:', response);

      // Verifica si la respuesta contiene datos de reserva y butaca
      if (response.reserva) {
        const reserva = response.reserva;
        const precioReserva = reserva.precio || butaca.precio; // Si no hay precio en la respuesta, usa el precio de la butaca
        alert(`Reserva realizada con éxito para la butaca ${butaca.fila}${butaca.columna}. Precio: ${precioReserva}€`);

        // Actualizar el estado de la butaca a 'reservada'
        const reservedSeat = seatsData.value.find(seat => seat.id === butaca.butaca_id);
        if (reservedSeat) {
          reservedSeat.estado = 'reservada'; // Actualizamos el estado de la butaca
        }
      } else {
        console.error('Respuesta inesperada del servidor:', response);
        throw new Error(`Error en la reserva para la butaca ${butaca.fila}${butaca.columna}`);
      }
    }

    fetchSeats(); // Recargar las butacas disponibles
    selectedSeats.value = []; // Limpiar selección de asientos
  } catch (error) {
    // Mostrar información completa del error en la consola
    console.error('Error reservando la butaca:', error);
    alert('Hubo un error al realizar la reserva. Revisa la consola para más detalles.');
  }
}


async function fetchSeats() {
  if (selectedSession.value) {
    seatsData.value = await communicationManager.getButacasPorSesion(selectedSession.value);
  }
}

onMounted(() => {
  fetchMovie();
  fetchSessions();
});
</script>
