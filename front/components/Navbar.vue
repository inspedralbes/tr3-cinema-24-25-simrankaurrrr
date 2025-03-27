<template>
  <header class="header">
    <div class="header-container">
      <!-- Logo o Nombre del Sitio -->
      <h1><span style="color: #ef233c;">MD</span>VD</h1>

      <!-- Enlaces de navegación -->
      <nav class="nav-links">
        <NuxtLink to="/" class="nav-link">Home</NuxtLink>
        <NuxtLink to="/buscar" class="nav-link">Buscar</NuxtLink>
        <NuxtLink to="/tusCompras" class="nav-link">Tus compras</NuxtLink>
        <!-- Mostrar CRUD solo si el usuario es admin -->
        <NuxtLink v-if="isAdmin" to="/crud/crud1" class="nav-link">Crud</NuxtLink>
      </nav>

      <!-- Botones -->
      <div class="button-container">
        <!-- Botón de Carrito -->
        <button @click="toggleCarritoPopup" class="button cart-button">
          🛒 Carrito
        </button>

        <!-- Botón de Iniciar sesión o Logout -->
        <button v-if="!isLoggedIn" @click="goToLogin" class="button login-button">
          Iniciar sesión
        </button>
        <button v-else @click="logout" class="button logout-button">
          Logout
        </button>
      </div>
    </div>
  </header>

  <!-- Mostrar el componente Carrito cuando se hace clic en el botón -->
  <Carrito v-if="showCarrito" />
</template>

<script setup>
import { ref, onMounted } from 'vue';
import Carrito from '~/components/Carrito.vue';
import communicationManager from '~/services/communicationManager';

const showCarrito = ref(false);
const isAdmin = ref(false);
const isLoggedIn = ref(false);

function checkAuthStatus() {
  const token = localStorage.getItem('auth_token');
  isLoggedIn.value = !!token;
}

async function checkUserRole() {
  try {
    const role = await communicationManager.getUserRole();
    isAdmin.value = role === 'admin';
  } catch (error) {
    console.error('Error al verificar el rol:', error);
    isAdmin.value = false;
  }
}

function toggleCarritoPopup() {
  showCarrito.value = !showCarrito.value;
}

function logout() {
  communicationManager.logout();
  isAdmin.value = false;
  isLoggedIn.value = false;
  window.location.href = '/';
}

function goToLogin() {
  window.location.href = '/register';
}

onMounted(() => {
  checkAuthStatus();
  if (isLoggedIn.value) {
    checkUserRole();
  }
});
</script>

<style scoped>
.header {
  background-color: #2b2d42;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
  position: sticky;
  top: 0;
  z-index: 1000;
  width: 100%;
  padding: 0;
  margin: 0;
  overflow-x: hidden;
}

.header-container {
  max-width: 100%;
  margin: 0 auto;
  display: flex;
  height: 70px;
  align-items: center;
  justify-content: space-between;
  padding: 0 20px;
  box-sizing: border-box;
}

.header h1 {
  font-size: 40px;
  color: #edf2f4;
  margin: 0;
  white-space: nowrap;
  flex-shrink: 0;
}

.header h1 span {
  color: #ef233c;
}

.nav-links {
  display: flex;
  gap: 15px;
  margin: 0 auto;
  padding: 0 10px;
  flex-grow: 1;
  justify-content: center;
  flex-wrap: wrap;
  max-width: 600px;
}

.nav-link {
  text-decoration: none;
  color: #8d99ae;
  font-size: 16px;
  font-weight: 700;
  padding: 8px 12px;
  transition: all 0.3s ease;
  border-radius: 4px;
  white-space: nowrap;
}

.nav-link:hover {
  color: #ef233c;
  background-color: rgba(237, 242, 244, 0.1);
}

.nav-link.router-link-active {
  color: #ef233c;
  border-bottom: 2px solid #ef233c;
}

.button-container {
  display: flex;
  gap: 10px;
  flex-shrink: 0;
}

.button {
  padding: 8px 16px;
  border-radius: 6px;
  font-weight: 600;
  font-size: 14px;
  cursor: pointer;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  white-space: nowrap;
}

.cart-button {
  background-color: #691e06;
  color: #edf2f4;
  border: 2px solid #691e06;
}

.cart-button:hover {
  background-color: #d80032;
  border-color: #d80032;
  transform: translateY(-2px);
}

.login-button {
  background-color: #8d99ae;
  color: #2b2d42;
  border: 2px solid #8d99ae;
}

.login-button:hover {
  background-color: #edf2f4;
  color: #2b2d42;
  transform: translateY(-2px);
}

.logout-button {
  background-color: #691e06;
  color: #edf2f4;
  border: 2px solid #691e06;
}

.logout-button:hover {
  background-color: #d80032;
  border-color: #d80032;
  transform: translateY(-2px);
}

@media (max-width: 1024px) {
  .header-container {
    padding: 0 15px;
  }

  .nav-links {
    gap: 10px;
    padding: 0 5px;
  }

  .nav-link {
    font-size: 15px;
    padding: 6px 10px;
  }

  .button {
    padding: 7px 14px;
    font-size: 13px;
  }
}

@media (max-width: 768px) {
  .header-container {
    margin-top: 10px;
    flex-direction: column;
    height: auto;
    padding: 10px;
  }

  .header h1 {
    margin: 5px 0;
    font-size: 25px;
    position: fixed;
    left: 10px;
    top: 3px;
  }

  .nav-links {
    margin: 10px 0 0;
    gap: 8px;
    justify-content: center;
    width: 100%;
  }

}

@media (max-width: 480px) {

  /* Cambiar el layout de los enlaces de navegación en pantallas pequeñas */
  .nav-links {
    width: 80%;
    display: flex;
    flex-direction: row;
    justify-content: space-between;
  }

  /* Modificar el layout de los botones en pantallas pequeñas */

  .button {
    width: 60%;
    max-width: 100px;
  }

  /* Para pantallas muy pequeñas (por ejemplo, móviles en modo vertical) */
  .header-container {
    flex-direction: column;
    gap: 15px;
  }

  /* Organizar los botones en una fila separada */
  .button-container {
    width: 50%;
    display: flex;
    flex-direction: row;
    justify-content: space-between;
  }
}
</style>
