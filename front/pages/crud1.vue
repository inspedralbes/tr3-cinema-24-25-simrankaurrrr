<script setup>
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import communicationManager from '~/services/communicationManager';

const router = useRouter();
const isAdmin = ref(false);
const selectedDate = ref(null);
const selectedTime = ref('16');
const ocupacioMapa = ref([]);
const entradas = ref({ normal: 0, vip: 0 });
const recaptacio = ref({ normal: 0, vip: 0, total: 0 });
const errorMessage = ref('');
const errorOcupacion = ref(false);

const checkAdmin = async () => {
  const token = localStorage.getItem('auth_token');
  if (token) {
    try {
      const user = await communicationManager.getCurrentUser();
      isAdmin.value = user.role === 'admin';
    } catch (error) {
      console.error('Error verificando el rol:', error);
      errorMessage.value = 'Error al verificar el rol del usuario.';
    }
  }
};

function goBack() {
  router.go(-1);
}

const fetchOcupacio = async () => {
  console.log("fetchOcupacio ejecutado", selectedDate.value, selectedTime.value);
  if (selectedDate.value && selectedTime.value) {
    try {
      const formattedDate = new Date(selectedDate.value).toISOString().split('T')[0];
      const movieId = 1;
      const data = await communicationManager.getOcupacionByDate(movieId, formattedDate, selectedTime.value);
      console.log("Datos obtenidos:", data);

      if (data && data.butacas) {
        ocupacioMapa.value = data.butacas;
        errorOcupacion.value = false;
      } else {
        ocupacioMapa.value = [];
        errorOcupacion.value = true;
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
      errorOcupacion.value = true;
    }
  }
};

const selectDate = (date) => {
  selectedDate.value = date;
  fetchOcupacio();
};

const selectTime = (time) => {
  selectedTime.value = time;
  fetchOcupacio();
};

onMounted(async () => {
  await checkAdmin();
});
</script>



<template>
  <Navbar />

  <button @click="goBack" class="back-link">
    ⬅ Tornar
  </button>
  <NuxtLink to="crud2" class="crud-link text-white bg-[#ef233c] hover:bg-[#d80032]">
    Administrar Streaming i Sessions
  </NuxtLink>
  <NuxtLink to="crud3" class="crud-link text-white bg-[#ef233c] hover:bg-[#d80032]">
    Administrar Pel·lícules
  </NuxtLink>
  <div v-if="isAdmin" class="p-6">
    <h1 class="text-2xl font-bold mb-4 text-[#2b2d42]">Consulta Administrativa: Ocupació i Recaptació</h1>

    <!-- Selector de data -->
    <div class="mb-4">
      <label for="date" class="text-lg text-[#2b2d42]">Selecciona un dia:</label>
      <input type="date" v-model="selectedDate" @change="selectDate(selectedDate)" class="input-field">
    </div>

    <!-- Selector d'hora -->
    <div class="mb-4">
      <label for="time" class="text-lg text-[#2b2d42]">Selecciona una hora:</label>
      <select v-model="selectedTime" @change="selectTime(selectedTime)" class="input-field">
        <option value="16:00:00">16:00</option>
        <option value="18:00:00">18:00</option>
        <option value="20:00:00">20:00</option>
      </select>
    </div>

    <h2 class="text-xl font-bold text-[#2b2d42]">Mapa d'Ocupació</h2>
    <div v-if="errorOcupacion" class="text-red-500 text-xl font-bold">
      Error en obtenir l'ocupació. Si us plau, intenta-ho de nou més tard.
    </div>

    <!-- Mostrar la taula només si no hi ha error -->
    <div v-else>
      <p class="text-[#2b2d42]">Mapa d'ocupació de les butaques per a la data seleccionada:</p>

      <!-- Informe de recaptació -->
      <div v-if="entradas && recaptacio" class="recaptacio mb-6 text-[#2b2d42]">
        <h2 class="text-xl font-bold text-[#ef233c]">Informe de Recaptació</h2>
        <ul class="list-disc pl-6">
          <li>Entrades Normal: {{ entradas.normal }}</li>
          <li>Entrades VIP: {{ entradas.vip }}</li>
          <li>Recaudació Normal: {{ recaptacio.normal }} €</li>
          <li>Recaudació VIP: {{ recaptacio.vip }} €</li>
          <li>Recaptació total: {{ recaptacio.total }} €</li>
        </ul>
      </div>

      <!-- Taula de butaques -->
      <table class="min-w-full table-auto border-collapse">
        <thead>
          <tr class="bg-[#8d99ae]">
            <th class="px-4 py-2 text-white">Fila</th>
            <th class="px-4 py-2 text-white">Columna</th>
            <th class="px-4 py-2 text-white">Estat</th>
            <th class="px-4 py-2 text-white">VIP</th>
            <th class="px-4 py-2 text-white">Preu (€)</th>
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

  <div v-else class="text-center p-6 text-red-500 text-xl font-bold">
    No tens permisos per veure aquesta pàgina.
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

h1,
h2 {
  color: #2b2d42;
  margin-bottom: 20px;
}


.crud-link {
  display: inline-block;
  padding: 10px 20px;
  background-color: #2b2d42;
  color: #edf2f4;
  border-radius: 8px;
  text-decoration: none;
  font-weight: bold;
  margin: 10px;
  transition: all 0.3s;
}

.crud-link:hover {
  background-color: #ef233c;
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

th,
td {
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