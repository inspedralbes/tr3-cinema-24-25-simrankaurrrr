<template>
  <div class="container" :class="{ 'right-panel-active': isRegisterActive }" id="container">
    <!-- Formulario de Registro -->
    <div class="form-container sign-up-container">
      <form @submit.prevent="handleRegister">
        <h1>Create Account</h1>
        <div class="social-container">
          <a href="#" class="social"><i class="fab fa-facebook-f"></i></a>
          <a href="#" class="social"><i class="fab fa-google-plus-g"></i></a>
          <a href="#" class="social"><i class="fab fa-linkedin-in"></i></a>
        </div>
        <span>or use your email for registration</span>
        <input type="text" placeholder="Name" v-model="registerData.name" required />
        <input type="email" placeholder="Email" v-model="registerData.email" required />
        <input type="password" placeholder="Password" v-model="registerData.password" required />
        <input type="tel" placeholder="Phone" v-model="registerData.phone" required />
        <input type="text" placeholder="Address" v-model="registerData.address" required />
        <input type="date" placeholder="Birthdate" v-model="registerData.birthdate" required />
      
        <button type="submit">Sign Up</button>
      </form>
    </div>

    <!-- Formulario de Login -->
    <div class="form-container sign-in-container">
      <form @submit.prevent="handleLogin">
        <h1>Sign in</h1>
        <div class="social-container">
          <a href="#" class="social"><i class="fab fa-facebook-f"></i></a>
          <a href="#" class="social"><i class="fab fa-google-plus-g"></i></a>
          <a href="#" class="social"><i class="fab fa-linkedin-in"></i></a>
        </div>
        <span>or use your account</span>
        <input type="email" placeholder="Email" v-model="loginData.email" required />
        <input type="password" placeholder="Password" v-model="loginData.password" required />
        <button type="submit">Sign In</button>
      </form>
    </div>

    <!-- Overlay -->
    <div class="overlay-container">
      <div class="overlay">
        <div class="overlay-panel overlay-left">
          <h1>Welcome Back!</h1>
          <p>To keep connected with us please login with your personal info</p>
          <button class="ghost" @click="toggleRegister(false)">Sign In</button>
        </div>
        <div class="overlay-panel overlay-right">
          <h1>Hello, Friend!</h1>
          <p>Enter your personal details and start journey with us</p>
          <button class="ghost" @click="toggleRegister(true)">Sign Up</button>
        </div>
      </div>
    </div>

    <!-- Mensaje de error -->
    <div v-if="error" class="error-message">{{ error }}</div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import communicationManager from '../services/communicationManager'; // Asegúrate de importar tu servicio

const router = useRouter();

// Estado para el formulario de registro
const registerData = ref({
  name: '',
  email: '',
  password: '',
  phone: '',
  address: '',
  birthdate: '',
  role: 'user',
});

// Estado para el formulario de login
const loginData = ref({
  email: '',
  password: '',
});

// Estado para manejar el panel activo (registro o login)
const isRegisterActive = ref(false);

// Estado para manejar errores
const error = ref('');

// Función para alternar entre registro y login
const toggleRegister = (active) => {
  isRegisterActive.value = active;
};

const handleRegister = async () => {
  try {
    console.log("Datos de registro a enviar:", registerData.value);

    // Realizar la solicitud de registro
    const response = await communicationManager.registerUser(registerData.value);

    // Depuración: Mostrar la respuesta del servidor
    console.log("Respuesta del servidor:", response);

    // Verificar si la respuesta es exitosa
    if (response && response.message === "User registered successfully") {
      // Redirigir al home después del registro
      router.push('/');
    } else {
      throw new Error('Registration failed: Respuesta inesperada del servidor');
    }
  } catch (err) {
    // Manejar errores
    error.value = err.message || 'Registration failed. Please try again.';
    console.error("Error durante el registro:", err);
  }
};

const handleLogin = async () => {
  try {
    const response = await communicationManager.loginUser(loginData.value);
    
    // Verificar si la respuesta es exitosa
    if (response && response.auth_token) {
      // Redirigir al home después del login
      router.push('/');
    } else {
      throw new Error('Invalid credentials');
    }
  } catch (err) {
    // Manejar errores específicos
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
  max-width: 320px; /* Ancho máximo para el formulario */
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
  font-family: inherit; /* Asegura misma fuente */
  box-sizing: border-box; /* Modelo de caja consistente */
}
input:focus, select:focus {
  border-color: #ef233c;
  outline: none;
}
/* Estilo para el input de fecha para que coincida */
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
/* Estilo para el hover/focus */
select:hover, 
select:focus {
  border-color: #ef233c;
  outline: none;
}

/* Estilo específico para el select para igualarlo completamente */
select {
  appearance: none; /* Elimina el estilo nativo del navegador */
  -webkit-appearance: none; /* Para Safari */
  -moz-appearance: none; /* Para Firefox */
  background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%232b2d42'%3e%3cpath d='M7 10l5 5 5-5z'/%3e%3c/svg%3e");
  background-repeat: no-repeat;
  background-position: right 12px center;
  background-size: 12px;
  padding-right: 32px; /* Espacio para la flecha */
  cursor: pointer; /* Cambia el cursor para indicar que es seleccionable */
}

/* Estilo para las opciones */
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
/* Mejoramos el responsive */
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