<template>
  <div class="p-6 bg-gray-100 min-h-screen">
    <SessionButton />
    <Carrito />
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
        <button
          v-for="butaca in seatsData"
          :key="butaca.id"
          :class="{
            'bg-red-500': butaca.estado === 'comprado',
            'bg-yellow-500': butaca.estado === 'reservado',
            'bg-white border border-gray-300': butaca.estado === 'disponible',
            'bg-blue-500': selectedSeats.includes(butaca.id),
          }"
          class="w-full text-black p-2 rounded"
          :disabled="butaca.estado === 'comprado' || butaca.estado === 'reservado'"
          @click="selectSeat(butaca)"
        >
          {{ butaca.fila }}{{ butaca.columna }}
          <span v-if="selectedSeats.includes(butaca.id)" class="text-yellow-300">
          </span>
        </button>
      </div>

      

      <!-- Botón para confirmar selección -->
      <div v-if="selectedSeats.length > 0" class="mt-6 text-center">
        <!-- Si el usuario no está logueado, mostrar botón de login -->
        <button v-if="!isUserLoggedIn" @click="showLoginPopup" class="mt-6 bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
          Iniciar sesión
        </button>

        <!-- Si el usuario está logueado, mostrar botón de reserva -->
        <button v-if="isUserLoggedIn" @click="reservarAsientos"
          class="mt-6 bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
          :disabled="selectedSeats.length === 0">
          Reservar Entrada
        </button>

        <!-- Botón de pagar -->
        <button v-if="isUserLoggedIn && compra_id" @click="realizarPago"
          class="mt-6 bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
          Pagar
        </button>
      </div>
    </div>

    <!-- Popup para iniciar sesión -->
    <div v-if="showLogin" class="fixed inset-0 bg-gray-800 bg-opacity-50 flex justify-center items-center">
      <div class="bg-white p-6 rounded-lg shadow-lg max-w-sm w-full text-center">
        <h3 class="text-xl font-semibold text-gray-800 mb-4">Inicia sesión para continuar</h3>
        <button @click="redirectToLogin" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
          Iniciar sesión
        </button>
        <button @click="closeLoginPopup" class="mt-4 text-red-500">Cerrar</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import communicationManager from '../../services/communicationManager';

const route = useRoute();
const router = useRouter();
const movieId = route.params.id;
const movie = ref(null);
const sessions = ref([]);
const seatsData = ref([]);
const availableDates = ref([]);
const selectedSession = ref(null);
const selectedDate = ref('');
const selectedSeats = ref([]);
const compra_id = ref(null); // Variable reactiva para almacenar el compra_id
const showLogin = ref(false);

const isUserLoggedIn = computed(() => {
  const token = localStorage.getItem('auth_token');
  return !!token;
});

async function reservarAsientos() {
  if (!selectedSession.value) {
    alert('Por favor selecciona una sesión.');
    return;
  }

  if (selectedSeats.value.length === 0) {
    alert('Por favor selecciona al menos un asiento.');
    return;
  }

  const token = localStorage.getItem('auth_token');
  if (!token) {
    alert('Por favor inicia sesión antes de hacer la reserva.');
    return;
  }

  const availableSeats = selectedSeats.value.filter(butaca => butaca.estado === 'disponible');
  if (availableSeats.length === 0) {
    alert('No hay butacas disponibles.');
    return;
  }

  try {
    const butacaIds = availableSeats.map(butaca => butaca.butaca_id);
    const response = await communicationManager.reservarButaca(selectedSession.value, butacaIds);

    if (response.message === 'Reservas en proceso. Por favor, complete el pago.') {
      alert('Reservas realizadas con éxito. Por favor, complete el pago.');
      compra_id.value = response.compra_id; // Guardar el ID de la compra
      selectedSeats.value = [];
      fetchSeats();
    } else {
      alert('Hubo un error al realizar las reservas.');
    }
  } catch (error) {
    console.error('Error reservando las butacas:', error);
    alert('Hubo un error al realizar las reservas. Revisa la consola para más detalles.');
  }
}

