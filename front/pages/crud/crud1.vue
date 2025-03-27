<script setup>
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import communicationManager from '~/services/communicationManager';  // El archivo de servicio

// Variables reactivas
const router = useRouter();

const isAdmin = ref(false);
const selectedDate = ref(null);
const selectedTime = ref('16');  // Hora seleccionada, por defecto 16
const ocupacioMapa = ref([]);  // Mapa de ocupación
const entradas = ref({ normal: 0, vip: 0 });
const recaptacio = ref({ normal: 0, vip: 0, total: 0 });
const errorMessage = ref('');  // Variable para manejar los mensajes de error
const errorOcupacion = ref(false);  // Nueva variable reactiva

// Verifica si el usuario es admin
const checkAdmin = async () => {
  const token = localStorage.getItem('auth_token');
  if (token) {
    try {
      const user = await communicationManager.getCurrentUser();  // Obtener usuario autenticado
      isAdmin.value = user.role === 'admin';  // Verifica el rol del usuario
    } catch (error) {
      console.error('Error verificando el rol:', error);
      errorMessage.value = 'Error al verificar el rol del usuario.'; // Mostrar mensaje de error
    }
  }
};
function goBack() {
  router.go(-1); // Navega a la página anterior
};
const fetchOcupacio = async () => {
  console.log("fetchOcupacio ejecutado", selectedDate.value, selectedTime.value);  // Debugging
  if (selectedDate.value && selectedTime.value) {
    try {
      const formattedDate = new Date(selectedDate.value).toISOString().split('T')[0];  // Fecha en formato YYYY-MM-DD
      const movieId = 1; // Cambia este valor según la película seleccionada

      const data = await communicationManager.getOcupacionByDate(movieId, formattedDate, selectedTime.value);
      console.log("Datos obtenidos:", data);  // Verifica que los datos están bien

      if (data && data.butacas) {
        ocupacioMapa.value = data.butacas;  // Mapa de ocupación
        errorOcupacion.value = false;  // No hubo error, ocultamos el mensaje
      } else {
        ocupacioMapa.value = [];
        errorOcupacion.value = true;  // Error, mostramos el mensaje
      }

      if (data && data.entradasNormal !== undefined) {
        entradas.value = {
          normal: data.entradasNormal || 0,
          vip: data.entradasVIP || 0,
        };

        recaptacio.value = {
          normal: data.recaudacionNormal || 0,
          vip: data.recaudacionVIP || 0,
          total: data.recaudacionTotal || 0,
        };
      }
    } catch (error) {
      console.error('Error al obtener ocupación:', error);
      errorOcupacion.value = true;  // Si ocurre un error, mostramos el mensaje
    }
  }
};

// Función que permite seleccionar una fecha
const selectDate = (date) => {
  selectedDate.value = date;
  fetchOcupacio();  // Llamada a la función para cargar los datos correspondientes
};

// Función que permite seleccionar una hora
const selectTime = (time) => {
  selectedTime.value = time;
  fetchOcupacio();  // Llamada a la función para cargar los datos correspondientes
};

onMounted(async () => {
  await checkAdmin();  // Comprobamos si el usuario tiene privilegios de administrador
});
</script>


