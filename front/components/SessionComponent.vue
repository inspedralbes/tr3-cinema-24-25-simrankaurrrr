<template>
    <div>
      <h1>Comprar Entradas para la Película</h1>
      <p>Session ID: {{ $route.params.id }}</p>
    </div>
  </template>
  
  <script>
  export default {
    props: ['id'],  
    mounted() {
      console.log('Session ID:', this.id);  
    }
  };
  </script>
  