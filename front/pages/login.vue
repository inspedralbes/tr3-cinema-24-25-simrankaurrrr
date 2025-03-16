<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router'; // Importa el router de Vue
import communicationManager from '../services/communicationManager';

const router = useRouter(); // Obtén el router de Vue

const email = ref('');
const password = ref('');
const error = ref('');

const handleLogin = async () => {
  const credentials = {
    email: email.value,
    password: password.value
  };

  try {
    const response = await communicationManager.loginUser(credentials);
  
    // Redirige al home u otra página
    router.push('/');
  } catch (err) {
    error.value = 'Invalid credentials or server error';
  }
};
</script>

<template>
  <div class="login-container">
    <h1>Login</h1>
    <form @submit.prevent="handleLogin">
      <div>
        <label for="email">Email</label>
        <input v-model="email" type="email" id="email" required />
      </div>
      <div>
        <label for="password">Password</label>
        <input v-model="password" type="password" id="password" required />
      </div>
      <button type="submit">Login</button>
      <p v-if="error" class="error-message">{{ error }}</p>
    </form>
    <p>Don't have an account? <nuxt-link to="/register">Register</nuxt-link></p>
  </div>
</template>
