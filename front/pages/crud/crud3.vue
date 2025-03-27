<template>
  <Navbar />
  
  <div class="links">
    <button @click="goBack" class="back-link">
      ⬅ Volver
    </button>
    <NuxtLink to="crud1" class="login-link">
      Consulta Administrativa
        </NuxtLink>
    <NuxtLink to="crud2" class="login-link">
      Administrar Streaming y Sessions
        </NuxtLink>
  </div>

  <div>
    <!-- Sección de Listado de Películas -->
    <div v-if="currentView === 'list'">
      <h1>Lista de Películas</h1>
      <table>
        <thead>
          <tr>
            <th>Título</th>
            <th>Sinopsis</th>
            <th>Duración</th>
            <th>Director</th>
            <th>Actores</th>
            <th>Año</th>
            <th>Género</th>
            <th>Póster</th>
            <th>Trailer</th>
            <th>Idioma</th>
            <th>Subtítulos</th>
            <th>Formato</th>
            <th>Streaming</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="movie in movies" :key="movie.id">
            <td>{{ movie.title }}</td>
            <td>{{ movie.sinopsis }}</td>
            <td>{{ movie.duracion }}</td>
            <td>{{ movie.director }}</td>
            <td>{{ movie.actores }}</td>
            <td>{{ movie.año }}</td>
            <td>{{ movie.genero }}</td>
            <td>
              <img :src="movie.poster_url" alt="Póster" width="50" />
            </td>
          <!-- Mostrar el trailer si existe -->
<td>
  <a v-if="movie.trailer_url" :href="movie.trailer_url" target="_blank">Ver Trailer</a>
  <video v-if="movie.trailer_url" :src="movie.trailer_url" controls width="200"></video>
</td>

            <td>{{ movie.idioma }}</td>
            <td>{{ movie.subtitulos ? 'Sí' : 'No' }}</td>
            <td>{{ movie.formato }}</td>
            <td>{{ movie.disponible_en_streaming ? 'Sí' : 'No' }}</td>
            <td>
              <button @click="editMovie(movie.id)">Editar</button>
              <button @click="deleteMovie(movie.id)">Eliminar</button>
            </td>
          </tr>
        </tbody>
      </table>
      <button @click="currentView = 'create'">Crear Nueva Película</button>
    </div>

    <!-- Sección para Crear o Editar Película -->
    <div v-if="currentView === 'create' || currentView === 'edit'">
      <h1>{{ currentView === 'edit' ? 'Editar Película' : 'Crear Nueva Película' }}</h1>
      <form @submit.prevent="currentView === 'edit' ? updateMovie() : createMovie">
        <div>
          <label for="title">Título</label>
          <input type="text" id="title" v-model="newMovie.title" required />
        </div>
        <div>
          <label for="sinopsis">Sinopsis</label>
          <textarea id="sinopsis" v-model="newMovie.sinopsis" required></textarea>
        </div>
        <div>
          <label for="duracion">Duración</label>
          <input type="text" id="duracion" v-model="newMovie.duracion" required />
        </div>
        <div>
          <label for="director">Director</label>
          <input type="text" id="director" v-model="newMovie.director" required />
        </div>
        <div>
          <label for="actores">Actores</label>
          <input type="text" id="actores" v-model="newMovie.actores" required />
        </div>
        <div>
          <label for="año">Año</label>
          <input type="number" id="año" v-model="newMovie.año" required />
        </div>
        <div>
          <label for="genero">Género</label>
          <input type="text" id="genero" v-model="newMovie.genero" required />
        </div>
        <div>
          <label for="poster">Póster</label>
          <input type="file" @change="uploadImage" />
          <div v-if="imageUrl">
            <img :src="imageUrl" alt="Uploaded image" width="100" />
          </div>
        </div>
        <!-- Campo para el trailer en el formulario de creación/edición -->
<div>
  <label for="trailer">Subir Trailer (Video)</label>
  <input type="file" @change="uploadTrailer" accept="video/*" />
  <div v-if="videoUrl">
    <video v-if="movie.trailer_url" :src="movie.trailer_url" controls width="200"></video>
  </div>
