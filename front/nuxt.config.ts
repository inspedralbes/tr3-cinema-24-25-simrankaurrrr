export default defineNuxtConfig({
  runtimeConfig: {
    public: {
      apiBase: 'http://127.0.0.1:8000/api', // Base URL de la API
    },
    pages: {
      // Deberías tener una página para comprar entradas
      'buy-ticket/:id': 'pages/buy-ticket.vue',
    },
  },

  compatibilityDate: '2025-03-06',
});