async function realizarPago() {
  if (!compra_id.value) {
    alert('No se ha encontrado el ID de la compra. Por favor, completa la reserva primero.');
    return;
  }

  const datosPago = {
    numero_tarjeta: '1111111111111111', // Datos de prueba
    fecha_vencimiento: '11/30', // Datos de prueba
    cvv: '111', // Datos de prueba
    compra_id: compra_id.value, // Usar el compra_id guardado
    nombre_pelicula: movie.value.title // Nombre de la película
  };

  console.log("Datos de pago antes de realizarPago:", datosPago);

  try {
    const pagoResponse = await communicationManager.realizarPago(datosPago);
    console.log("Respuesta del pago:", pagoResponse);
    alert('Pago realizado con éxito.');
  } catch (error) {
    console.error('Error al realizar el pago:', error);
    alert('Hubo un error al realizar el pago. Revisa la consola para más detalles.');
  }
}

function getUserIdFromToken() {
  const token = localStorage.getItem('auth_token');
  if (token) {
    const decodedToken = JSON.parse(atob(token.split('.')[1])); // Decodifica el token
    return decodedToken.user_id; // Suponiendo que el user_id esté en el token
  }
  return null;
}

async function checkUserAndReserve() {
  if (!userId.value) {
    userId.value = getUserIdFromToken(); // Recupéralo si no está definido
    if (!userId.value) {
      alert('No se ha encontrado el ID de usuario. Por favor, inicia sesión.');
      return;
    }
  }

  await reservarAsientos();
}

// Si no está logueado, redirige a la página de login
function redirectToLogin() {
  router.push('/login'); // Cambia esto por la ruta de login en tu aplicación
}

// Obtener la película
async function fetchMovie() {
  try {
    console.log("Obteniendo datos de la película con ID:", movieId);
    movie.value = await communicationManager.getMovieById(movieId);
    console.log("Datos de la película obtenidos:", movie.value); // Verifica los datos de la película
  } catch (error) {
    console.error('Error fetching movie details:', error);
  }
}

// Obtener las sesiones de la película y extraer las fechas disponibles
async function fetchSessions() {
  try {
    console.log("Obteniendo sesiones para la película con ID:", movieId);
    const sessionData = await communicationManager.getSessionsByMovie(movieId);
    availableDates.value = Array.from(new Set(sessionData.map(session => session.session_date)));
    console.log("Fechas disponibles:", availableDates.value); // Verifica las fechas disponibles
  } catch (error) {
    console.error('Error fetching sessions:', error);
  }
}

// Seleccionar una fecha y obtener las sesiones correspondientes
async function fetchSessionsForDate(date) {
  try {
    console.log("Obteniendo sesiones para la fecha:", date);
    sessions.value = await communicationManager.getSessionsByMovieAndDate(movieId, date);
    console.log("Sesiones obtenidas:", sessions.value); // Verifica las sesiones obtenidas
  } catch (error) {
    console.error('Error fetching sessions for date:', error);
  }
}

// Formatear la fecha para mostrarla en formato legible
function formatDate(date) {
  const formattedDate = new Date(date).toLocaleDateString('es-ES', { year: 'numeric', month: 'long', day: 'numeric' });
  console.log("Fecha formateada:", formattedDate);
  return formattedDate;
}

// Formatear la hora de la sesión
function formatTime(sessionTime) {
  const time = new Date(`1970-01-01T${sessionTime}Z`).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
  console.log("Hora formateada:", time);
  return time;
}
function selectSeat(butaca) {
  if (butaca.estado === 'confirmada' || butaca.estado === 'reservada') return;

  const index = selectedSeats.value.findIndex(s => s.butaca_id === butaca.butaca_id);
  if (index === -1) {
    selectedSeats.value.push({
      butaca_id: butaca.butaca_id,
      precio: butaca.precio, // Asegúrate de que butaca.precio esté definido
      fila: butaca.fila,
      columna: butaca.columna,
      estado: butaca.estado
    });
  } else {
    selectedSeats.value.splice(index, 1);
  }
}

async function fetchSeats() {
  if (selectedSession.value) {
    console.log("Cargando las butacas para la sesión:", selectedSession.value);
    seatsData.value = await communicationManager.getButacasPorSesion(selectedSession.value);
    console.log("Butacas obtenidas:", seatsData.value); // Verifica que cada butaca tenga precio
  }
}

// Mostrar el popup de login
function showLoginPopup() {
  showLogin.value = true;
}

// Cerrar el popup de login
function closeLoginPopup() {
  showLogin.value = false;
}

onMounted(() => {
  fetchMovie();
  fetchSessions();
});
</script>
