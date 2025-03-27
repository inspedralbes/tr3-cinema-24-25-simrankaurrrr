<template>
      <Navbar />

  <div class="container">
    <div class="back-button-container">

    <button @click="goBack" class="back-button">
      ⬅ Volver
    </button>
    </div>
    <!-- Alert Modal -->
    <div v-if="alertMessage" class="alert-modal">
      <div class="alert-content">
        <h3>{{ alertMessage }}</h3>
        <button @click="closeAlert" class="alert-close-button">Cerrar</button>
      </div>
    </div>

    <div v-if="movie" class="movie-container">
      <h1 class="movie-title">{{ movie.title }}</h1>
      <img :src="movie.poster_url" alt="Poster" class="movie-poster" />    </div>

    <div v-if="availableDates.length > 0" class="selection-container">
      <h2 class="selection-title">Selecciona una Fecha</h2>
      <select v-model="selectedDate" @change="fetchSessionsForDate(selectedDate)" class="select-box" required>
        <option value="" disabled selected>Selecciona una Fecha</option>
        <option v-for="date in availableDates" :key="date" :value="date">
          {{ formatDate(date) }}
        </option>
      </select>
    </div>

    <div v-if="sessions.length > 0" class="selection-container">
      <h2 class="selection-title">Selecciona una Sesión</h2>
      <select v-model="selectedSession" @change="selectSession($event.target.value)" class="select-box" required>
        <option value="" disabled selected>Selecciona una Sesión</option>
        <option v-for="session in sessions" :key="session.id" :value="session.id">
          {{ formatTime(session.session_time) }}
        </option>
      </select>
    </div>
    <div v-if="seatsData.length > 0" class="selection-container">
      <h2 class="selection-title">Selecciona tus Butacas</h2>
      
      <div class="legend">
        <span class="seat-bought">Comprado</span>
        <span class="seat-reserved">Reservado</span>
        <span class="seat-selected">Seleccionado</span>
        <span class="seat-available">Disponible</span>
      </div>
      <div v-if="selectedSeats.length > 0" class="button-group">
        <button v-if="!isUserLoggedIn" @click="showLoginPopup" class="action-button login-button">
          Iniciar sesión
        </button>
        <button v-if="isUserLoggedIn" @click="reservarAsientos" class="action-button reserve-button">
          Reservar Entrada
        </button>
        <button v-if="isUserLoggedIn && compra_id" @click="realizarPago" class="action-button pay-button">
          Pagar
        </button>
      </div>
      <div class="seat-grid">
    <button v-for="butaca in seatsData" :key="butaca.id" 
      :class="{
        'seat-bought': butaca.estado === 'comprado',
        'seat-reserved': butaca.estado === 'reservado',
        'seat-selected': isSeatSelected(butaca),
        'seat-available': butaca.estado === 'disponible' && !isSeatSelected(butaca)
      }"
      class="seat-button"
      :disabled="butaca.estado === 'comprado' || butaca.estado === 'reservado' || butaca.estado === 'en_proceso'"
      @click="selectSeat(butaca)">
      {{ butaca.fila }}{{ butaca.columna }}
    </button>
  </div>


     
    </div>

    <div v-if="showLogin" class="login-popup">
      <div class="login-container">
        <h3 class="login-title">Inicia sesión para continuar</h3>
        <button @click="redirectToLogin" class="action-button login-button">Iniciar sesión</button>
        <button @click="closeLoginPopup" class="close-button">Cerrar</button>
      </div>
    </div>
  </div>
</template>

<script setup>
// Lógica de Vue
import { ref, onMounted, computed } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import communicationManager from '../../services/communicationManager';
import Navbar from './components/Navbar.vue';

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
const compra_id = ref(null);
const showLogin = ref(false);
const alertMessage = ref(null);  // Controla el mensaje de alerta

const isUserLoggedIn = computed(() => {
  const token = localStorage.getItem('auth_token');
  return !!token;
});
// Añadir este método en el script setup
function goBack() {
  router.go(-1); // Navega a la página anterior
}
function showLoginPopup() {
  showLogin.value = true;
}

function closeLoginPopup() {
  showLogin.value = false;
}

function redirectToLogin() {
  router.push('/register');
}

