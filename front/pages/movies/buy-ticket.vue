<template>
    <div class="p-6 bg-gray-100 min-h-screen">
      <!-- Enlace para volver atrás con estilo -->
      <NuxtLink to="/" class="text-blue-500 underline hover:text-blue-700 mb-6 inline-block">
        ⬅ Volver
      </NuxtLink>
  
      <!-- Cargar la película por ID y mostrar el formulario de compra de entradas -->
      <div v-if="movie" class="mt-6 p-8 border rounded-lg shadow-xl bg-white max-w-4xl mx-auto">
        <!-- Título de la película -->
        <h1 class="text-4xl font-bold text-gray-800 mb-6">{{ movie.title }}</h1>
  
        <!-- Imagen de la película -->
        <img :src="movie.poster_url" alt="Poster" class="w-64 h-auto rounded-lg mb-6 mx-auto" />
  
        <!-- Detalles de la película -->
        <div class="space-y-4 text-gray-700">
          <p><strong class="font-semibold">Director:</strong> {{ movie.director }}</p>
          <p><strong class="font-semibold">Actores:</strong> {{ movie.actores }}</p>
          <p><strong class="font-semibold">Año:</strong> {{ movie.año }}</p>
          <p><strong class="font-semibold">Género:</strong> {{ movie.genero }}</p>
          <p><strong class="font-semibold">Sinopsis:</strong> {{ movie.sinopsis }}</p>
          <p><strong class="font-semibold">Duración:</strong> {{ movie.duracion }} minutos</p>
        </div>
      </div>
  
      <!-- Formulario de compra de entradas -->
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
    </div>
  </template>
  
  <script setup lang="ts">
  import { ref, onMounted } from 'vue';
  import { useRoute } from 'vue-router';
  import { useRuntimeConfig } from '#imports';
  
  const config = useRuntimeConfig();
  const route = useRoute();
  const movie = ref<any>(null);
  const selectedSession = ref<string>('');
  const selectedTicketType = ref<string>('general');
  const seats = ref<string>('');
  
  // Obtener el ID de la película desde la URL
  const movieId = route.params.id;
  
  // Obtener la película por ID
  async function fetchMovie() {
    try {
      movie.value = await $fetch(`${config.public.apiBase}/movies/${movieId}`);
      console.log('Datos de la película:', movie.value);
    } catch (error) {
      console.error('Error fetching movie details:', error);
    }
  }
  
  onMounted(fetchMovie);
  
  // Enviar el formulario
  function submitForm() {
    const formData = {
      movieId: movie.value.id,
      sessionId: selectedSession.value,
      ticketType: selectedTicketType.value,
      seats: seats.value,
    };
  
    console.log('Formulario enviado:', formData);
    // Aquí puedes enviar el formulario a un servidor o API
  }
  </script>
  
  <style scoped>
  /* Aquí puedes añadir el mismo estilo que ya tenías en el archivo anterior */
  </style>
  