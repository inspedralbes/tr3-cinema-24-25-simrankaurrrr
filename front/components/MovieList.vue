<template>
  <div>
    <!-- Carrusel de películas -->
    <div class="carousel" :style="carouselStyle">
      <!-- Texto "Añadido recientemente" -->

      <!-- Lista de películas -->
      <div class="list">
        <div v-for="(movie, index) in movies" :key="movie.id" class="item" :class="{ active: index === activeIndex }">
          <div class="content">


            <div class="movie-details">
              <div class="left-side">
                <h1>{{ movie.title }}</h1>
                <div class="details">
                  <p><strong>Duración:</strong> {{ movie.duracion }} </p>
                  <p><strong>Género:</strong> {{ movie.genero }}</p>
                  <p><strong>Año:</strong> {{ movie.año }}</p>
                  <p><strong>Director:</strong> {{ movie.director }}</p>
                </div>
                <h4><strong>Sinopsis:</strong> {{ movie.sinopsis }}</h4>
                <div class="buttons">
                  <button class="download-btn" @click="downloadMoviePDF">Descargar Información</button>
                                                    <button class="buy-tickets-btn" @click="goToMoviePage(activeMovie?.id)">Comprar Entradas</button>
                  <button class="trailer-btn" @click="playTrailer(movie.trailer_url)">Ver Trailer</button>
                </div>
              </div>

              <!-- Trailer a la derecha -->
              <div v-if="movie.trailer_url" class="right-side">
                <div class="vid-box">
                  <video :src="movie.trailer_url" controls class="vid-trailer"></video>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>


      <!-- Miniaturas de películas -->
      <div class="thumbnail-container">
        <div class="thumbnail" ref="thumbnailContainer">
          <div v-for="(movie, index) in movies" :key="movie.id" class="item" :class="{ active: index === activeIndex }"
            @click="setActiveIndex(index)" :ref="'thumbnail_' + index">
            <img :src="movie.poster_url" alt="Poster" class="w-full h-full object-cover" />
            <div class="thum-content">
              <div class="title">{{ movie.title }}</div>
              <div class="description">{{ movie.genre }}</div>
            </div>
          </div>
        </div>
      </div>

      <!-- Flechas de navegación -->
      <div class="arrows">
        <button id="prev" @click="prevSlide">‹</button>
        <button id="next" @click="nextSlide">›</button>
      </div>

      <!-- Barra de tiempo -->
      <div class="time"></div>
    </div>
  </div>
</template>

<script setup lang="ts">import { ref, onMounted, computed, nextTick } from 'vue';
import { useRouter } from 'vue-router';
import { jsPDF } from "jspdf";  // Añade esto al inicio con los otros imports

const config = useRuntimeConfig();
const movies = ref<any[]>([]);
const activeIndex = ref(0);
const router = useRouter();
const thumbnailContainer = ref<HTMLElement | null>(null);

async function fetchMovies() {
  try {
    movies.value = await $fetch(`${config.public.apiBase}/movies`);
    console.log('Películas cargadas:', movies.value);
  } catch (error) {
    console.error('Error fetching movies:', error);
  }
}

