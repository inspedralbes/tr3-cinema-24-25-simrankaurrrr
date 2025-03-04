import './assets/main.css'

import { createApp } from 'vue'
import { createPinia } from 'pinia'
import App from './App.vue'
import router from './router' 

const app = createApp(App)

// Usa Pinia para el manejo del estado global
app.use(createPinia())

// Usa el router para manejar las rutas
app.use(router)

app.mount('#app')
