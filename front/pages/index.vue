<template>
  <div class="app-container">
    <!-- Componente de sesión -->
    <NuxtLink to="/register" class="text-blue-500 underline hover:text-blue-700 mb-6 inline-block">
      haz login
    </NuxtLink>

    <!-- Botón Logout -->
    <button @click="logout" class="logout-button">
      Logout
    </button>

    <div class="movie-container">
      <h1 class="title">Películas Recomendadas</h1>
      <MovieList />
    </div>
  </div>
</template>

<script setup lang="ts">
import MovieList from '../components/MovieList.vue';

// Método de logout
function logout() {
  // Eliminar el token de localStorage
  localStorage.removeItem('auth_token');

  // Mostrar un popup de confirmación (puedes cambiar el texto como lo desees)
  alert('Has cerrado sesión exitosamente');

  // Opcional: Puedes hacer cualquier otra cosa que necesites después del logout
}
</script>

<style scoped>
/* Contenedor principal de la app */
.app-container {
  background: linear-gradient(135deg, #667eea, #764ba2); /* Fondo de gradiente suave */
  min-height: 100vh; /* Asegura que ocupe toda la pantalla */
  display: flex;
  justify-content: center;
  align-items: center;
  flex-direction: column;  /* Cambiado a column para apilar los elementos */
  padding: 20px;
  gap: 20px;  /* Agregado espacio entre el botón y el contenedor de películas */
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

/* Estilos del botón Logout */
.logout-button {
  padding: 10px 20px;
  background-color: #ff4d4d;
  color: white;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  font-size: 16px;
  transition: background-color 0.3s ease;
}

.logout-button:hover {
  background-color: #ff1a1a;
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
