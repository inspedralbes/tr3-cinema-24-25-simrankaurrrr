import LandingPage from '@/views/LandingPage.vue';
import { createRouter, createWebHistory } from 'vue-router';

const routes = [

{
    path: '/landing',
    name: 'LandingPage',
    component: LandingPage,
  }
];
  const router = createRouter({
    history: createWebHistory(),
    routes,
  });
  export default router;