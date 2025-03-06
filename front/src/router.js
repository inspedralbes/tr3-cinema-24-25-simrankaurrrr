import Vue from 'vue';
import Router from 'vue-router';
import SessionComponent from '@/components/SessionComponent.vue';

Vue.use(Router);

export default new Router({
  routes: [
    {
      path: '/sessions/:id',  // Ruta dinámica con el parámetro ':id'
      name: 'Session',         // Nombre de la ruta
      component: SessionComponent,
      props: true              // Pasar el parámetro 'id' como prop al componente
    },
    // Otras rutas...
  ]
});
