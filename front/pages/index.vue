<!-- index.vue -->
<template>
  <div class="app-container">
    <div class="movie-container">
      <h1 class="title">Películas Recomendadas</h1>
      <MovieList />
    </div>
  </div>
</template>

<script setup lang="ts">
import MovieList from '../components/MovieList.vue';
</script>

<style scoped>
/* Contenedor principal de la app */
.app-container {
  background: linear-gradient(135deg, #667eea, #764ba2); /* Fondo de gradiente suave */
  min-height: 100vh; /* Asegura que ocupe toda la pantalla */
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 20px;
}

/* Contenedor que envuelve el contenido */
.movie-container {
  background-color: #fff;
  border-radius: 20px;
  padding: 30px;
  width: 100%;
  max-width: 900px;
  box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2); /* Sombra suave */
  text-align: center;
  transition: all 0.3s ease-in-out;
}

.movie-container:hover {
  transform: scale(1.05); /* Efecto de escala al pasar el ratón */
  box-shadow: 0 12px 24px rgba(0, 0, 0, 0.3); /* Sombra más intensa */
}

/* Título */
.title {
  font-size: 36px;
  font-weight: 700;
  color: #333;
  margin-bottom: 20px;
  text-transform: uppercase;
  letter-spacing: 2px; /* Espaciado entre letras */
  font-family: 'Poppins', sans-serif;
}

/* Media queries para hacer la app más responsiva */
@media (max-width: 768px) {
  .title {
    font-size: 28px;
  }

  .movie-container {
    padding: 20px;
  }
}

@media (max-width: 480px) {
  .title {
    font-size: 24px;
  }

  .movie-container {
    padding: 15px;
  }
}
</style>
