<template>
    <nav class="bg-gray-800 text-white p-4">
      <div class="container mx-auto flex justify-between items-center">
        <!-- Logo o Nombre del Sitio -->
        <NuxtLink to="/" class="text-xl font-bold">Mi Cine</NuxtLink>
  
        <!-- Enlaces de navegación -->
        <div class="links-container">
          <NuxtLink to="/register" class="login-link">
            Haz login
          </NuxtLink>
          <NuxtLink to="crud/crud1" class="login-link">
            CRUD
          </NuxtLink>
        </div>
  
        <!-- Botón de Carrito -->
        <button @click="toggleCarritoPopup" class="text-white">
          🛒 Carrito
        </button>
  
        <!-- Botón Logout -->
        <button @click="logout" class="logout-button">
          Logout
        </button>
      </div>
    </nav>
  
    <!-- Mostrar el componente Carrito cuando se hace clic en el botón -->
    <Carrito v-if="showCarrito" class="open"/>
  </template>
  
  <script setup>
  import { ref } from 'vue';
  import Carrito from '~/components/Carrito.vue';
  
  const showCarrito = ref(false);
  
  function toggleCarritoPopup() {
    showCarrito.value = !showCarrito.value;
  }
  
  function logout() {
    localStorage.removeItem('auth_token');
        console.log('Logout');
  }
  </script>
  
  <style scoped>
  nav {
    background-color: #333;
  }
  
  nav .text-white {
    color: white;
  }
  
  nav .container {
    display: flex;
    justify-content: space-between;
    align-items: center;
    width: 100%;
  }
  
  .links-container {
    display: flex;
    gap: 15px; /* Espacio entre los enlaces */
  }
  
  .login-link {
    color: white;
    text-decoration: none;
    font-size: 16px;
    font-weight: 500;
  }
  
  .login-link:hover {
    color: #ff6347; /* Cambia a un color diferente cuando el mouse pasa por encima */
  }
  
  .logout-button {
    background: none;
    border: none;
    color: white;
    font-size: 16px;
    cursor: pointer;
  }
  
  .logout-button:hover {
    color: #ff6347; /* Color de texto cuando se pasa por encima */
  }
  </style>
  