<template>
  <Navbar />

  <button @click="goBack" class="back-link">
      ⬅ Volver
    </button>

  <div v-if="isAdmin" class="p-6">
    <h1 class="text-2xl font-bold mb-4 text-[#2b2d42]">Consulta Administrativa: Ocupación y Recaptación</h1>

    <!-- Botones CRUD -->
    <div class="crud-buttons mb-4">
      <NuxtLink to="crud2" class="crud-button text-white bg-[#ef233c] hover:bg-[#d80032]">
        Administrar Streaming y Sessions
            </NuxtLink>
      <NuxtLink to="crud3" class="crud-button text-white bg-[#ef233c] hover:bg-[#d80032]">
        Administrar Películas
      </NuxtLink>
    </div>

    <!-- Selector de fecha -->
    <div class="mb-4">
      <label for="date" class="text-lg text-[#2b2d42]">Selecciona un día:</label>
      <input type="date" v-model="selectedDate" @change="selectDate(selectedDate)" class="input-field">
    </div>

    <!-- Selector de hora -->
    <div class="mb-4">
      <label for="time" class="text-lg text-[#2b2d42]">Selecciona una hora:</label>
      <select v-model="selectedTime" @change="selectTime(selectedTime)" class="input-field">
        <option value="16:00:00">16:00</option>
        <option value="18:00:00">18:00</option>
        <option value="20:00:00">20:00</option>
      </select>
    </div>

    <h2 class="text-xl font-bold text-[#2b2d42]">Mapa de Ocupación</h2>
    <div v-if="errorOcupacion" class="text-red-500 text-xl font-bold">
      Error al obtener la ocupación. Por favor, intente de nuevo más tarde.
    </div>

    <!-- Mostrar la tabla solo si no hay error -->
    <div v-else>
      <p class="text-[#2b2d42]">Mapa de ocupación de las butacas para la fecha seleccionada:</p>

      <!-- Informe de recaptación -->
      <div v-if="entradas && recaptacio" class="recaptacio mb-6 text-[#2b2d42]">
        <h2 class="text-xl font-bold text-[#ef233c]">Informe de Recaptación</h2>
        <ul class="list-disc pl-6">
          <li>Entradas Normal: {{ entradas.normal }}</li>
          <li>Entradas VIP: {{ entradas.vip }}</li>
          <li>Recaudación Normal: {{ recaptacio.normal }} €</li>
          <li>Recaudación VIP: {{ recaptacio.vip }} €</li>
          <li>Recaptación total: {{ recaptacio.total }} €</li>
        </ul>
      </div>

      <!-- Tabla de butacas -->
      <table class="min-w-full table-auto border-collapse">
        <thead>
          <tr class="bg-[#8d99ae]">
            <th class="px-4 py-2 text-white">Fila</th>
            <th class="px-4 py-2 text-white">Columna</th>
            <th class="px-4 py-2 text-white">Estado</th>
            <th class="px-4 py-2 text-white">VIP</th>
            <th class="px-4 py-2 text-white">Precio (€)</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="butaca in ocupacioMapa" :key="butaca.butaca_id" class="bg-[#edf2f4]">
            <td class="px-4 py-2">{{ butaca.fila }}</td>
            <td class="px-4 py-2">{{ butaca.columna }}</td>
            <td class="px-4 py-2">{{ butaca.estado }}</td>
            <td class="px-4 py-2">{{ butaca.vip ? 'Sí' : 'No' }}</td>
            <td class="px-4 py-2">{{ butaca.precio }} €</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Si el usuario no es admin, mostramos un mensaje -->
  <div v-else class="text-center p-6 text-red-500 text-xl font-bold">
    No tienes permisos para ver esta página.
  </div>
</template>
<style scoped>
.back-link {
  display: inline-block;
  padding: 10px 20px;
  background-color: #2b2d42;
  color: #8d99ae;
  border: 2px solid #8d99ae;
  border-radius: 8px;
  text-decoration: none;
  font-weight: bold;
  margin: 20px;
  transition: all 0.3s;
}

.back-link:hover {
  color: #ef233c;
  border-color: #ef233c;
}

.p-6 {
  padding: 1.5rem;
  background-color: #edf2f4;
  border-radius: 8px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  margin: 20px;
}

h1, h2 {
  color: #2b2d42;
  margin-bottom: 20px;
}

.crud-buttons {
  display: flex;
  gap: 10px;
  margin-bottom: 20px;
}

.crud-button {
  padding: 10px 20px;
  background-color: #2b2d42;
  color: #edf2f4;
  border-radius: 4px;
  text-decoration: none;
  font-weight: bold;
  transition: all 0.3s;
}

.crud-button:hover {
  background-color: #ef233c;
  transform: translateY(-2px);
}

.input-field {
  background-color: #edf2f4;
  border: 2px solid #8d99ae;
  padding: 10px;
  border-radius: 4px;
  width: 100%;
  margin-bottom: 15px;
  color: #2b2d42;
}

.input-field:focus {
  border-color: #ef233c;
  outline: none;
}

table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 20px;
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

tr:nth-child(even) {
  background-color: rgba(141, 153, 174, 0.1);
}

tr:hover {
  background-color: rgba(141, 153, 174, 0.2);
}

.recaptacio {
  background-color: #8d99ae;
  padding: 15px;
  border-radius: 8px;
  margin-bottom: 20px;
  color: #edf2f4;
}

.recaptacio h2 {
  color: #ef233c;
}

.recaptacio ul {
  list-style-type: disc;
  padding-left: 20px;
}

.recaptacio li {
  margin-bottom: 8px;
}

.text-red-500 {
  color: #d80032;
  font-weight: bold;
}

@media (max-width: 768px) {
  table {
    display: block;
    overflow-x: auto;
  }
  
  .crud-buttons {
    flex-direction: column;
  }
}
</style>