</div>

        <div>
          <label for="idioma">Idioma</label>
          <input type="text" id="idioma" v-model="newMovie.idioma" />
        </div>
        <div>
          <label for="subtitulos">Subtítulos</label>
          <input type="checkbox" id="subtitulos" v-model="newMovie.subtitulos" />
        </div>
        <div>
          <label for="formato">Formato</label>
          <input type="text" id="formato" v-model="newMovie.formato" />
        </div>
        <div>
          <label for="disponible_en_streaming">Disponible en Streaming</label>
          <input type="checkbox" id="disponible_en_streaming" v-model="newMovie.disponible_en_streaming" />
        </div>
        <button type="submit">{{ currentView === 'edit' ? 'Actualizar' : 'Crear' }}</button>
        <button @click="currentView = 'list'">Cancelar</button>
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
    };
  },
  methods: {
    async uploadTrailer(event) {
  const file = event.target.files[0];
  if (!file) return;

  // Validar tamaño del archivo
  if (file.size > 100 * 1024 * 1024) { // 100MB
    alert('El tamaño del video no puede exceder los 100MB');
    return;
  }

  const formData = new FormData();
  formData.append('file', file);
  formData.append('upload_preset', 'cinema'); // Tu preset de carga en Cloudinary

  try {
    const response = await fetch('https://api.cloudinary.com/v1_1/dbculqlgg/video/upload', {
      method: 'POST',
      body: formData,
    });

    const data = await response.json();

    if (data.secure_url) {
      console.log('URL del video:', data.secure_url); // Verifica la URL del video
      this.newMovie.trailer_url = data.secure_url;  // Guarda la URL del video
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
    
    // Imprime la URL de la imagen en la consola
    console.log('URL de la imagen:', this.imageUrl);

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

    createMovie() {
  console.log('Creando película con los siguientes datos:', this.newMovie); // Esto te ayudará a ver los datos que se están enviando
  communicationManager.createMovie(this.newMovie)
    .then((data) => {
      console.log('Película creada con éxito:', data); // Verifica la respuesta
      this.movies.push(data); // Asegúrate de que la película se esté agregando a la lista
      this.resetNewMovie();
      this.currentView = 'list'; // Cambiar de vista
    })
    .catch((error) => {
      console.error('Error al crear la película:', error); // Verifica si hay algún error
    });
},
goBack() {
  this.$router.go(-1); // Usa this.$router en lugar de router
}
,
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

    deleteMovie(id) {
      communicationManager.deleteMovie(id)
        .then(() => {
          this.movies = this.movies.filter((movie) => movie.id !== id);
          this.currentView = 'list';
        })
        .catch((error) => {
          console.error('Error al eliminar la película:', error);
        });
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
.links {
  margin-bottom: 20px;
  padding: 20px;
}

.back-link {
  display: inline-block;
  padding: 10px 20px;
  background-color: #2b2d42;
  color: #8d99ae;
  border: 2px solid #8d99ae;
  border-radius: 8px;
  text-decoration: none;
  font-weight: bold;
  margin-right: 15px;
  transition: all 0.3s;
}

.back-link:hover {
  color: #ef233c;
  border-color: #ef233c;
}

.login-link {
  display: inline-block;
  padding: 10px 20px;
  background-color: #2b2d42;
  color: #edf2f4;
  border-radius: 8px;
  text-decoration: none;
  font-weight: bold;
  margin-right: 15px;
  transition: all 0.3s;
}

.login-link:hover {
  background-color: #ef233c;
}

table {
  width: 100%;
  border-collapse: collapse;
  margin: 20px 0;
  background-color: #edf2f4;
}

th, td {
  padding: 12px 15px;
  text-align: left;
  border-bottom: 1px solid #8d99ae;
}

th {
  background-color: #2b2d42;
  color: #edf2f4;
  font-weight: bold;
}

tr:hover {
  background-color: rgba(141, 153, 174, 0.1);
}

button {
  padding: 8px 16px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-weight: bold;
  margin-right: 5px;
  transition: all 0.3s;
}

button:hover {
  transform: translateY(-2px);
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
}

button[type="submit"] {
  background-color: #2b2d42;
  color: #edf2f4;
}

button[type="submit"]:hover {
  background-color: #ef233c;
}

button:not([type="submit"]) {
  background-color: #8d99ae;
  color: #2b2d42;
}

button:not([type="submit"]):hover {
  background-color: #d80032;
  color: #edf2f4;
}

form {
  background-color: #edf2f4;
  padding: 20px;
  border-radius: 8px;
  margin: 20px 0;
}

label {
  display: block;
  margin-bottom: 8px;
  font-weight: bold;
  color: #2b2d42;
}

input, textarea, select {
  width: 100%;
  padding: 10px;
  margin-bottom: 15px;
  border: 2px solid #8d99ae;
  border-radius: 4px;
  background-color: #edf2f4;
  color: #2b2d42;
}

input:focus, textarea:focus, select:focus {
  border-color: #ef233c;
  outline: none;
}

input[type="checkbox"] {
  width: auto;
  margin-right: 10px;
}

img {
  max-width: 100px;
  height: auto;
  border-radius: 4px;
}

video {
  max-width: 200px;
  border-radius: 4px;
}

@media (max-width: 768px) {
  table {
    display: block;
    overflow-x: auto;
  }
  
  form {
    padding: 15px;
  }
}
</style>