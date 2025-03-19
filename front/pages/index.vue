<template>
  <div class="app-container">
    <Navbar />

    

    <div class="movie-container">
      <h1 class="title">Películas Recomendadas</h1>
      <MovieList />
    </div>
  </div>
</template>

<script setup lang="ts">
import MovieList from '../components/MovieList.vue';
import { useRouter } from 'vue-router';
import communicationManager from '~/services/communicationManager';

const router = useRouter();
const isAdmin = ref(false);

// Método de logout
function logout() {
  localStorage.removeItem('auth_token');
  alert('Has cerrado sesión exitosamente');
}

// Verifica el rol del usuario
const checkUserRole = async () => {
  const token = localStorage.getItem('auth_token');
  if (token) {
    try {
      const user = await communicationManager.getCurrentUser(); // Recuperar usuario autenticado
      isAdmin.value = user.role === 'admin'; // Verifica si es admin
    } catch (error) {
      console.error('Error al verificar el rol:', error);
    }
  } else {
    isAdmin.value = false; // Si no hay token, no es admin
  }
};

onMounted(() => {
  checkUserRole(); // Al cargar la página, verificamos el rol
});
</script>
