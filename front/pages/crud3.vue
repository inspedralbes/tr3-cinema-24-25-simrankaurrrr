<template>
  <Navbar />

  <div class="nav-links">
    <button @click="goBack" class="back-link">
      ⬅ Volver
    </button>
    <div class="crud-links">
      <NuxtLink to="crud1" class="crud-link">
        Consulta Administrativa
      </NuxtLink>
      <NuxtLink to="crud2" class="crud-link">
        Administrar Streaming y Sessions

      </NuxtLink>
    </div>
  </div>

  <div v-if="showAlert" :class="['custom-alert', alertType]">
    <div class="alert-content">
      <p>{{ alertMessage }}</p>
      <div v-if="alertType === 'confirm'" class="confirm-buttons">
        <button @click="confirmAlert(true)" class="confirm-btn yes">Sí</button>
        <button @click="confirmAlert(false)" class="confirm-btn no">No</button>
      </div>
      <button v-else @click="showAlert = false" class="alert-close-btn">×</button>
    </div>
  </div>

  <div class="container">
    <!-- Sección de Listado de Películas -->
    <div v-if="currentView === 'list'" class="movie-list">
      <div class="list-header">
        <h1>Lista de Películas</h1>
        <button @click="currentView = 'create'" class="create-btn">
          + Nueva Película
        </button>
      </div>

      <div class="movie-table-container">
        <table class="movie-table">
          <thead>
            <tr>
              <th>Título</th>
              <th class="mobile-hidden">Sinopsis</th>
              <th class="mobile-hidden">Duración</th>
              <th class="mobile-hidden">Director</th>
              <th class="mobile-hidden">Actores</th>
              <th class="mobile-hidden">Año</th>
              <th>Género</th>
              <th>Póster</th>
              <th class="mobile-hidden">Trailer</th>
              <th class="mobile-hidden">Idioma</th>
              <th>Subs</th>
              <th class="mobile-hidden">Formato</th>
              <th>Stream</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="movie in movies" :key="movie.id">
              <td>{{ movie.title }}</td>
              <td class="mobile-hidden">{{ truncateText(movie.sinopsis, 30) }}</td>
              <td class="mobile-hidden">{{ movie.duracion }}</td>
              <td class="mobile-hidden">{{ truncateText(movie.director, 15) }}</td>
              <td class="mobile-hidden">{{ truncateText(movie.actores, 15) }}</td>
              <td class="mobile-hidden">{{ movie.año }}</td>
              <td>{{ truncateText(movie.genero, 10) }}</td>
              <td>
                <img :src="movie.poster_url" alt="Póster" class="poster-img" />
              </td>
              <td class="mobile-hidden">
                <a v-if="movie.trailer_url" :href="movie.trailer_url" target="_blank" class="trailer-link">Ver</a>
              </td>
              <td class="mobile-hidden">{{ movie.idioma }}</td>
              <td>{{ movie.subtitulos ? 'Sí' : 'No' }}</td>
              <td class="mobile-hidden">{{ movie.formato }}</td>
              <td>{{ movie.disponible_en_streaming ? 'Sí' : 'No' }}</td>
              <td class="actions">
                <button @click="editMovie(movie.id)" class="action-btn edit">✏️</button>
                <button @click="deleteMovie(movie.id)" class="action-btn delete">🗑️</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Sección para Crear o Editar Película -->
    <div v-if="currentView === 'create' || currentView === 'edit'" class="movie-form">
      <h1>{{ currentView === 'edit' ? 'Editar Película' : 'Crear Nueva Película' }}</h1>
      <form @submit.prevent="currentView === 'edit' ? updateMovie() : createMovie" class="form-grid">
        <div class="form-group">
          <label for="title">Título</label>
          <input type="text" id="title" v-model="newMovie.title" required />
        </div>
        
        <div class="form-group full-width">
          <label for="sinopsis">Sinopsis</label>
          <textarea id="sinopsis" v-model="newMovie.sinopsis" required></textarea>
        </div>
        
        <div class="form-group">
          <label for="duracion">Duración</label>
          <input type="text" id="duracion" v-model="newMovie.duracion" required />
        </div>
        
        <div class="form-group">
          <label for="director">Director</label>
          <input type="text" id="director" v-model="newMovie.director" required />
        </div>
        
        <div class="form-group">
          <label for="actores">Actores</label>
          <input type="text" id="actores" v-model="newMovie.actores" required />
        </div>
        
        <div class="form-group">
          <label for="año">Año</label>
          <input type="number" id="año" v-model="newMovie.año" required />
        </div>
        
        <div class="form-group">
          <label for="genero">Género</label>
          <input type="text" id="genero" v-model="newMovie.genero" required />
        </div>
        
        <div class="form-group full-width">
          <label for="poster">Póster</label>
          <input type="file" @change="uploadImage" class="file-input" />
          <div v-if="imageUrl" class="image-preview">
            <img :src="imageUrl" alt="Uploaded image" />
          </div>
        </div>
        
        <div class="form-group full-width">
          <label for="trailer">Subir Trailer (Video)</label>
          <input type="file" @change="uploadTrailer" accept="video/*" class="file-input" />
        </div>
        
        <div class="form-group">
          <label for="idioma">Idioma</label>
          <input type="text" id="idioma" v-model="newMovie.idioma" />
        </div>
        
        <div class="form-group checkbox-group">
          <input type="checkbox" id="subtitulos" v-model="newMovie.subtitulos" />
          <label for="subtitulos">Subtítulos</label>
        </div>
        
        <div class="form-group">
          <label for="formato">Formato</label>
          <input type="text" id="formato" v-model="newMovie.formato" />
        </div>
        
        <div class="form-group checkbox-group">
          <input type="checkbox" id="disponible_en_streaming" v-model="newMovie.disponible_en_streaming" />
          <label for="disponible_en_streaming">Streaming</label>
        </div>
        
        <div class="form-buttons full-width">
          <button type="submit" class="submit-btn">
            {{ currentView === 'edit' ? 'Actualizar' : 'Crear' }}
          </button>
          <button @click="currentView = 'list'" class="cancel-btn">Cancelar</button>
        </div>
      </form>
    </div>
  </div>
