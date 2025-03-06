<template>
    <div class="p-6 bg-gray-100 min-h-screen">
      <!-- Enlace para volver atrás con estilo -->
      <NuxtLink to="/" class="text-blue-500 underline hover:text-blue-700 mb-6 inline-block">
        ⬅ Volver
      </NuxtLink>
  
      <!-- Comprobamos si la película está cargada antes de intentar mostrarla -->
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
  
      <!-- Botón para comprar entradas, incluyendo el ID de la película -->
      <div v-if="movie" class="mt-8 text-center">
        <NuxtLink :to="`/buy-ticket/${movie.id}`" class="bg-green-500 text-white font-bold py-2 px-6 rounded-lg hover:bg-green-700">
          Comprar Entradas
        </NuxtLink>
      </div>
    </div>
  </template>
  
  <script setup lang="ts">
  import { ref, onMounted } from 'vue';
  import { useRoute } from 'vue-router';
  
  const config = useRuntimeConfig();
  const route = useRoute();
  const movie = ref<any>(null);
  
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
  </script>
  
  <style scoped>
  /* Estilos del formulario */
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
  </style>
  