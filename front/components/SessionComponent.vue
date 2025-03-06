<template>
    <div>
      <h1>Comprar Entradas para la Película</h1>
      <p>Session ID: {{ $route.params.id }}</p>
      <!-- El resto del contenido de tu componente -->
    </div>
  </template>
  
  <script>
  export default {
    props: ['id'],  // Esto es necesario si usas 'props: true' en la ruta
    mounted() {
      console.log('Session ID:', this.id);  // Usamos `this.id` si usamos props
    }
  };
  </script>
  