function closeAlert() {
  alertMessage.value = null;  // Cierra la alerta
}
async function reservarAsientos() {
  // Verificación inicial del límite
  if (selectedSeats.value.length > 10) {
    alertMessage.value = '❌ Límite alcanzado: No puedes reservar más de 10 butacas por transacción';
    return;
  }

  if (!isUserLoggedIn.value) {
    showLoginPopup();
    return;
  }

  if (!selectedSession.value) {
    alertMessage.value = 'Por favor selecciona una sesión.';
    return;
  }

  if (selectedSeats.value.length === 0) {
    alertMessage.value = 'Por favor selecciona al menos un asiento.';
    return;
  }

  const availableSeats = selectedSeats.value.filter(butaca => butaca.estado === 'disponible');
  if (availableSeats.length === 0) {
    alertMessage.value = 'No hay butacas disponibles.';
    return;
  }

  try {
    const butacaIds = availableSeats.map(butaca => butaca.butaca_id);
    const response = await communicationManager.reservarButaca(selectedSession.value, butacaIds);

    if (response.message === 'Reservas en proceso. Por favor, complete el pago.') {
      alertMessage.value = 'Reservas realizadas con éxito. Por favor, complete el pago.';
      compra_id.value = response.compra_id;
      selectedSeats.value = [];
      fetchSeats();
    } else {
      alertMessage.value = 'Hubo un error al realizar las reservas.';
    }
  } catch (error) {
    console.error('Error reservando las butacas:', error);
    
    // Manejo específico del error 400
    if (error.response && error.response.status === 400) {
      // Usamos el mensaje del backend si está disponible
      alertMessage.value = error.response.data?.message || 
                         'No puedes reservar más de 10 butacas para esta sesión';
    } else {
      alertMessage.value = error.message || 'Hubo un error al realizar las reservas.';
    }
  }
}

async function realizarPago() {
  if (!compra_id.value) {
    alertMessage.value = 'No se ha encontrado el ID de la compra. Por favor, completa la reserva primero.';
    return;
  }

  const datosPago = {
    numero_tarjeta: '1111111111111111',
    fecha_vencimiento: '11/30',
    cvv: '111',
    compra_id: compra_id.value,
    nombre_pelicula: movie.value.title
  };

  console.log("Datos de pago antes de realizarPago:", datosPago);

  try {
    const pagoResponse = await communicationManager.realizarPago(datosPago);
    console.log("Respuesta del pago:", pagoResponse);
    alertMessage.value = 'Pago realizado con éxito.';
  } catch (error) {
    console.error('Error al realizar el pago:', error);
    alertMessage.value = 'Hubo un error al realizar el pago. Revisa la consola para más detalles.';
  }
}

async function fetchMovie() {
  try {
    console.log("Obteniendo datos de la película con ID:", movieId);
    movie.value = await communicationManager.getMovieById(movieId);
    console.log("Datos de la película obtenidos:", movie.value);
  } catch (error) {
    console.error('Error fetching movie details:', error);
  }
}

async function fetchSessions() {
  try {
    console.log("Obteniendo sesiones para la película con ID:", movieId);
    const sessionData = await communicationManager.getSessionsByMovie(movieId);
    availableDates.value = Array.from(new Set(sessionData.map(session => session.session_date)));
    console.log("Fechas disponibles:", availableDates.value);
  } catch (error) {
    console.error('Error fetching sessions:', error);
  }
}

async function fetchSessionsForDate(date) {
  try {
    console.log("Obteniendo sesiones para la fecha:", date);
    sessions.value = await communicationManager.getSessionsByMovieAndDate(movieId, date);
    console.log("Sesiones obtenidas:", sessions.value);
  } catch (error) {
    console.error('Error fetching sessions for date:', error);
  }
}

function formatDate(date) {
  const formattedDate = new Date(date).toLocaleDateString('es-ES', { year: 'numeric', month: 'long', day: 'numeric' });
  return formattedDate;
}

function formatTime(sessionTime) {
  const time = new Date(`1970-01-01T${sessionTime}Z`).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
  return time;
}
function isSeatSelected(butaca) {
  return selectedSeats.value.some(s => s.butaca_id === butaca.butaca_id);
}

function selectSeat(butaca) {
  if (butaca.estado === 'comprado' || butaca.estado === 'reservado' || butaca.estado === 'en_proceso') return;

  const index = selectedSeats.value.findIndex(s => s.butaca_id === butaca.butaca_id);
  if (index === -1) {
    selectedSeats.value.push({
      butaca_id: butaca.butaca_id,
      precio: butaca.precio,
      fila: butaca.fila,
      columna: butaca.columna,
      estado: butaca.estado
    });
  } else {
    selectedSeats.value.splice(index, 1);
  }
}
async function fetchSeats() {
  if (!selectedSession.value) {
    console.error("No session selected. Please select a session first.");
    return;
  }

  try {
    console.log("Fetching seats for session:", selectedSession.value);
    seatsData.value = await communicationManager.getButacasPorSesion(selectedSession.value);
    console.log("Seats data fetched:", seatsData.value);
  } catch (error) {
    console.error('Error fetching seats:', error);
    alertMessage.value = 'Error fetching seats. Please try again.';
  }
}

