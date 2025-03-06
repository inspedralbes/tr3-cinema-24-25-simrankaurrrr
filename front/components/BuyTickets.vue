<template>
  <div class="container">
    <h1 class="text-4xl font-bold text-center text-white mb-6">Compra de Entradas</h1>

    <!-- Información de la película -->
    <div class="movie-info text-center">
      <img :src="movie.poster_url" alt="Poster" class="w-full h-64 object-cover rounded-lg mb-4" />
      <h2 class="text-2xl font-semibold text-gray-800">{{ movie.title }}</h2>
      <p class="text-lg text-gray-600 mt-2">{{ movie.genre || 'Género no disponible' }}</p>
    </div>

    <!-- Formulario para elegir hora y butaca -->
    <form @submit.prevent="submitForm" class="mt-8">
      <!-- Selección de la hora -->
      <div class="mb-4">
        <label for="time" class="block text-gray-700 text-lg">Selecciona una hora</label>
        <select id="time" v-model="selectedTime" class="w-full p-2 mt-2 border rounded">
          <option v-for="time in availableTimes" :key="time" :value="time">{{ time }}</option>
        </select>
      </div>

      <!-- Selección de la butaca -->
      <div class="mb-4">
        <label for="seat" class="block text-gray-700 text-lg">Selecciona tu butaca</label>
        <select id="seat" v-model="selectedSeat" class="w-full p-2 mt-2 border rounded">
          <option v-for="seat in availableSeats" :key="seat" :value="seat">{{ seat }}</option>
        </select>
      </div>

      <!-- Botón para confirmar la compra -->
      <div class="mt-6">
        <button type="submit" class="bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700">Confirmar Compra</button>
      </div>
    </form>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';

const route = useRoute();
const movieId = route.params.id; // Obtener el ID de la película desde la URL

// Datos de la película seleccionada
const movie = ref<any>({});

// Datos de horarios y butacas (pueden ser dinámicos dependiendo de la API)
const availableTimes = ref<string[]>(['12:00 PM', '3:00 PM', '6:00 PM', '9:00 PM']);
const availableSeats = ref<string[]>(['A1', 'A2', 'B1', 'B2', 'C1', 'C2']);

// Variables para manejar las selecciones
const selectedTime = ref<string>('');
const selectedSeat = ref<string>('');

// Función para obtener la película desde la API (esto puede cambiar según cómo obtienes los detalles de la película)
async function fetchMovie() {
  try {
    const response = await $fetch(`https://api.example.com/movies/${movieId}`);
    movie.value = response;
  } catch (error) {
    console.error('Error al obtener los detalles de la película:', error);
  }
}

// Función para manejar la confirmación de compra
function submitForm() {
  if (!selectedTime.value || !selectedSeat.value) {
    alert('Por favor, selecciona una hora y una butaca.');
    return;
  }

  // Aquí puedes agregar la lógica para procesar la compra
  alert(`Compra confirmada:\nPelícula: ${movie.value.title}\nHora: ${selectedTime.value}\nButaca: ${selectedSeat.value}`);
}

// Cargar los detalles de la película cuando el componente se monta
onMounted(fetchMovie);
</script>

<style scoped>
.container {
  max-width: 600px;
  margin: 0 auto;
  padding: 20px;
}

.movie-info {
  margin-bottom: 20px;
}

form {
  background-color: #fff;
  padding: 20px;
  border-radius: 8px;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
}

button {
  width: 100%;
  font-size: 16px;
  font-weight: bold;
  transition: background-color 0.3s;
}

button:hover {
  background-color: #007bff;
}
</style>
