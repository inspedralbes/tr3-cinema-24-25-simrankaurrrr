<template>
  <Navbar />
  
  <div class="compra-container">
    <button @click="goBack" class="back-button">
      ⬅ Volver
    </button>
    <div class="content-wrapper">
      <div class="carrito-container">
        <h2>Carrito de Reservas</h2>

        <ul v-if="carrito.length > 0">
          <li v-for="(reserva, index) in carrito" :key="reserva.reserva_id">
            <p>🎬 Película: {{ reserva.nombre_pelicula || 'Desconocida' }}</p>
            <p>📍 Asiento: Fila {{ reserva.fila }}, Columna {{ reserva.columna }}</p>
            <p>💲 Precio: {{ reserva.precio || 'No disponible' }} €</p>

            <button @click="eliminarReserva(reserva.reserva_id, index)" class="btn-eliminar">❌ Eliminar</button>
            <button @click="mostrarFormularioPagoIndividual(reserva)" class="btn-pagar-individual">
              💳 Pagar Individualmente
            </button>

            <div v-if="reserva.pagarIndividual" class="formulario-pago">
              <h3>Ingrese sus Datos de Pago</h3>
              <input v-model="reserva.datosPago.numero_tarjeta" placeholder="Número de Tarjeta" maxlength="16" class="input-form" />
              <input v-model="reserva.datosPago.fecha_vencimiento" placeholder="MM/YY" maxlength="5" class="input-form" />
              <input v-model="reserva.datosPago.cvv" placeholder="CVV" type="password" maxlength="3" class="input-form" />

              <input v-model="reserva.datosPago.nombre" placeholder="Nombre" class="input-form" />
              <input v-model="reserva.datosPago.apellido" placeholder="Apellido" class="input-form" />
              <input v-model="reserva.datosPago.email" placeholder="Correo Electrónico" type="email" class="input-form" />

              <button @click="realizarPagoIndividual(reserva)" :disabled="procesandoPago" class="btn-pago">Realizar Pago</button>
            </div>
          </li>
        </ul>

        <p v-else>No tienes reservas en el carrito.</p>

        <p v-if="mensajeError" class="mensaje-error">{{ mensajeError }}</p>

        <div v-if="carrito.length > 0 && !mostrarFormulario">
          <button @click="mostrarFormulario = true" :disabled="procesandoPago" class="btn-pagar-todo">
            💳 Pagar Todo
          </button>
        </div>

        <div v-if="mostrarFormulario" class="formulario-pago">
          <h3>Ingrese sus Datos de Pago</h3>
          <input v-model="datosPago.numero_tarjeta" placeholder="Número de Tarjeta" maxlength="16" class="input-form" />
          <input v-model="datosPago.fecha_vencimiento" placeholder="MM/YY" maxlength="5" class="input-form" />
          <input v-model="datosPago.cvv" placeholder="CVV" type="password" maxlength="3" class="input-form" />

          <input v-model="datosPago.nombre" placeholder="Nombre" class="input-form" />
          <input v-model="datosPago.apellido" placeholder="Apellido" class="input-form" />
          <input v-model="datosPago.email" placeholder="Correo Electrónico" type="email" class="input-form" />

          <button @click="realizarPago" :disabled="procesandoPago" class="btn-pago">Realizar Pago</button>
          <p v-if="mensaje" class="mensaje">{{ mensaje }}</p>
        </div>
      </div>

      <div v-if="procesandoPago" class="popup">
        <div class="popup-content">
          <h2>⏳ Procesando pago...</h2>
          <p>Por favor espera mientras procesamos tu pago.</p>
        </div>
      </div>

      <div v-if="mensajeConfirmacion" class="popup">
        <div class="popup-content">
          <h2>✅ ¡Pago exitoso!</h2>
          <p>Se ha enviado un correo con tu justificante de la entrada.</p>
          <button @click="cerrarPopUp" class="btn-aceptar">Aceptar</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import communicationManager from '@/services/communicationManager';
