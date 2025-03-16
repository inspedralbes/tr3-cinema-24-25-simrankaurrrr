<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router'; // Importa el router de Vue
import communicationManager from '../services/communicationManager';

const router = useRouter(); // Obtén el router de Vue

const name = ref('');
const email = ref('');
const password = ref('');
const phone = ref('');
const address = ref('');
const birthdate = ref('');
const role = ref('user');
const error = ref('');

const handleRegister = async () => {
  const userData = {
    name: name.value,
    email: email.value,
    password: password.value,
    phone: phone.value,
    address: address.value,
    birthdate: birthdate.value,
    role: role.value
  };

  try {
    const response = await communicationManager.registerUser(userData);
    
    // Redirige al home u otra página
    router.push('/');
  } catch (err) {
    error.value = 'Registration failed. Please try again.';
  }
};
</script>

<template>
    <div class="register-form">
      <h2>Register</h2>
  
      <!-- Formulario de registro -->
      <form @submit.prevent="handleRegister">
        <div>
          <label for="name">Name</label>
          <input type="text" id="name" v-model="name" />
        </div>
  
        <div>
          <label for="email">Email</label>
          <input type="email" id="email" v-model="email" />
        </div>
  
        <div>
          <label for="password">Password</label>
          <input type="password" id="password" v-model="password" />
        </div>
  
        <div>
          <label for="phone">Phone</label>
          <input type="tel" id="phone" v-model="phone" />
        </div>
  
        <div>
          <label for="address">Address</label>
          <input type="text" id="address" v-model="address" />
        </div>
  
        <div>
          <label for="birthdate">Birthdate</label>
          <input type="date" id="birthdate" v-model="birthdate" />
        </div>
  
        <div>
          <label for="role">Role</label>
          <select id="role" v-model="role">
            <option value="user">User</option>
            <option value="admin">Admin</option>
          </select>
        </div>
  
        <!-- Mensaje de error -->
        <div v-if="error" class="error-message">{{ error }}</div>
  
        <button type="submit">Register</button>
      </form>
    </div>
    <p>Already have an account? <nuxt-link to="/login">Login</nuxt-link></p>
</template>
