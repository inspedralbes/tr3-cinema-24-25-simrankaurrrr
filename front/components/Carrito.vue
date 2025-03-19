<template>
  <div class="carrito-container">
    <div class="carrito-content">
      <h2>Carrito de Reservas</h2>

      <ul v-if="carrito.length > 0">
        <li v-for="(reserva, index) in carrito" :key="reserva.reserva_id">
          <p>🎬 Película: {{ reserva.nombre_pelicula }}</p>
          <p>📍 Asiento: Fila {{ reserva.fila }}, Columna {{ reserva.columna }}</p>
          <p>💲 Precio: ${{ reserva.precio }}</p>

          <!-- Botón de Eliminar -->
          <button @click="eliminarReserva(reserva.reserva_id, index)">❌ Eliminar</button>

          <!-- Botón de Añadir -->
          <button @click="agregarReserva(reserva)">➕ Añadir</button>
        </li>
      </ul>

      <p v-else>No tienes reservas en el carrito.</p>

      <!-- Mensaje de error si se superan las 10 entradas -->
      <p v-if="mensajeError" style="color: red;">{{ mensajeError }}</p>

      <!-- Botón para ir a la página de compra -->
      <div v-if="carrito.length > 0">
        <NuxtLink to="/compra" class="login-link">
            Ir a compra
          </NuxtLink>
      </div>
    </div>
  </div>
</template>

<script>
import communicationManager from '@/services/communicationManager';

export default {
  data() {
    return {
      carrito: [],
      mensajeError: '',
    };
  },
  methods: {
    cargarCarrito() {
      communicationManager.getCarrito()
        .then(data => {
          console.log("Carrito recibido de la API:", data);
          this.carrito = data;
        })
        .catch(error => {
          console.error("Error cargando el carrito:", error);
        });
    },

    eliminarReserva(reservaId, index) {
      communicationManager.eliminarReserva(reservaId)
        .then(() => {
          this.carrito.splice(index, 1);
          this.mensajeError = '';
        })
        .catch(error => {
          console.error('Error eliminando la reserva:', error);
        });
    },

    agregarReserva(reserva) {
      const reservaExistente = this.carrito.find(item => item.reserva_id === reserva.reserva_id);
      if (this.carrito.length >= 10) {
        this.mensajeError = '⚠️ No puedes añadir más de 10 entradas al carrito.';
        return;
      }

      if (!reservaExistente) {
        this.carrito.push(reserva);
        this.mensajeError = '';
      }
    },
  },
  mounted() {
    this.cargarCarrito();
  },
};
</script>

<style scoped>
.carrito-container {
  position: fixed;
  top: 0;
  right: 0;
  width: 350px;
  height: 100%;
  background-color: white;
  box-shadow: -4px 0 6px rgba(0, 0, 0, 0.1);
  z-index: 1000;
  transform: translateX(100%);
  transition: transform 0.3s ease-in-out;
}

.carrito-container.open {
  transform: translateX(0);
}

.carrito-content {
  padding: 20px;
  overflow-y: auto;
}

button {
  margin-top: 10px;
}

button:disabled {
  opacity: 0.5;
}
</style>
