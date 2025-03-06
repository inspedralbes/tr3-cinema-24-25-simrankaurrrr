<template>
  <div class="p-6 bg-gray-100 min-h-screen">
    <!-- Enlace para volver atrás con estilo -->
    <NuxtLink to="/" class="text-blue-500 underline hover:text-blue-700 mb-6 inline-block">
      ⬅ Volver
    </NuxtLink>

    <!-- Título de la película -->
    <div v-if="movie" class="mt-6 p-8 border rounded-lg shadow-xl bg-white max-w-4xl mx-auto">
      <h1 class="text-4xl font-bold text-gray-800 mb-6">{{ movie.title }}</h1>
    </div>

    <!-- Verificar si las sesiones existen antes de mostrar el formulario -->
    <div v-if="movie?.sessions?.length > 0" class="mt-8 p-6 border rounded-lg shadow-xl bg-white max-w-4xl mx-auto">
      <h2 class="text-3xl font-semibold text-gray-800 mb-4">Comprar Entradas</h2>

      <form @submit.prevent="submitForm">
        <!-- Selección de sesión -->
        <div class="mb-4">
          <label for="session" class="block text-lg font-medium text-gray-700">Selecciona la sesión:</label>
          <select id="session" v-model="selectedSession" class="mt-2 p-2 border rounded-md w-full" required>
            <option v-for="session in movie.sessions" :key="session.id" :value="session.id">
              {{ session.time }} - {{ session.language }}
            </option>
          </select>
        </div>

        <!-- Selección de tipo de entrada -->
        <div class="mb-4">
          <label for="ticketType" class="block text-lg font-medium text-gray-700">Selecciona el tipo de entrada:</label>
          <select id="ticketType" v-model="selectedTicketType" class="mt-2 p-2 border rounded-md w-full" required>
            <option value="general">Entrada General</option>
            <option value="vip">Entrada VIP</option>
            <option value="student">Entrada Estudiante</option>
          </select>
        </div>

        <!-- Selección de butacas -->
        <div class="mb-4">
          <label for="seats" class="block text-lg font-medium text-gray-700">Selecciona las butacas:</label>
          <input type="text" id="seats" v-model="seats" class="mt-2 p-2 border rounded-md w-full" placeholder="Ejemplo: A1, A2, B3" required />
        </div>

        <!-- Sección de pago con tarjeta -->
        <div class="mb-4">
          <h3 class="text-lg font-medium text-gray-700">Pago con tarjeta</h3>
          
          <!-- Nombre en la tarjeta -->
          <label for="cardName" class="block text-lg font-medium text-gray-700 mt-4">Nombre en la tarjeta:</label>
          <input type="text" id="cardName" v-model="cardName" class="mt-2 p-2 border rounded-md w-full" placeholder="Ejemplo: Juan Pérez" required />

          <!-- Número de tarjeta -->
          <label for="cardNumber" class="block text-lg font-medium text-gray-700 mt-4">Número de tarjeta:</label>
          <input type="text" id="cardNumber" v-model="cardNumber" class="mt-2 p-2 border rounded-md w-full" placeholder="XXXX XXXX XXXX XXXX" required />

          <!-- Fecha de vencimiento -->
          <div class="flex space-x-4 mt-4">
            <div class="w-1/2">
              <label for="expiryMonth" class="block text-lg font-medium text-gray-700">Mes de vencimiento:</label>
              <input type="number" id="expiryMonth" v-model="expiryMonth" class="mt-2 p-2 border rounded-md w-full" placeholder="MM" required />
            </div>
            <div class="w-1/2">
              <label for="expiryYear" class="block text-lg font-medium text-gray-700">Año de vencimiento:</label>
              <input type="number" id="expiryYear" v-model="expiryYear" class="mt-2 p-2 border rounded-md w-full" placeholder="AAAA" required />
            </div>
          </div>

          <!-- Código de seguridad (CVV) -->
          <label for="cvv" class="block text-lg font-medium text-gray-700 mt-4">Código de seguridad (CVV):</label>
          <input type="text" id="cvv" v-model="cvv" class="mt-2 p-2 border rounded-md w-full" placeholder="XXX" required />
        </div>

        <!-- Botón de compra -->
        <div class="text-center">
          <button type="submit" class="bg-green-500 text-white font-bold py-2 px-6 rounded-lg hover:bg-green-700">
            Confirmar Compra
          </button>
        </div>
      </form>
    </div>

    <!-- Mensaje si no hay sesiones disponibles -->
    <div v-else class="mt-8 p-6 border rounded-lg shadow-xl bg-white max-w-4xl mx-auto">
      <p>No hay sesiones disponibles para esta película.</p>
    </div>

    <!-- Popup de confirmación de compra -->
    <div v-if="isModalVisible" class="fixed inset-0 flex justify-center items-center bg-black bg-opacity-50">
      <div class="bg-white p-6 rounded-lg shadow-lg max-w-sm text-center">
        <h2 class="text-2xl font-bold text-green-500">¡Compra Confirmada!</h2>
        <p class="mt-4">Has comprado tu entrada con éxito.</p>
        <button @click="closeModal" class="mt-6 bg-green-500 text-white py-2 px-4 rounded-lg hover:bg-green-700">
          Cerrar
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';  // Importamos el hook de router

