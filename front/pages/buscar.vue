<template>
  <Navbar />
   
  <div class="search-page">
    <button @click="goBack" class="back-button">
      ⬅ Tornar
    </button>
    <div class="search-container">
      <h1>Cercar Pel·lícules</h1>
      
      <!-- Filtres de cerca -->
      <div class="filters">
        <div class="search-bar">
          <input 
            type="text" 
            v-model="searchQuery" 
            placeholder="Cercar per títol, director o actors..."
            @input="searchMovies"
          />
          <button @click="searchMovies">🔍</button>
        </div>
        
        <div class="filter-group">
          <label>Gènere:</label>
          <select v-model="selectedGenre" @change="searchMovies">
            <option value="">Tots</option>
            <option v-for="genre in genres" :key="genre" :value="genre">{{ genre }}</option>
          </select>
        </div>
        
        <div class="filter-group">
          <label>Any:</label>
          <select v-model="selectedYear" @change="searchMovies">
            <option value="">Tots</option>
            <option v-for="year in years" :key="year" :value="year">{{ year }}</option>
          </select>
        </div>
        
        <div class="filter-group">
          <label>Idioma:</label>
          <select v-model="selectedLanguage" @change="searchMovies">
            <option value="">Tots</option>
            <option v-for="lang in languages" :key="lang" :value="lang">{{ lang }}</option>
          </select>
        </div>
        
        <div class="filter-group">
          <label>Format:</label>
          <select v-model="selectedFormat" @change="searchMovies">
            <option value="">Tots</option>
            <option v-for="format in formats" :key="format" :value="format">{{ format }}</option>
          </select>
        </div>
        
        <div class="filter-group checkbox-group">
          <label>
            <input type="checkbox" v-model="onlyStreaming" @change="searchMovies" />
            Només disponibles en streaming
          </label>
        </div>
      </div>
      
      <!-- Resultats de cerca -->
      <div class="results">
        <div v-if="loading" class="loading">Carregant pel·lícules...</div>
        
        <div v-else-if="filteredMovies.length === 0" class="no-results">
          No es van trobar pel·lícules que coincideixin amb els teus criteris de cerca.
        </div>
        
        <div v-else class="movies-grid">
          <div 
            v-for="movie in filteredMovies" 
            :key="movie.id" 
            class="movie-card"
            @click="goToMovie(movie.id)"
          >
            <div class="movie-poster">
              <img :src="movie.poster_url || 'https://via.placeholder.com/300x450'" :alt="movie.title" />
              <div v-if="movie.disponible_en_streaming" class="streaming-badge">STREAMING</div>
            </div>
            <div class="movie-info">
              <h3>{{ movie.title }}</h3>
              <p class="movie-year">{{ movie.año }}</p>
              <p class="movie-genre">{{ movie.genero }}</p>
              <p class="movie-director">Director: {{ movie.director }}</p>
              <div class="movie-meta">
                <span>{{ movie.duracion }}</span>
                <span>{{ movie.idioma }}</span>
                <span>{{ movie.formato }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

  
<script>
import { ref, onMounted, computed } from 'vue';
import communicationManager from '@/services/communicationManager';
import Navbar from '@/components/Navbar.vue';
import { useRoute, useRouter } from 'vue-router';

export default {
  components: {
    Navbar
  },
  setup() {
    const router = useRouter();
    const allMovies = ref([]);
    const loading = ref(true);
    const searchQuery = ref('');
    const selectedGenre = ref('');
    const selectedYear = ref('');
    const selectedLanguage = ref('');
    const selectedFormat = ref('');
    const onlyStreaming = ref(false);

    const genres = computed(() => {
      const uniqueGenres = new Set();
      allMovies.value.forEach(movie => {
        if (movie.genero) uniqueGenres.add(movie.genero);
      });
      return Array.from(uniqueGenres).sort();
    });

    const years = computed(() => {
      const uniqueYears = new Set();
      allMovies.value.forEach(movie => {
        if (movie.año) uniqueYears.add(movie.año);
      });
      return Array.from(uniqueYears).sort((a, b) => b - a);
    });

    const languages = computed(() => {
      const uniqueLangs = new Set();
      allMovies.value.forEach(movie => {
        if (movie.idioma) uniqueLangs.add(movie.idioma);
      });
      return Array.from(uniqueLangs).sort();
    });

    const formats = computed(() => {
      const uniqueFormats = new Set();
      allMovies.value.forEach(movie => {
        if (movie.formato) uniqueFormats.add(movie.formato);
      });
      return Array.from(uniqueFormats).sort();
    });

    const filteredMovies = computed(() => {
      return allMovies.value.filter(movie => {
        const searchText = searchQuery.value.toLowerCase();
        const matchesSearch = 
          !searchText ||
          movie.title.toLowerCase().includes(searchText) ||
          movie.director.toLowerCase().includes(searchText) ||
          (movie.actores && movie.actores.toLowerCase().includes(searchText)) ||
          (movie.sinopsis && movie.sinopsis.toLowerCase().includes(searchText));

        const matchesGenre = !selectedGenre.value || movie.genero === selectedGenre.value;
        const matchesYear = !selectedYear.value || movie.año == selectedYear.value;
        const matchesLanguage = !selectedLanguage.value || movie.idioma === selectedLanguage.value;
        const matchesFormat = !selectedFormat.value || movie.formato === selectedFormat.value;
        const matchesStreaming = !onlyStreaming.value || movie.disponible_en_streaming;

        return matchesSearch && matchesGenre && matchesYear && 
               matchesLanguage && matchesFormat && matchesStreaming;
      });
    });

    const fetchMovies = async () => {
      try {
        loading.value = true;
        const movies = await communicationManager.getAllMovies();
        allMovies.value = movies;
      } catch (error) {
        console.error('Error al cargar películas:', error);
      } finally {
        loading.value = false;
      }
    };

    const searchMovies = () => {
      console.log('Buscando películas con filtros:', {
        searchQuery: searchQuery.value,
        genre: selectedGenre.value,
        year: selectedYear.value,
        language: selectedLanguage.value,
        format: selectedFormat.value,
        streaming: onlyStreaming.value
      });
    };

    const goToMovie = (movieId) => {
      router.push(`/movies/${movieId}`);
    };

    function goBack() {
      router.go(-1);
    }

    onMounted(fetchMovies);

    return {
      goBack,
      allMovies,
      loading,
      searchQuery,
      selectedGenre,
      selectedYear,
      selectedLanguage,
      selectedFormat,
      onlyStreaming,
      genres,
      years,
      languages,
      formats,
      filteredMovies,
      searchMovies,
      goToMovie
    };
  }
};
</script>

  
  <style scoped>
.back-button {
  background-color: #2b2d42;
  color: #8d99ae;
  border: 2px solid #8d99ae;
  padding: 10px 20px;
  border-radius: 8px;
  font-weight: bold;
  cursor: pointer;
  transition: all 0.3s;
  margin-bottom: 20px;
  font-size: 1rem;
}

.back-button:hover {
  color: #ef233c;
  border-color: #ef233c;
}
  .search-page {
    background-color: #2b2d42;
    min-height: 100vh;
    color: #edf2f4;
    padding: 20px;
  }
  
  .search-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
  }
  
  h1 {
    color: #ef233c;
    margin-bottom: 30px;
    text-align: center;
  }
  
  .filters {
    background-color: #8d99ae;
    padding: 20px;
    border-radius: 10px;
    margin-bottom: 30px;
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 20px;
  }
  
  .search-bar {
    grid-column: 1 / -1;
    display: flex;
  }
  
  .search-bar input {
    flex-grow: 1;
    padding: 12px 15px;
    border: 2px solid #2b2d42;
    border-radius: 6px 0 0 6px;
    font-size: 16px;
    background-color: #edf2f4;
    color: #2b2d42;
  }
  
  .search-bar button {
    padding: 0 20px;
    background-color: #ef233c;
    color: white;
    border: none;
    border-radius: 0 6px 6px 0;
    cursor: pointer;
    font-size: 18px;
    transition: background-color 0.3s;
  }
  
  .search-bar button:hover {
    background-color: #d80032;
  }
  
  .filter-group {
    display: flex;
    flex-direction: column;
  }
  
  .filter-group label {
    margin-bottom: 8px;
    font-weight: bold;
    color: #2b2d42;
  }
  
  .filter-group select {
    padding: 10px;
    border-radius: 6px;
    border: 2px solid #2b2d42;
    background-color: #edf2f4;
    color: #2b2d42;
  }
  
  .checkbox-group {
    justify-content: center;
  }
  
  .checkbox-group label {
    display: flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
  }
  
  .checkbox-group input {
    width: 18px;
    height: 18px;
  }
  
  .results {
    margin-top: 20px;
  }
  
  .loading, .no-results {
    text-align: center;
    padding: 40px;
    font-size: 18px;
    color: #8d99ae;
  }
  
  .movies-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 25px;
  }
  
  .movie-card {
    background-color: #edf2f4;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    transition: transform 0.3s, box-shadow 0.3s;
    cursor: pointer;
    color: #2b2d42;
  }
  
  .movie-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.3);
  }
  
  .movie-poster {
    position: relative;
    height: 0;
    padding-bottom: 100%;
    overflow: hidden;
  }
  
  .movie-poster img {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
  }
  
  .streaming-badge {
    position: absolute;
    top: 10px;
    right: 10px;
    background-color: #ef233c;
    color: white;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: bold;
  }
  
  .movie-info {
    padding: 15px;
  }
  
  .movie-info h3 {
    margin: 0 0 5px 0;
    font-size: 18px;
    color: #2b2d42;
  }
  
  .movie-year, .movie-genre, .movie-director {
    margin: 5px 0;
    font-size: 14px;
  }
  
  .movie-meta {
    display: flex;
    justify-content: space-between;
    margin-top: 10px;
    font-size: 12px;
    color: #8d99ae;
  }
  
  @media (max-width: 768px) {
    .filters {
      grid-template-columns: 1fr;
    }
    
    .movies-grid {
      grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    }
  }
  
  @media (max-width: 480px) {
    .movies-grid {
      grid-template-columns: 1fr;
    }
  }
  </style>