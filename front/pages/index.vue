<template>
      <Navbar />

  <div class="app-container">
    <div class="movie-container">
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

function logout() {
  localStorage.removeItem('auth_token');
  alert('Has cerrado sesión exitosamente');
}

const checkUserRole = async () => {
  const token = localStorage.getItem('auth_token');
  if (token) {
    try {
      const user = await communicationManager.getCurrentUser();
      isAdmin.value = user.role === 'admin';
    } catch (error) {
      console.error('Error al verificar el rol:', error);
    }
  } else {
    isAdmin.value = false;
  }
};

onMounted(() => {
  checkUserRole();
});
</script>

<style scoped>
html, body {
  height: 100%;
  width: 100%;
  overflow-x: hidden;
}

.app-container {
  background-color: #2b2d42;
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: flex-start;
  width: 100%;
}

/* Contenedor de la película, ahora sin márgenes ni padding */
.movie-container {
  width: 100%;
  margin-top: 0;
  height: 100%;
  overflow: hidden;
}
</style>
