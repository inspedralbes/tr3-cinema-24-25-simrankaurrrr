<template>
    <div class="container">
        <h1 class="title">Bienvenidos al Cine</h1>
        <p class="subtitle">¡Las mejores películas esperan por ti!</p>
        <div class="cta-buttons">
          <button class="btn">Explorar Películas</button>
          <button class="btn">Próximos Estrenos</button>
        </div>
    </div>
  </template>
  
  <script>
  export default {
    name: 'LandingPage'
  }
  </script>
  
  <style scoped>
  * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
  }
  
  body {
    font-family: 'Arial', sans-serif;
    color: rgb(186, 10, 10);
  }
  
  .container {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    text-align: center;
    background-color: aqua;
  }
  

  
  /* Título */
  .title {
    font-size: 3rem;
    margin-bottom: 10px;
    font-weight: bold;
  }
  
  /* Subtítulo */
  .subtitle {
    font-size: 1.5rem;
    margin-bottom: 30px;
  }
  
  /* Botones */
  .cta-buttons {
    display: flex;
    gap: 20px;
  }
  
  .btn {
    padding: 12px 24px;
    font-size: 1.2rem;
    color: white;
    background-color: #bcbcbc; /* Rojo de cine */
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s ease;
  }
  
  .btn:hover {
    background-color: #d32f2f;
  }
  </style>
  