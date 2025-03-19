<template>
  <div class="compra-container">
    <Navbar />
    <NuxtLink to="/" class="text-blue-500 underline hover:text-blue-700 mb-6 inline-block">
      ⬅ Volver
    </NuxtLink>

    <div class="carrito-container">
      <h2>Carrito de Reservas</h2>

      <ul v-if="carrito.length > 0">
        <li v-for="(reserva, index) in carrito" :key="reserva.reserva_id">
          <p>🎬 Película: {{ reserva.nombre_pelicula || 'Desconocida' }}</p>
          <p>📍 Asiento: Fila {{ reserva.fila }}, Columna {{ reserva.columna }}</p>
          <p>💲 Precio: ${{ reserva.precio || 'No disponible' }}</p>

          <!-- Botones de interacción -->
          <button @click="eliminarReserva(reserva.reserva_id, index)">❌ Eliminar</button>
          <button @click="agregarReserva(reserva)">➕ Añadir</button>
          <button @click="mostrarFormularioPagoIndividual(reserva)">
            💳 Pagar Individualmente
          </button>

          <!-- Formulario de pago individual -->
          <div v-if="reserva.pagarIndividual">
            <h3>Ingrese sus Datos de Pago</h3>
            <input v-model="reserva.datosPago.numero_tarjeta" placeholder="Número de Tarjeta" maxlength="16" />
            <input v-model="reserva.datosPago.fecha_vencimiento" placeholder="MM/YY" maxlength="5" />
            <input v-model="reserva.datosPago.cvv" placeholder="CVV" type="password" maxlength="3" />

            <!-- Datos de usuario -->
            <input v-model="reserva.datosPago.nombre" placeholder="Nombre" />
            <input v-model="reserva.datosPago.apellido" placeholder="Apellido" />
            <input v-model="reserva.datosPago.email" placeholder="Correo Electrónico" type="email" />

            <button @click="realizarPagoIndividual(reserva)" :disabled="procesandoPago">Realizar Pago</button>
          </div>
        </li>
      </ul>

      <p v-else>No tienes reservas en el carrito.</p>

      <!-- Mensaje de error si se superan las 10 entradas -->
      <p v-if="mensajeError" style="color: red;">{{ mensajeError }}</p>

      <!-- Botón para mostrar el formulario de pago para todo el carrito -->
      <div v-if="carrito.length > 0 && !mostrarFormulario">
        <button @click="mostrarFormulario = true" :disabled="procesandoPago">
          💳 Pagar Todo
        </button>
      </div>

      <!-- Formulario de Pago para todo el carrito -->
      <div v-if="mostrarFormulario">
        <h3>Ingrese sus Datos de Pago</h3>
        <input v-model="datosPago.numero_tarjeta" placeholder="Número de Tarjeta" maxlength="16" />
        <input v-model="datosPago.fecha_vencimiento" placeholder="MM/YY" maxlength="5" />
        <input v-model="datosPago.cvv" placeholder="CVV" type="password" maxlength="3" />

        <!-- Datos de usuario -->
        <input v-model="datosPago.nombre" placeholder="Nombre" />
        <input v-model="datosPago.apellido" placeholder="Apellido" />
        <input v-model="datosPago.email" placeholder="Correo Electrónico" type="email" />

        <button @click="realizarPago" :disabled="procesandoPago">Realizar Pago</button>
        <p v-if="mensaje">{{ mensaje }}</p>
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
  };
},

  methods: {
    cargarCarrito() {
      communicationManager.getCarrito()
        .then(data => {
          console.log("Carrito recibido de la API:", data);
          this.carrito = data.map(reserva => ({
            ...reserva,
            pagarIndividual: false,
            datosPago: {
              numero_tarjeta: '',
              fecha_vencimiento: '',
              cvv: '',
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
    realizarPagoIndividual(reserva) {
  if (!reserva.datosPago.numero_tarjeta || !reserva.datosPago.fecha_vencimiento || !reserva.datosPago.cvv || !reserva.datosPago.nombre || !reserva.datosPago.apellido || !reserva.datosPago.email) {
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
      reserva.pagarIndividual = false;
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

  if (!this.datosPago.numero_tarjeta || !this.datosPago.fecha_vencimiento || !this.datosPago.cvv || !this.datosPago.nombre || !this.datosPago.apellido || !this.datosPago.email) {
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
      this.mostrarFormulario = false;
    })
    .catch(error => {
      this.mensaje = "⚠️ Error al procesar el pago. Inténtalo nuevamente.";
    })
    .finally(() => {
      this.procesandoPago = false;
    });
},
  },
  mounted() {
    this.cargarCarrito();
  },
};
</script>