function downloadMoviePDF() {
  if (!activeMovie.value) {
    console.error("No hay película activa seleccionada");
    return;
  }

  try {
    const movie = activeMovie.value;
    const doc = new jsPDF();
    
    // Configuración del documento
    const margin = 15;
    let yPosition = margin;
    const lineHeight = 7;
    const pageWidth = doc.internal.pageSize.getWidth();
    const contentWidth = pageWidth - 2 * margin;
    
    // Colores (formato RGB individual)
    const primaryColorRed = 239;
    const primaryColorGreen = 35;
    const primaryColorBlue = 60;
    
    const secondaryColorRed = 43;
    const secondaryColorGreen = 45;
    const secondaryColorBlue = 66;
    
    // --- Encabezado con estilo ---
    doc.setFillColor(primaryColorRed, primaryColorGreen, primaryColorBlue);
    doc.rect(0, 0, pageWidth, 20, 'F');
    doc.setFontSize(16);
    doc.setTextColor(255, 255, 255);
    doc.text('Información de Película', pageWidth / 2, 15, { align: 'center' });
    
    yPosition = 30;
    
    // --- Título de la película ---
    doc.setFontSize(20);
    doc.setTextColor(primaryColorRed, primaryColorGreen, primaryColorBlue);
    doc.setFont("helvetica", "bold");
    const titleLines = doc.splitTextToSize(movie.title, contentWidth);
    doc.text(titleLines, margin, yPosition);
    yPosition += (titleLines.length * lineHeight) + 5;
    
    // --- Línea divisoria decorativa ---
    doc.setDrawColor(primaryColorRed, primaryColorGreen, primaryColorBlue);
    doc.setLineWidth(0.5);
    doc.line(margin, yPosition, pageWidth - margin, yPosition);
    yPosition += 10;
    
    // --- Función para agregar detalles con estilo ---
    const addDetail = (label: string, value: string, isImportant = false) => {
      // Label
      doc.setFontSize(isImportant ? 14 : 12);
      doc.setTextColor(secondaryColorRed, secondaryColorGreen, secondaryColorBlue);
      doc.setFont("helvetica", "bold");
      const labelWidth = doc.getTextWidth(`${label}: `);
      doc.text(`${label}:`, margin, yPosition);
      
      // Value
      doc.setFont("helvetica", isImportant ? "bold" : "normal");
      doc.setTextColor(0, 0, 0);
      const valueLines = doc.splitTextToSize(value, contentWidth - labelWidth - 5);
      
      let tempY = yPosition;
      valueLines.forEach((line: string) => {
        doc.text(line, margin + labelWidth + 5, tempY);
        tempY += lineHeight;
      });
      
      yPosition = tempY + (isImportant ? lineHeight : lineHeight / 2);
    };
    
    // --- Detalles principales ---
    doc.setFontSize(14);
    doc.setTextColor(secondaryColorRed, secondaryColorGreen, secondaryColorBlue);
    doc.text('Detalles Técnicos', margin, yPosition);
    yPosition += lineHeight + 5;
    
    addDetail("Duración", `${movie.duracion}`);
    addDetail("Género", movie.genero);
    addDetail("Año", movie.año);
    addDetail("Director", movie.director);
    
    // --- Sinopsis con estilo especial ---
    yPosition += 5;
    doc.setFontSize(16);
    doc.setTextColor(primaryColorRed, primaryColorGreen, primaryColorBlue);
    doc.text('Sinopsis', margin, yPosition);
    yPosition += lineHeight + 2;
    
    doc.setFontSize(12);
    doc.setTextColor(0, 0, 0);
    doc.setFont("helvetica", "normal");
    const sinopsisLines = doc.splitTextToSize(movie.sinopsis, contentWidth);
    sinopsisLines.forEach((line: string) => {
      doc.text(line, margin, yPosition);
      yPosition += lineHeight;
    });
    
    // --- Sección adicional ---
    if (movie.actores || movie.idioma || movie.formato) {
      yPosition += 10;
      doc.setFontSize(14);
      doc.setTextColor(secondaryColorRed, secondaryColorGreen, secondaryColorBlue);
      doc.text('Información Adicional', margin, yPosition);
      yPosition += lineHeight + 5;
      
      if (movie.actores) addDetail("Reparto", movie.actores);
      if (movie.idioma) addDetail("Idioma", movie.idioma);
      if (movie.formato) addDetail("Formato", movie.formato);
    }
    
    // --- Pie de página ---
    doc.setFontSize(10);
    doc.setTextColor(150, 150, 150);
    doc.text(`Generado desde MDVD - ${new Date().toLocaleDateString()}`, 
             pageWidth / 2, doc.internal.pageSize.getHeight() - 10, 
             { align: 'center' });
    
    // --- Guardar el PDF ---
    const filename = `${movie.title.replace(/[^\w\s]/gi, '').replace(/\s+/g, '_')}_info.pdf`;
    doc.save(filename);
    
  } catch (error) {
    console.error("Error al generar el PDF:", error);
  }

}
function setActiveIndex(index: number) {
  activeIndex.value = index;
  scrollToActiveThumbnail();
}

function nextSlide() {
  activeIndex.value = (activeIndex.value + 1) % movies.value.length;
  scrollToActiveThumbnail();
}

