<template>
     <Navbar />

<NuxtLink to="/" class="text-blue-500 underline hover:text-blue-700 mb-6 inline-block">
    ⬅ Volver
  </NuxtLink>
<NuxtLink to="crud1" class="login-link">
          CRUD 1
        </NuxtLink>
        <NuxtLink to="crud2" class="login-link">
          CRUD 2
        </NuxtLink>
       
    <div>
      <!-- Sección de Listado de Películas -->
      <div v-if="currentView === 'list'">
        <h1>Lista de Películas</h1>
        <table border="1">
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
              <td>
                <a :href="movie.trailer_url" target="_blank">Ver Trailer</a>
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
  
      <!-- Sección para Crear Nueva Película -->
      <div v-if="currentView === 'create'">
        <h1>Crear Nueva Película</h1>
        <form @submit.prevent="createMovie">
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
          <div>
            <label for="trailer_url">URL del Trailer</label>
            <input type="text" id="trailer_url" v-model="newMovie.trailer_url" />
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
          <button type="submit">Crear</button>
          <button @click="currentView = 'list'">Cancelar</button>
        </form>
      </div>
    </div>
  </template>
  
  <script>
  import communicationManager from '~/services/communicationManager';
  
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
        communicationManager.getMovies()
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
        communicationManager.createMovie(this.newMovie)
          .then((data) => {
            this.movies.push(data);
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
            this.currentView = 'list';
          })
          .catch((error) => {
            console.error('Error al crear la película:', error);
          });
      },
  
      editMovie(id) {
        this.movie = this.movies.find((movie) => movie.id === id);
        this.imageUrl = this.movie.poster_url || '';
        this.currentView = 'edit';
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
    },
    created() {
      this.fetchMovies();
    },
  };
  </script>
  
  
  <style scoped>
  /* Estilos básicos para las vistas */
  button {
    margin-left: 10px;
  }
  
  form {
    margin-top: 20px;
  }
  
  label {
    display: block;
    margin-bottom: 5px;
  }
  
  input,
  textarea {
    width: 100%;
    margin-bottom: 15px;
    padding: 10px;
  }
  </style>
  