</template>


<script>
import communicationManager from '~/services/communicationManager';
import { useRoute, useRouter } from 'vue-router';

export default {
  data() {
    return {
      currentView: 'list',
      movies: [],
      newMovie: {
        title: '',
        sinopsis: '',
        duracion: '',
        director: '',
        actores: '',
        año: null,
        genero: '',
        poster_url: '',
        trailer_url: '',
        idioma: '',
        subtitulos: false,
        formato: '',
        disponible_en_streaming: false,
      },
      imageUrl: '',
      // Nuevas propiedades para las alertas
      showAlert: false,
      alertMessage: '',
      alertType: 'success' // 'success', 'error' o 'confirm'
    };
  },
  methods: {
    async uploadTrailer(event) {
      const file = event.target.files[0];
      if (!file) return;

      if (file.size > 100 * 1024 * 1024) {
        this.showAlert = true;
        this.alertMessage = 'El tamaño del video no puede exceder los 100MB';
        this.alertType = 'error';
        setTimeout(() => { this.showAlert = false; }, 3000);
        return;
      }

      const formData = new FormData();
      formData.append('file', file);
      formData.append('upload_preset', 'cinema');

      try {
        const response = await fetch('https://api.cloudinary.com/v1_1/dbculqlgg/video/upload', {
          method: 'POST',
          body: formData,
        });

        const data = await response.json();

        if (data.secure_url) {
          this.newMovie.trailer_url = data.secure_url;
        } else {
          console.error('No se ha recibido la URL del video.');
        }
      } catch (error) {
        console.error('Error al subir el video:', error);
      }
    },

    async uploadImage(event) {
      const file = event.target.files[0];
      if (!file) return;

      const formData = new FormData();
      formData.append('file', file);
      formData.append('upload_preset', 'cinema');

      try {
        const response = await fetch(`https://api.cloudinary.com/v1_1/dbculqlgg/image/upload`, {
          method: 'POST',
          body: formData,
        });

        const data = await response.json();
        this.imageUrl = data.secure_url;
        this.newMovie.poster_url = this.imageUrl;
      } catch (error) {
        console.error('Error al subir la imagen:', error);
      }
    },

    fetchMovies() {
      communicationManager.getAllMovies()
        .then((data) => {
          if (Array.isArray(data)) {
            this.movies = data;
          } else {
            console.error('Respuesta de la API no válida');
          }
        })
        .catch((error) => {
          console.error('Error al cargar las películas:', error);
        });
    },
    truncateText(text, length) {
    if (!text) return '';
    return text.length > length ? text.substring(0, length) + '...' : text;
  },


    createMovie() {
      communicationManager.createMovie(this.newMovie)
        .then((data) => {
          this.movies.push(data);
          this.resetNewMovie();
          this.currentView = 'list';
        })
        .catch((error) => {
          console.error('Error al crear la película:', error);
        });
    },

    goBack() {
      this.$router.go(-1);
    },

    editMovie(id) {
      this.newMovie = { ...this.movies.find((movie) => movie.id === id) };
      this.imageUrl = this.newMovie.poster_url || '';
      this.currentView = 'edit';
    },

    updateMovie() {
      communicationManager.updateMovie(this.newMovie.id, this.newMovie)
        .then(() => {
          const index = this.movies.findIndex((movie) => movie.id === this.newMovie.id);
          this.movies[index] = this.newMovie;
          this.resetNewMovie();
          this.currentView = 'list';
        })
        .catch((error) => {
          console.error('Error al actualizar la película:', error);
        });
    },

    async deleteMovie(id) {
      try {
        // Mostrar alerta de confirmación
        const confirmed = await this.showConfirmDialog(
          '¿Estás seguro de que deseas eliminar esta película?'
        );

        if (!confirmed) return;

        // Proceder con la eliminación
        await communicationManager.deleteMovie(id);
        this.movies = this.movies.filter((movie) => movie.id !== id);

        // Mostrar mensaje de éxito
        this.showAlertMessage('Película eliminada correctamente', 'success');

      } catch (error) {
        console.error('Error al eliminar la película:', error);
        this.showAlertMessage(
          'Error al eliminar la película. Por favor, inténtalo de nuevo.',
          'error'
        );

        if (error.response && error.response.status === 401) {
          this.$router.push('/login');
        }
      }
    },

    // Nuevo método para mostrar diálogos de confirmación
    showConfirmDialog(message) {
      return new Promise((resolve) => {
        this.alertMessage = message;
        this.alertType = 'confirm';
        this.showAlert = true;

        // Guardamos la función resolve para usarla cuando el usuario responda
        this.confirmResolve = resolve;
      });
    },

    // Método para mostrar mensajes simples
    showAlertMessage(message, type = 'success') {
      this.alertMessage = message;
      this.alertType = type;
      this.showAlert = true;

      if (type !== 'confirm') {
        setTimeout(() => {
          this.showAlert = false;
        }, 3000);
      }
    },

    // Método modificado para manejar la respuesta
    confirmAlert(confirmed) {
      if (this.confirmResolve) {
        this.confirmResolve(confirmed);
        this.confirmResolve = null;
      }
      this.showAlert = false;
    },

    resetNewMovie() {
      this.newMovie = {
        title: '',
        sinopsis: '',
        duracion: '',
        director: '',
        actores: '',
        año: null,
        genero: '',
        poster_url: '',
        trailer_url: '',
        idioma: '',
        subtitulos: false,
        formato: '',
        disponible_en_streaming: false,
      };
      this.imageUrl = '';
    },
  },
  created() {
    this.fetchMovies();
  },
};
</script>