function prevSlide() {
  activeIndex.value = (activeIndex.value - 1 + movies.value.length) % movies.value.length;
  scrollToActiveThumbnail();
}


function scrollToActiveThumbnail() {
  nextTick(() => {
    if (!thumbnailContainer.value) {
      console.warn('Contenedor de thumbnails no encontrado');
      return;
    }
    
    const container = thumbnailContainer.value;
    const activeThumbnail = container.querySelector('.item.active');
    
    if (!activeThumbnail) {
      console.warn('Thumbnail activo no encontrado');
      return;
    }
    
    try {
      const containerWidth = container.offsetWidth;
      const thumbnailWidth = activeThumbnail.clientWidth;
      const thumbnailPosition = activeThumbnail.getBoundingClientRect();
      const containerPosition = container.getBoundingClientRect();
      
      // Calcula la posición relativa dentro del contenedor
      const relativePosition = thumbnailPosition.left - containerPosition.left;
      const scrollLeft = relativePosition - (containerWidth / 2) + (thumbnailWidth / 2);
      
      container.scrollTo({
        left: scrollLeft,
        behavior: 'smooth'
      });
    } catch (error) {
      console.error('Error al calcular scroll position:', error);
    }
  });
}
function playTrailer(trailerUrl: string) {
  const trailer = document.querySelector('.vid-trailer') as HTMLVideoElement;
  trailer.src = trailerUrl;
  trailer.play();
}

const activeMovie = computed(() => {
  const movie = movies.value[activeIndex.value];
  return movie ? movie : null;
});

function goToMoviePage(movieId: number | null) {
  if (movieId === null) {
    console.error("ID de la película no válido");
    return;
  }

  console.log('ID de la película enviada:', movieId); // Verifica que el ID es el correcto
  router.push(`/movies/${movieId}`);
}

const carouselStyle = computed(() => {
  const posterUrl = activeMovie.value?.poster_url;
  return posterUrl
    ? { backgroundImage: `url(${posterUrl})`, backgroundSize: 'cover', backgroundPosition: 'center' }
    : { backgroundColor: '#2b2d42' };
});

onMounted(fetchMovies);
</script>






<style scoped>
.newly-added {
  position: absolute;
  top: 10px;
  left: 50%;
  transform: translateX(-50%);
  padding: 8px 16px;
  background-color: rgba(0, 0, 0, 0.7);
  color: #fff;
  font-weight: bold;
  font-size: 14px;
  border-radius: 5px;
}
.buy-tickets-btn {
  width: 180px;
  padding: 12px 0;
  font-weight: bold;
  border: none;
  border-radius: 20px;
  transition: 0.3s;
  cursor: pointer;
  background-color: #ef233c;
  color: #edf2f4;
  box-shadow: 5px 5px 15px rgba(0, 0, 0, 0.753);
  font-size: 16px;
}

.buy-tickets-btn:hover {
  background-color: #d80032;
  opacity: 0.8;
  box-shadow: 5px 5px 25px rgba(0, 0, 0, 0.753);
}
.buy-tickets-btn:active {
  transform: scale(0.98);
}

.carousel {
  width: 100%;
  height: 100vh;
  margin-top: 0;
  position: relative;
  overflow: hidden;
  background-color: #2b2d42;
  background-size: cover;
  background-position: center;
  transition: background-image 0.5s ease-in-out;
  padding-top: 70px;
}

.carousel .list .item {
  width: 100%;
  height: 100%;
  position: absolute;
  inset: 0;
  opacity: 0;
  transition: opacity 0.5s ease-in-out;
}

.carousel .list .item.active {
  opacity: 1;
}

.carousel .list .item img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

/* Contenedor del contenido con overlay para mejorar legibilidad */
.carousel .list .item .content {
  position: absolute;
  top: 5%;
  width: 80%;
  max-width: 1140px;
  left: 50%;
  transform: translateX(-50%);
  padding: 20px 30px;
  box-sizing: border-box;
  background-color: rgba(0, 0, 0, 0.5);
  border-radius: 10px;
  color: #edf2f4;
  text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.9);
}

.content h1,
.content h4,
.details p {
  color: #edf2f4;
  text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.9);
}

