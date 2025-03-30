<template>
  <Navbar />

  <div class="movie-page">
    <button @click="goBack" class="back-button">
      ⬅ Tornar
    </button>
    <div v-if="movie" class="movie-content">
      <!-- Capçalera de la pel·lícula -->
      <div class="movie-header">
        <h1 class="movie-title">{{ movie.title }}</h1>

        <div v-if="isLoading" class="loading-sessions">
          Verificant disponibilitat...
        </div>
        <template v-else>
          <NuxtLink 
            v-if="isAvailableForStreaming && hasAvailableSessions" 
            :to="`/buy-ticket/${movie.id}`"
            class="btn-buy">
            🎟️ Veure Butaques i Sessions
          </NuxtLink>
          
          <div v-else-if="isAvailableForStreaming && !hasAvailableSessions" class="proximament">
            🎬 No hi ha sessions disponibles actualment
            <p class="subtext">
              Ho sentim, no hi ha funcions programades per a aquesta pel·lícula.
            </p>
          </div>
          
          <div v-else class="proximament">
            🎬 No disponible actualment
            <p class="subtext">
              Aquesta pel·lícula no està disponible en streaming.
            </p>
          </div>
        </template>
      </div>

      <!-- Resta del contingut... -->
      <div class="movie-main">
        <img :src="movie.poster_url" alt="Poster" class="movie-poster" />
        <div class="movie-info">
          <div class="details">
            <p><strong>Director:</strong> {{ movie.director }}</p>
            <p><strong>Actors:</strong> {{ movie.actores }}</p>
            <p><strong>Any:</strong> {{ movie.año }}</p>
            <p><strong>Gènere:</strong> {{ movie.genero }}</p>
            <p><strong>Idioma:</strong> {{ movie.idioma }}</p>
            <p><strong>Subtítols:</strong> {{ movie.subtitulos ? 'Sí' : 'No' }}</p>
            <p><strong>Format:</strong> {{ movie.formato }}</p>
            <p><strong>Streaming:</strong> {{ movie.disponible_en_streaming === '1' ? 'Disponible' : 'No disponible' }}</p>
            <p><strong>Duració:</strong> {{ movie.duracion }}</p>
          </div>
        </div>
      </div>

      <div class="sinopsis">
        <h4>Sinopsi</h4>
        <p>{{ movie.sinopsis }}</p>
      </div>

      <div v-if="movie.trailer_url" class="trailer">
        <h4>Tràiler</h4>
        <video :src="movie.trailer_url" controls class="vid-trailer"></video>
      </div>
    </div>

    <div v-else class="loading">Carregant detalls de la pel·lícula...</div>
  </div>
</template>


<script setup>
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';

const router = useRouter();
const config = useRuntimeConfig();
const route = useRoute();
const movie = ref(null);
const hasAvailableSessions = ref(false);
const movieId = route.params.id;
const isLoading = ref(true);

function goBack() {
  router.go(-1);
}
// Computed para simplificar la condición
const isAvailableForStreaming = ref(false);

async function fetchMovie() {
  try {
    // 1. Obtener datos de la película
    movie.value = await $fetch(`${config.public.apiBase}/movies/${movieId}`);
    
    // Verificar el valor real de streaming (convertir a string por si acaso)
    isAvailableForStreaming.value = String(movie.value.disponible_en_streaming) === '1';
    
    console.log('Streaming status:', {
      rawValue: movie.value.disponible_en_streaming,
      converted: String(movie.value.disponible_en_streaming),
      isAvailable: isAvailableForStreaming.value
    });

    // 2. Verificar sesiones solo si está disponible en streaming
    if (isAvailableForStreaming.value) {
      const sessions = await $fetch(`${config.public.apiBase}/sessions/movie/${movieId}`);
      hasAvailableSessions.value = Array.isArray(sessions) && sessions.length > 0;
      
      console.log('Sessions data:', {
        sessionsCount: sessions?.length || 0,
        hasSessions: hasAvailableSessions.value
      });
    }
    
  } catch (error) {
    console.error('Error:', error);
  } finally {
    isLoading.value = false;
  }
}

onMounted(fetchMovie);
</script>

<style scoped>
/* Tus estilos existentes... */
.movie-page {
  background-color: #2b2d42;
  min-height: 100vh;
  padding: 15px;
  color: #edf2f4;
  display: flex;
  flex-direction: column;
  align-items: center;
}

.back-button {
  background-color: #2b2d42;
  color: #8d99ae;
  border: 2px solid #8d99ae;
  padding: 8px 15px;
  border-radius: 6px;
  font-weight: bold;
  cursor: pointer;
  margin-bottom: 15px;
  font-size: 14px;
  margin-right: 840px;
}

.movie-content {
  background-color: #edf2f4;
  border-radius: 10px;
  padding: 15px;
  color: #2b2d42;
  max-width: 900px;
  width: 100%;
}

.movie-header {
  margin-bottom: 20px;
  text-align: center;
}

.movie-title {
  color: #d80032;
  font-size: 22px;
  margin-bottom: 12px;
  word-break: break-word;
}

.btn-buy {
  background-color: #2b2d42;
  color: #edf2f4;
  padding: 10px 15px;
  border-radius: 6px;
  text-decoration: none;
  font-weight: bold;
  display: inline-block;
  font-size: 14px;
}

.proximamente {
  background-color: #691e06;
  color: white;
  padding: 12px;
  border-radius: 6px;
  font-size: 16px;
  margin: 15px 0;
}

.subtext {
  font-size: 14px;
  margin-top: 8px;
  color: #f8f9fa;
}

.movie-main {
  display: flex;
  flex-direction: column;
  gap: 20px;
  margin-bottom: 20px;
  align-items: center;
}

.movie-poster {
  width: 100%;
  max-width: 300px;
  border-radius: 8px;
}

.movie-info {
  width: 100%;
  max-width: 500px;
}

.details {
  background-color: white;
  padding: 15px;
  border-radius: 8px;
  font-size: 14px;
}

.sinopsis,
.trailer {
  background-color: white;
  padding: 15px;
  border-radius: 8px;
  margin-bottom: 20px;
  font-size: 14px;
  text-align: justify;
}

.vid-trailer {
  width: 100%;
  border-radius: 8px;
}

@media (min-width: 768px) {
  .movie-main {
    flex-direction: row;
    align-items: flex-start;
  }

  .movie-poster {
    max-width: 350px;
  }
}

@media (min-width: 1024px) {
  .movie-content {
    padding: 20px;
  }

  .movie-title {
    font-size: 28px;
  }

  .btn-buy {
    font-size: 16px;
    padding: 12px 18px;
  }
}
</style>