function selectSession(sessionId) {
  selectedSession.value = sessionId;
  fetchSeats();
}

onMounted(() => {
  fetchMovie();
  fetchSessions();
});
</script>
<style>
.movie-poster {
  max-width: 100%;
  height: auto;
  max-height: 450px; /* Ajusta este valor según necesites */
  object-fit: contain;
  border-radius: 8px;
  margin: 15px 0;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
}
.container {
  background-color: #2b2d42;
  color: #edf2f4;
  min-height: 100vh;
  padding: 20px;
}

.back-button-container {
  max-width: 1200px;
  margin: 0 auto 20px;
  padding: 0 20px;
}

.back-button {
  background-color: #2b2d42;
  color: #8d99ae;
  border: 2px solid #8d99ae;
  padding: 10px 20px;
  border-radius: 8px;
  font-weight: bold;
  cursor: pointer;
  transition: all 0.3s;
}

.back-button:hover {
  color: #ef233c;
  border-color: #ef233c;
}

.alert-modal {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(43, 45, 66, 0.8);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1000;
}

.alert-content {
  background-color: #edf2f4;
  padding: 30px;
  border-radius: 12px;
  max-width: 500px;
  color: #2b2d42;
}

.alert-close-button {
  background-color: #691e06;
  color: white;
  padding: 10px 20px;
  border: none;
  border-radius: 6px;
  margin-top: 15px;
  cursor: pointer;
  transition: background-color 0.3s;
}

.alert-close-button:hover {
  background-color: #d80032;
}

.movie-container,
.selection-container {
  background-color: #edf2f4;
  color: #2b2d42;
  padding: 20px;
  border-radius: 12px;
  margin: 20px auto;
  max-width: 600px;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
}

.movie-title,
.selection-title {
  color: #d80032;
  margin-bottom: 15px;
}

.select-box {
  background-color: #edf2f4;
  border: 2px solid #2b2d42;
  color: #2b2d42;
  padding: 10px;
  width: 100%;
  border-radius: 6px;
}

.legend {
  display: flex;
  justify-content: center;
  gap: 10px;
  margin: 15px 0;
  flex-wrap: wrap;
}

.legend span {
  padding: 5px 10px;
  border-radius: 4px;
  font-size: 14px;
  color: white;
}

.seat-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(40px, 1fr));
  gap: 10px;
  margin: 20px 0;
}

.seat-button {
  width: 40px;
  height: 40px;
  border-radius: 6px;
  border: none;
  font-weight: bold;
  cursor: pointer;
  transition: transform 0.2s;
}

.seat-button:hover:not(:disabled) {
  transform: scale(1.05);
}

.seat-available {
  background-color: #8d99ae;
  color: #2b2d42;
}

.seat-selected {
  background-color: #2b2d42;
  color: #edf2f4;
}

.seat-bought {
  background-color: #d80032;
  color: white;
  cursor: not-allowed;
}

.seat-reserved {
  background-color: #691e06;
  color: white;
  cursor: not-allowed;
}

.button-group {
  display: flex;
  justify-content: center;
  gap: 10px;
  margin-top: 20px;
}

.action-button {
  padding: 10px 20px;
  border-radius: 6px;
  border: none;
  color: white;
  font-weight: bold;
  cursor: pointer;
  transition: background-color 0.3s;
}

.login-button {
  background-color: #2b2d42;
}

.login-button:hover {
  background-color: #8d99ae;
}

.reserve-button {
  background-color: #8d99ae;
}

.reserve-button:hover {
  background-color: #2b2d42;
}

.pay-button {
  background-color: #d80032;
}

.pay-button:hover {
  background-color: #691e06;
}

.login-popup {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(43, 45, 66, 0.8);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1000;
}

.login-container {
  background-color: #edf2f4;
  padding: 30px;
  border-radius: 12px;
  max-width: 400px;
  color: #2b2d42;
}

.login-title {
  color: #d80032;
  margin-bottom: 20px;
}

.close-button {
  background: none;
  border: none;
  color: #d80032;
  font-weight: bold;
  cursor: pointer;
  margin-top: 15px;
}
</style>