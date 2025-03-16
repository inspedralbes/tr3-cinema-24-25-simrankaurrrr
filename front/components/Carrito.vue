<template>
    <div>
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
  
          <!-- Botón para pagar individualmente -->
          <button @click="mostrarFormularioPagoIndividual(reserva)">
            💳 Pagar Individualmente
          </button>
  
          <!-- Formulario de Pago individual, solo aparece cuando se quiere pagar individualmente -->
          <div v-if="reserva.pagarIndividual">
            <h3>Ingrese sus Datos de Pago</h3>
            <input v-model="reserva.datosPago.numero_tarjeta" placeholder="Número de Tarjeta" maxlength="16" />
            <input v-model="reserva.datosPago.fecha_vencimiento" placeholder="MM/YY" maxlength="5" />
            <input v-model="reserva.datosPago.cvv" placeholder="CVV" type="password" maxlength="3" />
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
        <button @click="realizarPago" :disabled="procesandoPago">Realizar Pago</button>
        <p v-if="mensaje">{{ mensaje }}</p>
      </div>
    </div>
  </template>
  
  <script>
  import communicationManager from '@/services/communicationManager';
  
  export default {
    data() {
      return {
        carrito: [],
        mostrarFormulario: false,  // Controla la visualización de los inputs para pagar todo
        datosPago: {
          numero_tarjeta: '',
          fecha_vencimiento: '',
          cvv: '',
        },
        procesandoPago: false,
        mensaje: '',
        mensajeError: '',  // Variable para mostrar el error si se superan las 10 entradas
      };
    },
    methods: {
      cargarCarrito() {
        communicationManager.getCarrito()
          .then(data => {
            console.log("Carrito recibido de la API:", data);
            this.carrito = data.map(reserva => ({
              ...reserva,
              comprarIndividual: false,  // Se agrega el flag para gestionar el pago individual
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
            this.mensajeError = '';  // Limpiar mensaje de error cuando se elimina una reserva
          })
          .catch(error => {
            console.error('Error eliminando la reserva:', error);
          });
      },
  
      agregarReserva(reserva) {
        const reservaExistente = this.carrito.find(item => item.reserva_id === reserva.reserva_id);
        
        // Comprobar si el carrito ya tiene más de 10 reservas
        if (this.carrito.length >= 10) {
          this.mensajeError = '⚠️ No puedes añadir más de 10 entradas al carrito.';
          return;  // Evitar agregar más reservas
        }
  
        if (!reservaExistente) {
          this.carrito.push(reserva);
          this.mensajeError = '';  // Limpiar mensaje de error si se agrega una reserva correctamente
        }
      },
  
      // Mostrar formulario de pago para una reserva individual
      mostrarFormularioPagoIndividual(reserva) {
        reserva.pagarIndividual = !reserva.pagarIndividual;  // Alternar el estado del formulario
      },
  
      // Realizar el pago para una reserva individual
      realizarPagoIndividual(reserva) {
        if (!reserva.datosPago.numero_tarjeta || !reserva.datosPago.fecha_vencimiento || !reserva.datosPago.cvv) {
          this.mensaje = "⚠️ Todos los campos de pago son obligatorios.";
          return;
        }
  
        this.procesandoPago = true;
        this.mensaje = "⏳ Procesando pago...";
  
        // Crear el pago solo para esa reserva
        const pagoData = {
          numero_tarjeta: reserva.datosPago.numero_tarjeta,
          fecha_vencimiento: reserva.datosPago.fecha_vencimiento,
          cvv: reserva.datosPago.cvv,
          compra_id: [reserva.compra_id],  // Solo el `compra_id` de la reserva seleccionada
        };
  
        console.log('Datos de pago individual enviados:', pagoData);
  
        // Realizar el pago (solo para esa reserva)
        communicationManager.realizarPago(pagoData)
          .then(response => {
            this.mensaje = "✅ " + response.message;
            // Eliminar la reserva del carrito después de pagarla
            this.carrito = this.carrito.filter(item => item.reserva_id !== reserva.reserva_id);
            reserva.pagarIndividual = false; // Ocultamos los inputs después de realizar el pago
          })
          .catch(error => {
            this.mensaje = "⚠️ Error al procesar el pago. Inténtalo nuevamente.";
            console.error("⚠️ Error al realizar el pago individual:", error);
          })
          .finally(() => {
            this.procesandoPago = false;
          });
      },
  
      // Método para realizar el pago de todas las reservas
      realizarPago() {
        if (this.carrito.length === 0) {
          this.mensaje = "❌ No tienes reservas en el carrito.";
          return;
        }
  
        // Validar que los campos de pago no estén vacíos
        if (!this.datosPago.numero_tarjeta || !this.datosPago.fecha_vencimiento || !this.datosPago.cvv) {
          this.mensaje = "⚠️ Todos los campos de pago son obligatorios.";
          return;
        }
  
        this.procesandoPago = true;
        this.mensaje = "⏳ Procesando pago...";
  
        // Crear el array con solo los IDs de las compras
        const compraIds = this.carrito.map(reserva => reserva.compra_id);
  
        // Realizar el pago con los datos correctos
        const pagoData = {
          numero_tarjeta: this.datosPago.numero_tarjeta,
          fecha_vencimiento: this.datosPago.fecha_vencimiento,
          cvv: this.datosPago.cvv,
          compra_id: compraIds,  // Solo el array de compra_ids
        };
  
        console.log('Datos de pago enviados:', pagoData);
  
        // Realizar el pago (puede ser un pago único o múltiple)
        communicationManager.realizarPago(pagoData)
          .then(response => {
            this.mensaje = "✅ " + response.message;
            this.carrito = [];  // Limpiar el carrito después del pago
            this.mostrarFormulario = false;  // Ocultar el formulario de pago
          })
          .catch(error => {
            this.mensaje = "⚠️ Error al procesar el pago. Inténtalo nuevamente.";
            console.error("⚠️ Error al realizar el pago:", error);
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
  