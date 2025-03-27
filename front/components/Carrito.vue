<template>
  <div>
    <!-- Carrito desplegable -->
    <div class="carrito-overlay" v-if="isCarritoVisible" @click="cerrarCarrito">
      <div class="carrito-container" @click.stop>
        <h2>Carrito de Reservas</h2>

        <ul v-if="carrito.length > 0">
          <li v-for="(reserva, index) in carrito" :key="reserva.reserva_id">
            <p>🎬 Película: {{ reserva.nombre_pelicula }}</p>
            <p>📍 Asiento: Fila {{ reserva.fila }}, Columna {{ reserva.columna }}</p>
            <p>💲 Precio: {{ reserva.precio }}  €</p>

            <!-- Botón de Eliminar -->
            <button @click="eliminarReserva(reserva.reserva_id, index)" class="btn-eliminar">❌ Eliminar</button>
          </li>
        </ul>

        <p v-else>No tienes reservas en el carrito.</p>

        <!-- Mensaje de error si se superan las 10 entradas -->
        <p v-if="mensajeError" class="mensaje-error">{{ mensajeError }}</p>

        <!-- Botón para ir a la página de compra -->
        <div v-if="carrito.length > 0">
          <NuxtLink to="/compra" class="btn-compra">
            Ir a compra
          </NuxtLink>
          <br><br>
        </div>
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
      isCarritoVisible: false,
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

    abrirCarrito() {
      this.isCarritoVisible = true;
    },

    cerrarCarrito() {
      this.isCarritoVisible = false;
    },
  },
  mounted() {
    this.cargarCarrito();
    this.abrirCarrito();
  },
};
</script>

<style scoped>
.carrito-overlay {
  position: fixed;
  top: 0;
  right: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(43, 45, 66, 0.8);
  display: flex;
  justify-content: flex-end;
  z-index: 1000;
}

.carrito-container {
  width: 350px;
  height: 100%;
  background-color: #edf2f4;
  box-shadow: -4px 0 6px rgba(0, 0, 0, 0.1);
  padding: 20px;
  overflow-y: auto;
  transform: translateX(0); /* Mostrar siempre visible desde la derecha */
}

h2 {
  font-size: 24px;
  color: #ef233c;
  margin-bottom: 20px;
  border-bottom: 2px solid #8d99ae;
  padding-bottom: 10px;
}

ul {
  list-style-type: none;
  padding: 0;
}

li {
  background-color: #fff;
  border-radius: 8px;
  padding: 15px;
  margin-bottom: 15px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  border-left: 4px solid #8d99ae;
  transition: all 0.3s ease;
}

li:hover {
  border-left-color: #ef233c;
  transform: translateY(-2px);
}

p {
  margin: 5px 0;
  color: #2b2d42;
}

.btn-eliminar {
  background-color: #d80032;
  color: #edf2f4;
  border: none;
  padding: 8px 16px;
  border-radius: 4px;
  cursor: pointer;
  font-weight: bold;
  transition: all 0.3s;
  margin-top: 10px;
}

.btn-eliminar:hover {
  background-color: #691e06;
  transform: translateY(-2px);
}

.btn-compra {
  display: block;
  background-color: #2b2d42;
  color: #edf2f4;
  text-align: center;
  padding: 12px;
  border-radius: 4px;
  text-decoration: none;
  font-weight: bold;
  margin-top: 20px;
  transition: all 0.3s;
}

.btn-compra:hover {
  background-color: #ef233c;
  transform: translateY(-2px);
}

.mensaje-error {
  color: #d80032;
  font-weight: bold;
  text-align: center;
  margin: 15px 0;
}

@media (max-width: 480px) {
  .carrito-container {
    width: 100%;
  }
  
  li {
    padding: 10px;
  }
  
  .btn-eliminar, .btn-compra {
    padding: 8px 12px;
  }
}
</style>