<template>
    <div>
      <!-- Título de la lista de películas -->
      <h1 class="text-4xl font-bold text-center text-white mb-6">Películas Recomendadas</h1>
      
      <!-- Contenedor de películas en forma de grid -->
      <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6 p-6">
        <div
          v-for="movie in movies"
          :key="movie.id"
          class="movie-card bg-white p-4 rounded-lg shadow-md hover:shadow-xl transition-all cursor-pointer"
          @click="goToMovie(movie.id)"
        >
          <!-- Imagen de la película -->
          <img :src="movie.poster_url" alt="Poster" class="w-full h-64 object-cover rounded-lg mb-4" />
          
          <!-- Título de la película -->
          <h2 class="text-xl font-semibold text-gray-800">{{ movie.title }}</h2>
          
          <!-- Descripción o categoría de la película -->
          <p class="text-sm text-gray-600 mt-2">{{ movie.genre || 'Género no disponible' }}</p>
        </div>
      </div>
      <div>
  </div>
    </div>
  </template>
  
  <script setup lang="ts">

  const config = useRuntimeConfig();
  const movies = ref<any[]>([]);
  
  // Función para obtener las películas desde la API
  async function fetchMovies() {
    try {
      movies.value = await $fetch(`${config.public.apiBase}/movies`);
    } catch (error) {
      console.error('Error fetching movies:', error);
    }
  }
  
  // Función para navegar a la página de detalles de la película
  function goToMovie(movieId: number) {
    navigateTo(`/movies/${movieId}`);
  }
  
  onMounted(fetchMovies);
  </script>
  
  <style scoped>
  /* Estilos generales de las películas */
  .movie-card {
    /* Redondeo de las esquinas */
    border-radius: 10px;
    /* Sombra suave */
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    /* Transición suave para hover */
    transition: transform 0.3s, box-shadow 0.3s;
  }
  
  .movie-card:hover {
    transform: translateY(-5px); /* Efecto de levitación al pasar el ratón */
    box-shadow: 0 6px 15px rgba(0, 0, 0, 0.2); /* Sombra más intensa al hacer hover */
  }
  
  /* Media queries */
  @media (max-width: 768px) {
    .grid {
      grid-template-columns: repeat(2, 1fr);
    }
  }
  
  @media (max-width: 480px) {
    .grid {
      grid-template-columns: 1fr;
    }
  }
  </style>
  