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
          <label class="block text-lg font-medium text-gray-700">Selecciona la sesión:</label>
          <select v-model="selectedSession" class="mt-2 p-2 border rounded-md w-full" required>
            <option v-for="session in movie.sessions" :key="session.id" :value="session.id">
              {{ session.time }} - {{ session.language }}
            </option>
          </select>
        </div>

        <!-- Selección de tipo de entrada -->
        <div class="mb-4">
          <label class="block text-lg font-medium text-gray-700">Tipo de entrada:</label>
          <select v-model="selectedTicketType" class="mt-2 p-2 border rounded-md w-full" required>
            <option value="general">Entrada General</option>
            <option value="vip">Entrada VIP</option>
            <option value="student">Entrada Estudiante</option>
          </select>
        </div>

        <!-- Selección de butacas -->
        <div class="mb-4">
          <label class="block text-lg font-medium text-gray-700">Butacas:</label>
          <input type="text" v-model="seats" class="mt-2 p-2 border rounded-md w-full" placeholder="Ejemplo: A1, A2, B3" required />
        </div>

        <!-- Sección de pago con tarjeta -->
        <div class="mb-4">
          <h3 class="text-lg font-medium text-gray-700">Pago con tarjeta</h3>

          <label class="block text-lg font-medium text-gray-700 mt-4">Nombre en la tarjeta:</label>
          <input type="text" v-model="cardName" class="mt-2 p-2 border rounded-md w-full" required />

          <label class="block text-lg font-medium text-gray-700 mt-4">Número de tarjeta:</label>
          <input type="text" v-model="cardNumber" class="mt-2 p-2 border rounded-md w-full" required />

          <div class="flex space-x-4 mt-4">
            <div class="w-1/2">
              <label class="block text-lg font-medium text-gray-700">Mes de vencimiento:</label>
              <input type="number" v-model="expiryMonth" class="mt-2 p-2 border rounded-md w-full" required />
            </div>
            <div class="w-1/2">
              <label class="block text-lg font-medium text-gray-700">Año de vencimiento:</label>
              <input type="number" v-model="expiryYear" class="mt-2 p-2 border rounded-md w-full" required />
            </div>
          </div>

          <label class="block text-lg font-medium text-gray-700 mt-4">CVV:</label>
          <input type="text" v-model="cvv" class="mt-2 p-2 border rounded-md w-full" required />
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

    <!-- Popup de confirmación -->
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
import { useRoute, useRouter } from 'vue-router';

const config = useRuntimeConfig();
const route = useRoute();
const router = useRouter();

const movie = ref<any>(null);
const selectedSession = ref<string>('');
const selectedTicketType = ref<string>('general');
const seats = ref<string>('');
const cardName = ref<string>('');
const cardNumber = ref<string>('');
const expiryMonth = ref<string>('');
const expiryYear = ref<string>('');
const cvv = ref<string>('');
const isModalVisible = ref<boolean>(false);

// Obtener la película por ID desde la API
async function fetchMovie() {
  try {
    movie.value = await $fetch(`${config.public.apiBase}/movies/${route.params.id}`);
  } catch (error) {
    console.error('Error al obtener la película:', error);
  }
}

// Enviar el formulario
async function submitForm() {
  try {
    await $fetch(`${config.public.apiBase}/compras`, {
      method: 'POST',
      body: {
        movieId: movie.value.id,
        sessionId: selectedSession.value,
        ticketType: selectedTicketType.value,
        seats: seats.value,
        cardName: cardName.value,
        cardNumber: cardNumber.value,
        expiryMonth: expiryMonth.value,
        expiryYear: expiryYear.value,
        cvv: cvv.value,
      },
    });
    
    isModalVisible.value = true;
  } catch (error) {
    console.error('Error en la compra:', error);
    alert('Hubo un error al procesar la compra');
  }
}

// Cerrar el modal y redirigir a inicio
function closeModal() {
  isModalVisible.value = false;
  router.push('/');
}

onMounted(fetchMovie);
</script>

<style scoped>
.bg-gray-100 { background-color: #f7fafc; }
.max-w-4xl { max-width: 50rem; }
.mx-auto { margin-left: auto; margin-right: auto; }
.shadow-xl { box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1); }
select, input { width: 100%; padding: 0.5rem; border-radius: 0.375rem; border: 1px solid #ddd; }
button { background-color: #38a169; color: white; font-weight: bold; padding: 10px 20px; border-radius: 10px; cursor: pointer; }
button:hover { background-color: #2f855a; }
.bg-opacity-50 { background-color: rgba(0, 0, 0, 0.5); }
</style>
