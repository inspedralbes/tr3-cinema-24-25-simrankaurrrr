Aquí tens el teu codi traduït al català! 😊  

```vue
<template>
  <div class="container" :class="{ 'right-panel-active': isRegisterActive }" id="container">
    <!-- Formulari de Registre -->
    <div class="form-container sign-up-container">
      <form @submit.prevent="handleRegister">
        <h1>Crea un compte</h1>
        <div class="social-container">
          <a href="#" class="social"><i class="fab fa-facebook-f"></i></a>
          <a href="#" class="social"><i class="fab fa-google-plus-g"></i></a>
          <a href="#" class="social"><i class="fab fa-linkedin-in"></i></a>
        </div>
        <span>o utilitza el teu correu electrònic per registrar-te</span>
        <input type="text" placeholder="Nom" v-model="registerData.name" required />
        <input type="email" placeholder="Correu electrònic" v-model="registerData.email" required />
        <input type="password" placeholder="Contrasenya" v-model="registerData.password" required />
        <input type="tel" placeholder="Telèfon" v-model="registerData.phone" required />
        <input type="text" placeholder="Adreça" v-model="registerData.address" required />
        <input type="date" placeholder="Data de naixement" v-model="registerData.birthdate" required />
      
        <button type="submit">Registra't</button>
      </form>
    </div>

    <!-- Formulari d'Inici de Sessió -->
    <div class="form-container sign-in-container">
      <form @submit.prevent="handleLogin">
        <h1>Inicia sessió</h1>
        <div class="social-container">
          <a href="#" class="social"><i class="fab fa-facebook-f"></i></a>
          <a href="#" class="social"><i class="fab fa-google-plus-g"></i></a>
          <a href="#" class="social"><i class="fab fa-linkedin-in"></i></a>
        </div>
        <span>o utilitza el teu compte</span>
        <input type="email" placeholder="Correu electrònic" v-model="loginData.email" required />
        <input type="password" placeholder="Contrasenya" v-model="loginData.password" required />
        <button type="submit">Inicia sessió</button>
      </form>
    </div>

    <!-- Superposició -->
    <div class="overlay-container">
      <div class="overlay">
        <div class="overlay-panel overlay-left">
          <h1>Benvingut de nou!</h1>
          <p>Per mantenir-te connectat amb nosaltres, inicia sessió amb la teva informació personal</p>
          <button class="ghost" @click="toggleRegister(false)">Inicia sessió</button>
        </div>
        <div class="overlay-panel overlay-right">
          <h1>Hola, amic!</h1>
          <p>Introdueix les teves dades personals i comença el teu viatge amb nosaltres</p>
          <button class="ghost" @click="toggleRegister(true)">Registra't</button>
        </div>
      </div>
    </div>

    <!-- Missatge d'error -->
    <div v-if="error" class="error-message">{{ error }}</div>
  </div>
</template>


<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import communicationManager from '../services/communicationManager'; // Asegúrate de importar tu servicio

const router = useRouter();

const registerData = ref({
  name: '',
  email: '',
  password: '',
  phone: '',
  address: '',
  birthdate: '',
  role: 'user',
});

const loginData = ref({
  email: '',
  password: '',
});

const isRegisterActive = ref(false);

const error = ref('');

const toggleRegister = (active) => {
  isRegisterActive.value = active;
};

const handleRegister = async () => {
  try {
    console.log("Datos de registro a enviar:", registerData.value);

    const response = await communicationManager.registerUser(registerData.value);

    console.log("Respuesta del servidor:", response);

    if (response && response.message === "User registered successfully") {
      router.push('/');
    } else {
      throw new Error('Registration failed: Respuesta inesperada del servidor');
    }
  } catch (err) {
    error.value = err.message || 'Registration failed. Please try again.';
    console.error("Error durante el registro:", err);
  }
};

const handleLogin = async () => {
  try {
    const response = await communicationManager.loginUser(loginData.value);
    
    if (response && response.auth_token) {
      router.push('/');
    } else {
      throw new Error('Invalid credentials');
    }
  } catch (err) {
    if (err.response && err.response.status === 401) {
      error.value = 'Correo electrónico o contraseña incorrectos';
    } else {
      error.value = 'Error en el servidor. Inténtalo de nuevo más tarde.';
    }
    console.error("Error durante el login:", err);
  }
};
</script>


<style scoped>
.container {
  background-color: #2b2d42;
  border-radius: 10px;
  box-shadow: 0 14px 28px rgba(0, 0, 0, 0.25), 0 10px 10px rgba(0, 0, 0, 0.22);
  position: relative;
  overflow: hidden;
  width: 100%;
  max-width: 100%;
  min-height: 100vh;
  display: flex;
  justify-content: center;
  align-items: center;
}

.form-container {
  position: absolute;
  top: 0;
  height: 100%;
  width: 40%; 
  display: flex;
  justify-content: center;
  align-items: center;
  flex-direction: column;
  padding: 0 40px;
  text-align: center;
  transition: all 0.6s ease-in-out;
  background-color: #edf2f4;
}
form {
  width: 100%;
  max-width: 320px;
}

.sign-in-container {
  left: 0;
  z-index: 2;
  opacity: 1;
}

.sign-up-container {
  left: 0;
  z-index: 1;
  opacity: 0;
}

.container.right-panel-active .sign-in-container {
  transform: translateX(100%);
  opacity: 0;
}

.container.right-panel-active .sign-up-container {
  transform: translateX(100%);
  opacity: 1;
  z-index: 5;
}

.overlay-container {
  position: absolute;
  top: 0;
  left: 50%;
  width: 50%;
  height: 100%;
  overflow: hidden;
  transition: transform 0.6s ease-in-out;
  z-index: 100;
}

.container.right-panel-active .overlay-container {
  transform: translateX(-100%);
}

.overlay {
  background: linear-gradient(to right, #ef233c, #d80032);
  background-repeat: no-repeat;
  background-size: cover;
  background-position: 0 0;
  color: #edf2f4;
  position: relative;
  left: -100%;
  height: 100%;
  width: 200%;
  transform: translateX(0);
  transition: transform 0.6s ease-in-out;
}

.container.right-panel-active .overlay {
  transform: translateX(50%);
}

.overlay-panel {
  position: absolute;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-direction: column;
  padding: 0 40px;
  text-align: center;
  top: 0;
  height: 100%;
  width: 50%;
  transition: transform 0.6s ease-in-out;
}

.overlay-left {
  transform: translateX(-20%);
}

.container.right-panel-active .overlay-left {
  transform: translateX(0);
}

.overlay-right {
  right: 0;
  transform: translateX(0);
}

.container.right-panel-active .overlay-right {
  transform: translateX(20%);
}

h1 {
  font-weight: bold;
  margin: 0;
  color: #2b2d42;
}

.social-container {
  margin: 20px 0;
}

.social-container a {
  border: 1px solid #8d99ae;
  border-radius: 50%;
  display: inline-flex;
  justify-content: center;
  align-items: center;
  margin: 15px 0;
  height: 40px;
  width: 40px;
  color: #2b2d42;
}

span {
  font-size: 14px;
  color: #2b2d42;
  margin-bottom: 15px;
}

/* Estilo unificado para inputs y select */
input, select {
  background-color: #edf2f4;
  border: 2px solid #8d99ae;
  padding: 10px 12px;
  margin: 6px 0;
  width: 100%;
  border-radius: 5px;
  font-size: 14px;
  color: #2b2d42;
  height: 42px;
  font-family: inherit;
  box-sizing: border-box; 
}
input:focus, select:focus {
  border-color: #ef233c;
  outline: none;
}
input[type="date"] {
  appearance: none;
  -webkit-appearance: none;
  position: relative;
}

input[type="date"]::-webkit-calendar-picker-indicator {
  position: absolute;
  right: 10px;
  opacity: 0.7;
  cursor: pointer;
}
select:hover, 
select:focus {
  border-color: #ef233c;
  outline: none;
}

select {
  appearance: none; 
  -webkit-appearance: none; 
  -moz-appearance: none; 
  background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%232b2d42'%3e%3cpath d='M7 10l5 5 5-5z'/%3e%3c/svg%3e");
  background-repeat: no-repeat;
  background-position: right 12px center;
  background-size: 12px;
  padding-right: 32px; 
  cursor: pointer; 
}

select option {
  background-color: #edf2f4;
  color: #2b2d42;
  padding: 8px;
}

button {
  border-radius: 20px;
  border: 1px solid #ef233c;
  background-color: #ef233c;
  color: #edf2f4;
  font-size: 14px;
  font-weight: bold;
  letter-spacing: 1px;
  text-transform: uppercase;
  transition: all 0.3s ease;
  cursor: pointer;
  margin-top: 15px;
  padding: 10px 30px;}

button:hover {
  background-color: #d80032;
}

button.ghost {
  background-color: transparent;
  border-color: #edf2f4;
  color: #edf2f4;
}

button.ghost:hover {
  background-color: rgba(237, 242, 244, 0.1);
}

.forgot-password {
  color: #2b2d42;
  font-size: 14px;
  text-decoration: none;
  margin: 15px 0;
}

.forgot-password:hover {
  color: #ef233c;
}

.error-message {
  color: #d80032;
  font-weight: bold;
  margin-top: 10px;
}
@media (max-width: 1024px) {
  .form-container {
    width: 45%;
  }
}
@media (max-width: 768px) {
  .form-container {
    width: 100%;
    padding: 0 30px;

  }
  
  .overlay-container {
    display: none;
  }
  
  .container.right-panel-active .sign-in-container,
  .container.right-panel-active .sign-up-container {
    transform: translateX(0);
  }
    
  form {
    max-width: 280px;
  }
  
  h1 {
    font-size: 24px;
  }
  
  input, select {
    padding: 8px 10px;
    height: 38px;
  }

}
@media (max-width: 480px) {
  form {
    max-width: 100%;
  }
  
  .social-container a {
    height: 36px;
    width: 36px;
    margin: 0 3px;
  }
}
</style>