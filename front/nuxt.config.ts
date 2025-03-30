import { defineNuxtConfig } from 'nuxt/config';

export default defineNuxtConfig({
  nitro: {
    prerender: {
      ignore: ['/crud3', '/compra']
    }
  },
  runtimeConfig: {
    public: {
      apiBase: 'http://mdvdback.daw.inspedralbes.cat/api'
    }
  },
  css: [
    'normalize.css'
  ],
  compatibilityDate: '2025-03-28',
});