<style scoped>
/* Estilos base */
.container {
  padding: 15px;
  max-width: 100%;
  overflow-x: hidden;
}

.nav-links {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
  margin: 15px 0;
  padding: 0 15px;
}

.crud-links {
  display: flex;
  gap: 10px;
}

.back-link, .crud-link {
  padding: 10px 15px;
  border-radius: 6px;
  font-weight: bold;
  text-decoration: none;
  font-size: 14px;
  transition: all 0.3s;
}

.back-link {
  background-color: #2b2d42;
  color: #8d99ae;
  border: 2px solid #8d99ae;
}

.back-link:hover {
  color: #ef233c;
  border-color: #ef233c;
}

.crud-link {
  background-color: #2b2d42;
  color: #edf2f4;
}

.crud-link:hover {
  background-color: #ef233c;
}

/* Lista de películas */
.movie-list {
  width: 100%;
}

.list-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 15px;
  flex-wrap: wrap;
  gap: 10px;
}

.create-btn {
  padding: 8px 12px;
  background-color: #4CAF50;
  color: white;
  border: none;
  border-radius: 4px;
  font-weight: bold;
}

.movie-table-container {
  width: 100%;
  overflow-x: auto;
  -webkit-overflow-scrolling: touch;
}

.movie-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 14px;
}