.details {
  display: flex;
  margin-bottom: 20px;
  flex-wrap: wrap;
  justify-content: flex-start;
}

.details p {
  border-right: 2px solid #edf2f4;
  font-weight: bold;
  font-size: 18px;
  color: #8d99ae;
  padding: 0 10px;
}

.details p:last-child {
  border-right: none;
}

.content h4 {
  max-width: 600px;
  font-size: 17px;
  line-height: 25px;
  margin: 20px 0;
}

.buttons {
  display: flex;
  gap: 15px;
}

.trailer-btn {
  width: 150px;
  padding: 12px 0;
  font-weight: bold;
  border: none;
  border-radius: 20px;
  transition: 0.3s;
  cursor: pointer;
  background-color: #8d99ae;
  color: #2b2d42;
  box-shadow: 5px 5px 15px rgba(0, 0, 0, 0.753);
}


.trailer-btn:hover {
  background-color: #6a7485;
  color: #edf2f4;
  box-shadow: 5px 5px 25px rgba(0, 0, 0, 0.753);
}

.download-btn:active,
.buy-tickets-btn:active,
.trailer-btn:active {
  transform: scale(0.98);
}

.movie-details {
  display: flex;
  justify-content: space-between;
  gap: 30px;
}

.left-side {
  width: 60%;
}

.right-side {
  width: 35%;
  display: flex;
  justify-content: center;
  align-items: center;
}

.vid-box {
  width: 100%;
  height: 250px;
  min-height: 200px;
  max-height: 300px;
  border-radius: 20px;
  margin-top: 20px;
  overflow: hidden;
  box-shadow: 0 0 20px rgba(61, 61, 61, 0.733);
}

.vid-trailer {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

/* ESTILOS PARA THUMBNAILS */
.thumbnail-container {
  margin-top: 200px;
  position: absolute;
  bottom: 180px;
  left: 0;
  right: 0;
  bottom: 120px; /* Cambiado de 180px a 120px */

  overflow: hidden;
  padding-left: 38%;
}

.thumbnail {
  position: relative;
  z-index: 100;
  display: flex;
  gap: 15px;
  background-color: rgba(59, 59, 59, 0.4);
  backdrop-filter: blur(10px);
  border-top-left-radius: 50px;
  border-bottom-left-radius: 50px;
  box-shadow: 0 0 25px #282828;
  padding: 10px 20px;
  overflow-x: auto;
  scroll-behavior: smooth;
  -webkit-overflow-scrolling: touch;
  scrollbar-width: none; /* Para Firefox */
}

.thumbnail::-webkit-scrollbar {
  display: none; /* Para Chrome, Safari y Opera */
}

.thumbnail .item {
  width: 150px;
  height: 220px;
  flex-shrink: 0;
  position: relative;
  cursor: pointer;
  transition: all 0.3s ease;
  border: 3px solid transparent;
  border-radius: 50px;
  overflow: hidden;
}

/* ESTILOS PARA THUMBNAIL ACTIVO */
.thumbnail .item.active {
  border: 3px solid #ef233c;
  transform: scale(1.05);
  box-shadow: 0 0 20px rgba(239, 35, 60, 0.7);
  z-index: 10;
}

.thumbnail .item:hover {
  transform: scale(1.03);
}

.thumbnail .item img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.3s ease;
}

.thumbnail .item.active img {
  transform: scale(1.02);
}

.thumbnail .item .thum-content {
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  padding: 15px;
  background: linear-gradient(to top, rgba(0, 0, 0, 0.8), transparent);
  color: #edf2f4;
}

.thumbnail .item .thum-content .title {
  font-weight: 600;
  font-size: 14px;
  margin-bottom: 3px;
}

.thumbnail .item .thum-content .description {
  font-weight: 300;
  font-size: 12px;
}

.arrows {
  position: absolute;
  right: 52%;
  z-index: 100;
  width: 300px;
  max-width: 30%;
  display: flex;
  gap: 10px;
  bottom: 190px; /* Cambiado de 250px a 190px */

  align-items: center;
}

.arrows button {
  width: 50px;
  height: 50px;
  border-radius: 50%;
  background-color: rgba(238, 238, 238, 0.87);
  border: none;
  color: #2b2d42;
  font-size: 25px;
  font-weight: bold;
  transition: 0.5s;
  cursor: pointer;
}

