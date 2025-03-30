export default defineNuxtConfig({
  // Configuración de Nitro para prerender
  nitro: {
    prerender: {
      ignore: ['/crud3', '/compra']
    }
  },

  // Configuración runtime (variables de entorno)
  runtimeConfig: {
    public: {
      apiBase: 'http://mdvdback.daw.inspedralbes.cat/api' // Base URL de la API
    }
  },

  // Configuración de CSS global
  css: [
    'normalize.css'
  ],

  compatibilityDate: '2025-03-28',
})