import { useRoute, useRouter } from 'vue-router';

export default {
  data() {
    const router = useRouter();

    return {
      carrito: [],
      mostrarFormulario: false,
      datosPago: {
        numero_tarjeta: '',
        fecha_vencimiento: '',
        cvv: '',
        nombre: '',
        apellido: '',
        email: '',
      },
      procesandoPago: false,
      mensaje: '',
      mensajeError: '',
      mensajeConfirmacion: false,
    };
  },
  methods: {
    goBack() {
      this.$router.go(-1);
    },
    
    cargarCarrito() {
      communicationManager.getCarrito()
        .then(data => {
          this.carrito = data.map(reserva => ({
            ...reserva,
            pagarIndividual: false,
            datosPago: {
              numero_tarjeta: '',
              fecha_vencimiento: '',
              cvv: '',
              nombre: '',
              apellido: '',
              email: '',
            },
          }));
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
    
    mostrarFormularioPagoIndividual(reserva) {
      reserva.pagarIndividual = !reserva.pagarIndividual;
    },
    
    limpiarFormularioPago() {
      // Limpiar formulario de pago general
      this.datosPago = {
        numero_tarjeta: '',
        fecha_vencimiento: '',
        cvv: '',
        nombre: '',
        apellido: '',
        email: '',
      };
      
      // Limpiar formularios individuales
      this.carrito.forEach(reserva => {
        reserva.datosPago = {
          numero_tarjeta: '',
          fecha_vencimiento: '',
          cvv: '',
          nombre: '',
          apellido: '',
          email: '',
        };
        reserva.pagarIndividual = false;
      });
      
      this.mostrarFormulario = false;
    },
    
    realizarPagoIndividual(reserva) {
      if (!reserva.datosPago.numero_tarjeta || !reserva.datosPago.fecha_vencimiento || 
          !reserva.datosPago.cvv || !reserva.datosPago.nombre || 
          !reserva.datosPago.apellido || !reserva.datosPago.email) {
        this.mensaje = "⚠️ Todos los campos de pago son obligatorios.";
        return;
      }

      this.procesandoPago = true;
      this.mensaje = "⏳ Procesando pago...";

      const pagoData = {
        numero_tarjeta: reserva.datosPago.numero_tarjeta,
        fecha_vencimiento: reserva.datosPago.fecha_vencimiento,
        cvv: reserva.datosPago.cvv,
        nombre: reserva.datosPago.nombre,
        apellido: reserva.datosPago.apellido,
        email: reserva.datosPago.email,
        compra_id: [reserva.compra_id],
      };

      communicationManager.realizarPago(pagoData)
        .then(response => {
          this.mensaje = "✅ " + response.message;
          this.carrito = this.carrito.filter(item => item.reserva_id !== reserva.reserva_id);
          this.limpiarFormularioPago();
          this.mensajeConfirmacion = true;
        })
        .catch(error => {
          this.mensaje = "⚠️ Error al procesar el pago. Inténtalo nuevamente.";
        })
        .finally(() => {
          this.procesandoPago = false;
        });
    },
    
    realizarPago() {
      if (this.carrito.length === 0) {
        this.mensaje = "❌ No tienes reservas en el carrito.";
        return;
      }

      if (!this.datosPago.numero_tarjeta || !this.datosPago.fecha_vencimiento || 
          !this.datosPago.cvv || !this.datosPago.nombre || 
          !this.datosPago.apellido || !this.datosPago.email) {
        this.mensaje = "⚠️ Todos los campos de pago son obligatorios.";
        return;
      }

      this.procesandoPago = true;
      this.mensaje = "⏳ Procesando pago...";

      const compraIds = this.carrito.map(reserva => reserva.compra_id);

      const pagoData = {
        numero_tarjeta: this.datosPago.numero_tarjeta,
        fecha_vencimiento: this.datosPago.fecha_vencimiento,
        cvv: this.datosPago.cvv,
        nombre: this.datosPago.nombre,
        apellido: this.datosPago.apellido,
        email: this.datosPago.email,
        compra_id: compraIds,
      };

      communicationManager.realizarPago(pagoData)
        .then(response => {
          this.mensaje = "✅ " + response.message;
          this.carrito = [];
          this.limpiarFormularioPago();
          this.mensajeConfirmacion = true;
        })
        .catch(error => {
          this.mensaje = "⚠️ Error al procesar el pago. Inténtalo nuevamente.";
        })
        .finally(() => {
          this.procesandoPago = false;
        });
    },
    
    cerrarPopUp() {
      this.mensajeConfirmacion = false;
      this.limpiarFormularioPago();
    }
  },
  created() {
    this.cargarCarrito();
  }
};
</script>

<style scoped>
.compra-container {
  background-color: #2b2d42;
  color: #edf2f4;
  min-height: 100vh;
  padding: 20px;
}

.content-wrapper {
  display: flex;
  gap: 20px;
  align-items: flex-start;
  max-width: 1200px;
  margin: 0 auto;
}

.carrito-container {
  background-color: #edf2f4;
  color: #2b2d42;
  padding: 30px;
  border-radius: 12px;
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
  flex-grow: 1;
  max-width: 800px;
}

.carrito-container h2 {
  color: #d80032;
  margin-bottom: 20px;
  font-size: 28px;
}

.back-button {
  background-color: #2b2d42;
  color: #8d99ae;
  border: 2px solid #8d99ae;
  padding: 10px 20px;
  border-radius: 8px;
  font-weight: bold;
  cursor: pointer;
  transition: all 0.3s ease;
  margin-bottom: 20px;
}

.back-button:hover {
  color: #ef233c;
  border-color: #ef233c;
}

.carrito-container ul {
  list-style: none;
  padding: 0;
}

.carrito-container li {
  background-color: white;
  border-radius: 8px;
  padding: 20px;
  margin-bottom: 20px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  border-left: 4px solid #8d99ae;
}

.carrito-container li:hover {
  border-left-color: #ef233c;
}

.carrito-container p {
  margin: 8px 0;
  color: #2b2d42;
}

.formulario-pago {
  background-color: #8d99ae;
  padding: 20px;
  border-radius: 10px;
  margin-top: 20px;
}

.formulario-pago h3 {
  color: #2b2d42;
  margin-bottom: 15px;
}

.input-form {
  background-color: #edf2f4;
  border: 2px solid #2b2d42;
  color: #2b2d42;
  padding: 10px;
  margin: 10px 0;
  border-radius: 6px;
  width: 100%;
}

.input-form:focus {
  border-color: #ef233c;
  outline: none;
}

.btn-eliminar {
  background-color: #d80032;
  color: white;
  padding: 8px 16px;
  border: none;
  border-radius: 6px;
  margin-right: 10px;
  cursor: pointer;
  transition: background-color 0.3s;
}

.btn-eliminar:hover {
  background-color: #691e06;
}

.btn-pagar-individual,
.btn-pagar-todo,
.btn-pago,
.btn-aceptar {
  background-color: #2b2d42;
  color: #edf2f4;
  padding: 10px 20px;
  border: none;
  border-radius: 6px;
  margin: 5px;
  cursor: pointer;
  transition: all 0.3s;
}

.btn-pagar-individual:hover,
.btn-pagar-todo:hover,
.btn-pago:hover,
.btn-aceptar:hover {
  background-color: #ef233c;
  transform: translateY(-2px);
}

.popup {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(43, 45, 66, 0.8);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1000;
}

.popup-content {
  background-color: #edf2f4;
  padding: 30px;
  border-radius: 12px;
  max-width: 500px;
  text-align: center;
  color: #2b2d42;
}

.mensaje-error {
  color: #d80032;
  font-weight: bold;
}

.mensaje {
  color: #2b2d42;
  margin-top: 10px;
}

@media (max-width: 768px) {
  .content-wrapper {
    flex-direction: column;
  }
  
  .carrito-container {
    padding: 20px;
  }
}
</style>