.movie-table th, 
.movie-table td {
  padding: 8px 10px;
  text-align: left;
  border-bottom: 1px solid #ddd;
}

.movie-table th {
  background-color: #2b2d42;
  color: white;
  position: sticky;
  top: 0;
}

.poster-img {
  width: 40px;
  height: auto;
  border-radius: 4px;
}

.actions {
  display: flex;
  gap: 5px;
}

.action-btn {
  padding: 5px 8px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-size: 14px;
}

.action-btn.edit {
  background-color: #FFC107;
  color: #000;
}

.action-btn.delete {
  background-color: #F44336;
  color: white;
}

.trailer-link {
  color: #2196F3;
  text-decoration: none;
}

/* Formulario */
.movie-form {
  width: 100%;
}

.form-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
  gap: 15px;
}

.form-group {
  margin-bottom: 10px;
}

.full-width {
  grid-column: 1 / -1;
}

.form-group label {
  display: block;
  margin-bottom: 5px;
  font-weight: bold;
  font-size: 14px;
}

.form-group input,
.form-group textarea,
.form-group select {
  width: 100%;
  padding: 8px;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 14px;
}

.form-group textarea {
  min-height: 80px;
}

.checkbox-group {
  display: flex;
  align-items: center;
  gap: 8px;
}

.checkbox-group input {
  width: auto;
}

.file-input {
  width: 100%;
  padding: 8px 0;
}

.image-preview img {
  max-width: 100px;
  height: auto;
  margin-top: 10px;
  border-radius: 4px;
}

.form-buttons {
  display: flex;
  gap: 10px;
  margin-top: 15px;
}

.submit-btn, .cancel-btn {
  padding: 10px 15px;
  border: none;
  border-radius: 4px;
  font-weight: bold;
  cursor: pointer;
}

.submit-btn {
  background-color: #2b2d42;
  color: white;
}

.cancel-btn {
  background-color: #8d99ae;
  color: #2b2d42;
}

/* Alertas */
.custom-alert {
  position: fixed;
  top: 20px;
  right: 15px;
  left: 15px;
  padding: 15px;
  border-radius: 8px;
  color: white;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  z-index: 9999;
  animation: slideIn 0.3s ease-out;
  max-width: calc(100% - 30px);
}

/* Media queries para móviles */
@media (max-width: 768px) {
  .mobile-hidden {
    display: none;
  }
  
  .movie-table th, 
  .movie-table td {
    padding: 6px 8px;
    font-size: 13px;
  }
  
  .form-grid {
    grid-template-columns: 1fr;
  }
  
  .nav-links {
    flex-direction: column;
  }
  
  .crud-links {
    flex-wrap: wrap;
  }
}

@media (max-width: 480px) {
  .container {
    padding: 10px;
  }
  
  .list-header {
    flex-direction: column;
    align-items: flex-start;
  }
  
  .create-btn {
    width: 100%;
  }
  
  .form-buttons {
    flex-direction: column;
  }
  
  .submit-btn, .cancel-btn {
    width: 100%;
  }
}

@keyframes slideIn {
  from {
    transform: translateY(-20px);
    opacity: 0;
  }
  to {
    transform: translateY(0);
    opacity: 1;
  }
}
</style>