<template>
    <div>
      <h2>Carrito de Reservas</h2>
  
      <ul v-if="carrito.length > 0">
        <li v-for="(reserva, index) in carrito" :key="reserva.reserva_id">
          <p>🎬 Película: {{ reserva.nombre_pelicula }}</p>
          <p>📍 Asiento: Fila {{ reserva.fila }}, Columna {{ reserva.columna }}</p>
          <p>💲 Precio: ${{ reserva.precio }}</p>
          <button @click="eliminarReserva(reserva.reserva_id, index)">❌ Eliminar</button>
          <button @click="iniciarPago(reserva)">💳 Pagar</button>
        </li>
      </ul>
  
      <p v-else>No tienes reservas en el carrito.</p>
  
      <!-- Formulario de Pago -->
      <div v-if="mostrarFormulario">
        <h3>Ingrese sus Datos de Pago para {{ datosPago.nombre_pelicula }}</h3>
        <input v-model="datosPago.numero_tarjeta" placeholder="Número de Tarjeta" maxlength="16" />
        <input v-model="datosPago.fecha_vencimiento" placeholder="MM/YY" maxlength="5" />
        <input v-model="datosPago.cvv" placeholder="CVV" type="password" maxlength="3" />
  
        <button @click="realizarCompra" :disabled="procesandoPago">Comprar</button>
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
        mostrarFormulario: false,
        datosPago: {
          numero_tarjeta: '',
          fecha_vencimiento: '',
          cvv: '',
          compra_id: '',  // Aquí debe ir el compra_id
          nombre_pelicula: '',
        },
        procesandoPago: false,
        mensaje: '',
      };
    },
    methods: {
        cargarCarrito() {
  communicationManager.getCarrito()
    .then(data => {
      console.log("Carrito recibido de la API:", data);
      this.carrito = data.map(reserva => ({
   ...reserva,
   compra_id: reserva.compra_id ? reserva.compra_id : null, // No asignar reserva_id por defecto
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
          })
          .catch(error => {
            console.error('Error eliminando la reserva:', error);
          });
      },
      iniciarPago(reserva) {
  console.log("Reserva seleccionada para pago:", reserva); // Verifica qué contiene reserva

  // Verificamos si existe compra_id; si no, mostramos un error
  if (!reserva.compra_id) {
    console.error("❌ Error: No se encontró compra_id en la reserva.");
    this.mensaje = "❌ Error: No se encontró el ID de la compra.";
    return;  // Detenemos el proceso si no hay compra_id
  }

  // Asignamos correctamente los datos al objeto de pago
  this.datosPago = {
    numero_tarjeta: '',
    fecha_vencimiento: '',
    cvv: '',
    compra_id: reserva.compra_id,  // Usamos el compra_id de la reserva
    nombre_pelicula: reserva.nombre_pelicula,
  };

  console.log("Datos de pago inicializados:", this.datosPago);
  this.mostrarFormulario = true;
},
  
realizarCompra() {
  // Validación de datos de pago
  if (!this.datosPago.numero_tarjeta || !this.datosPago.fecha_vencimiento || !this.datosPago.cvv || !this.datosPago.compra_id) {
    this.mensaje = "❌ Por favor, completa todos los campos.";
    console.error("❌ Error: Falta información en el pago.", this.datosPago);
    return;
  }

  this.procesandoPago = true;
  this.mensaje = "⏳ Procesando pago...";

  // Realizar el pago llamando al servicio de comunicación
  communicationManager.realizarPago(this.datosPago)
    .then(response => {
      this.mensaje = "✅ " + response.message;
      this.carrito = this.carrito.filter(r => r.compra_id !== this.datosPago.compra_id);
      this.mostrarFormulario = false;
    })
    .catch(error => {
      this.mensaje = "⚠️ Error al procesar el pago. Inténtalo nuevamente.";
      console.error("⚠️ Error al realizar el pago:", error);
    })
    .finally(() => {
      this.procesandoPago = false;
    });
}
    },
    mounted() {
      this.cargarCarrito();
    }
  };
  </script>
  