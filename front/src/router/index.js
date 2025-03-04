// src/router/index.js
import { createRouter, createWebHistory } from 'vue-router'
import LandingPage from '../views/LandingPage.vue'  // Importa tu componente LandingPage

const routes = [
  {
    path: '/',
    name: 'LandingPage',  // La ruta raíz mostrará LandingPage
    component: LandingPage
  },
  // Otras rutas pueden ir aquí si es necesario
]

const router = createRouter({
  history: createWebHistory(),  // Usamos la historia web para las rutas
  routes,  // Agregamos las rutas definidas
})

export default router
