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
      apiBase: 'http://mdvd.daw.inspedralbes.cat/back/public/api' // Base URL de la API
    }
  },
  
  // Configuración de CSS global
  css: [
    'normalize.css'
  ],

})