const router = useRouter(); // Usamos el router
const config = useRuntimeConfig();
const route = useRoute();
const movie = ref<any>(null);
const selectedSession = ref<string>('');
const selectedTicketType = ref<string>('general');
const seats = ref<string>('');
const cardName = ref<string>('');
const cardNumber = ref<string>('');
const expiryMonth = ref<string>('');
const expiryYear = ref<string>('');
const cvv = ref<string>('');
const isModalVisible = ref<boolean>(false); // Controlar la visibilidad del modal

// Datos de prueba para la película
const movieData = {
  title: "Película de Comedia",
  sessions: [
    {
      id: "1",
      time: "12:00 PM",
      language: "Español"
    },
    {
      id: "2",
      time: "3:00 PM",
      language: "Subtítulos en Español"
    },
    {
      id: "3",
      time: "6:00 PM",
      language: "Inglés"
    }
  ]
};

// Asignar los datos de prueba al cargar la página
function fetchMovie() {
  movie.value = movieData;
  console.log('Datos de la película:', movie.value); // Verificar los datos de la película
}

onMounted(fetchMovie);

// Enviar el formulario
function submitForm() {
  const formData = {
    movieId: movie.value.id,
    sessionId: selectedSession.value,
    ticketType: selectedTicketType.value,
    seats: seats.value,
    cardName: cardName.value,
    cardNumber: cardNumber.value,
    expiryMonth: expiryMonth.value,
    expiryYear: expiryYear.value,
    cvv: cvv.value,
  };

  console.log('Formulario enviado:', formData);
  // Aquí podrías integrar el envío del formulario a un servidor o API.

  // Mostrar el modal de confirmación
  isModalVisible.value = true;
}

// Cerrar el modal y redirigir a la página de inicio
function closeModal() {
  isModalVisible.value = false;
  router.push('/'); // Redirige al índice (página de inicio)
}
</script>

<style scoped>
/* Estilos para el formulario */
.bg-gray-100 {
  background-color: #f7fafc;
}

.max-w-4xl {
  max-width: 50rem;
}

.mx-auto {
  margin-left: auto;
  margin-right: auto;
}

.shadow-xl {
  box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
}

form {
  max-width: 600px;
  margin: 0 auto;
}

select, input {
  width: 100%;
  padding: 0.5rem;
  margin-top: 0.5rem;
  border-radius: 0.375rem;
  border: 1px solid #ddd;
}

button {
  background-color: #38a169;
  color: white;
  font-weight: bold;
  padding: 10px 20px;
  border-radius: 10px;
  cursor: pointer;
  transition: background-color 0.3s ease;
}

button:hover {
  background-color: #2f855a;
}

/* Estilos del modal */
.fixed {
  position: fixed;
}

.bg-opacity-50 {
  background-color: rgba(0, 0, 0, 0.5);
}

.bg-white {
  background-color: white;
}

.max-w-sm {
  max-width: 24rem;
}

.text-center {
  text-align: center;
}
</style>