.arrows button:hover {
  background-color: #fff;
  color: #d80032;
  box-shadow: 0 0 20px rgba(0, 0, 0, 0.587);
}

.time {
  position: absolute;
  z-index: 1000;
  width: 0%;
  height: 5px;
  background-color: #ef233c;
  left: 0;
  top: 0;
}
.download-btn {
  width: 150px;
  padding: 12px 0;
  font-weight: bold;
  border: none;
  border-radius: 20px;
  transition: 0.3s;
  cursor: pointer;
  background-color: #2b2d42;
  color: #edf2f4;
  box-shadow: 5px 5px 15px rgba(0, 0, 0, 0.753);
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 5px;
}


.download-btn:hover {
  background-color: #1a1b2a;
  opacity: 0.9;
  box-shadow: 5px 5px 25px rgba(0, 0, 0, 0.753);
}

.download-btn:active {
  transform: scale(0.98);
}
/* RESPONSIVE PARA MÓVILES */
@media (max-width: 768px) {
  .carousel {
    height: auto;
    min-height: 100vh;
    padding-top: 60px;
    padding-bottom: 200px;
  }

  .carousel .list .item .content {
    width: 90%;
    padding: 15px;
    top: 2%;
    position: relative;
    margin-bottom: 20px;
  }

  .movie-details {
    flex-direction: column;
    gap: 20px;
  }

  .left-side,
  .right-side {
    width: 100% !important;
  }

  .right-side {
    order: 1;
    margin-top: 20px;
    display: flex;
    justify-content: center;
  }

  .vid-box {
    height: 180px;
    border-radius: 10px;
    margin: 0 auto;
  }

  .content h1 {
    font-size: 1.5rem;
  }

  .details {
    display: grid;
    grid-template-columns: 1fr;
    gap: 8px;
    margin-bottom: 15px;
  }

  .details p {
    border-right: none !important;
    padding: 5px 0 !important;
    font-size: 14px;
    margin: 0;
  }

  .content h4 {
    font-size: 14px;
    line-height: 1.4;
    margin: 15px 0;
  }

  .buttons {
    flex-direction: column;
    gap: 10px;
    margin-top: 20px;
  }

  .download-btn,
  .trailer-btn,
  .buy-tickets-btn {
    width: 100% !important;
    padding: 12px !important;
    font-size: 14px;
  }

  .thumbnail-container {
    position: fixed !important;
    bottom: 10px !important;
    left: 10px !important;
    right: 10px !important;
    padding-left: 0 !important;
  }

  .thumbnail {
    position: relative !important;
    bottom: auto !important;
    left: auto !important;
    right: auto !important;
    transform: none !important;
    border-radius: 10px !important;
    gap: 10px;
    padding: 10px;
    overflow-x: auto;
    max-width: calc(100% - 20px);
    background-color: rgba(59, 59, 59, 0.7);
    backdrop-filter: blur(5px);
    z-index: 1000;
  }

  .thumbnail .item {
    width: 100px !important;
    height: 150px !important;
    border-radius: 10px !important;
  }

  .thumbnail .item.active {
    border: 2px solid #ef233c;
    transform: scale(1.05);
    box-shadow: 0 0 15px rgba(239, 35, 60, 0.7);
  }

  .thumbnail .item .thum-content {
    padding: 8px;
  }

  .thumbnail .item .thum-content .title {
    font-size: 12px;
  }

  .thumbnail .item .thum-content .description {
    font-size: 10px;
  }

  .arrows {
    bottom: 180px !important;
    right: auto !important;
    left: 50% !important;
    transform: translateX(-50%) !important;
    width: auto !important;
    z-index: 1001;
  }

  .arrows button {
    width: 40px !important;
    height: 40px !important;
    font-size: 20px !important;
  }
}

@media (max-width: 480px) {
  .thumbnail .item {
    width: 80px !important;
    height: 120px !important;
  }

  .thumbnail .item .thum-content {
    left: 5px !important;
    padding: 5px;
  }

  .arrows {
    bottom: 160px !important;
  }

  .vid-box {
    height: 160px;
  }
}
</style>