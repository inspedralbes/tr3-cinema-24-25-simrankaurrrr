<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import communicationManager from '~/services/communicationManager';  // El archivo de servicio

// Variables reactivas
const router = useRouter();
const isAdmin = ref(false);
const selectedDate = ref(null);
const selectedTime = ref('null');  // Hora seleccionada
const ocupacioMapa = ref([]);  // Mapa de ocupación
const entradas = ref({ normal: 0, vip: 0 });
const recaptacio = ref({ normal: 0, vip: 0, total: 0 });

// Verifica si el usuario es admin
const checkAdmin = async () => {
  const token = localStorage.getItem('auth_token');
  if (token) {
    try {
      const user = await communicationManager.getCurrentUser();  // Obtener usuario autenticado
      isAdmin.value = user.role === 'admin';  // Verifica el rol del usuario
    } catch (error) {
      console.error('Error verificando el rol:', error);
    }
  }
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
      } else {
        ocupacioMapa.value = [];
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
    }
  }
};



// Función que permite seleccionar una fecha
const selectDate = (date) => {

  selectedDate.value = date;
  fetchOcupacio();  // Llamada a la función para cargar los datos correspondientes
};

// Función que permite seleccionar una hora (puedes modificarla para ofrecer un selector de hora)
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

  <NuxtLink to="/" class="text-blue-500 underline hover:text-blue-700 mb-6 inline-block">
      ⬅ Volver
    </NuxtLink>
  <div v-if="isAdmin" class="p-6">
    <h1 class="text-2xl font-bold mb-4">Consulta Administrativa: Ocupación y Recaptación</h1>
    
    <NuxtLink to="crud2" class="login-link">
            CRUD 2
          </NuxtLink>
          <NuxtLink to="crud3" class="login-link">
            CRUD 3
          </NuxtLink>
    <!-- Selector de fecha -->
    <div class="mb-4">
      <label for="date" class="text-lg">Selecciona un día:</label>
      <input type="date" v-model="selectedDate" @change="selectDate(selectedDate)" class="border px-4 py-2 rounded">
    </div>

    <!-- Selector de hora -->
    <div class="mb-4">
      <label for="time" class="text-lg">Selecciona una hora:</label>
      <input type="time" v-model="selectedTime" @change="selectTime(selectedTime)" class="border px-4 py-2 rounded">
    </div>

    <h2 class="text-xl font-bold">Mapa de Ocupación</h2>
    <div class="ocupacio-mapa">
      <p>Mapa de ocupación de las butacas para la fecha seleccionada:</p>
      <div v-if="entradas && recaptacio" class="mb-6">
        <h2 class="text-xl font-bold">Informe de Recaptación</h2>
        <li>Normal: {{ entradas.normal }}</li> <!-- Cambié de entradas.entradasnormal a entradas.normal -->
        <li>VIP: {{ entradas.vip }}</li> <!-- Cambié de entradasvip a entradas.vip -->
        <li>Normal: {{ recaptacio.normal }} €</li> <!-- Cambié de recaptacionormal a recaptacio.normal -->
        <li>VIP: {{ recaptacio.vip }} €</li> <!-- Cambié de recaptaciovip a recaptacio.vip -->
        <li>Recaptación total: {{ recaptacio.total }} €</li> <!-- Cambié de recaptaciototal a recaptacio.total -->
      </div>

      <!-- Tabla de butacas -->
      <table class="min-w-full table-auto border-collapse">
        <thead>
          <tr>
            <th class="px-4 py-2">Fila</th>
            <th class="px-4 py-2">Columna</th>
            <th class="px-4 py-2">Estado</th>
            <th class="px-4 py-2">VIP</th>
            <th class="px-4 py-2">Precio (€)</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="butaca in ocupacioMapa" :key="butaca.butaca_id">
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
.ocupacio-mapa {
  border: 1px solid #ddd;
  padding: 20px;
  margin-top: 10px;
  background-color: #f9f9f9;
}

button {
  cursor: pointer;
}

button:hover {
  opacity: 0.8;
}

table {
  width: 100%;
  border-collapse: collapse;
}

th,
td {
  border: 1px solid #ddd;
  text-align: left;
  padding: 8px;
}

th {
  background-color: #f2f2f2;
}

td {
  background-color: #fafafa;
}

tr:nth-child(even) {
  background-color: #f9f9f9;
}
</style>
