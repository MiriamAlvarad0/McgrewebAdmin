<template>
    <div class="container">
        <h1>Error 404</h1>
        <p>¡Oops! Parece que la página que estás buscando no existe.</p>
        <p>¿Quieres volver a la <a href="/">página de inicio</a>?</p>
        <span class="error-code">404</span>
    </div>
</template>
<style scoped>
    body {
      font-family: Arial, sans-serif;
      background-color: red; /* Color de fondo */
      margin: 0;
      padding: 0;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }

    .container {
      text-align: center;
    }

    h1 {
      font-size: 4rem;
      margin-bottom: 20px;
      color: #dc3545; /* Rojo oscuro */
    }

    p {
      font-size: 1.2rem;
      margin-bottom: 30px;
      color: #6c757d; /* Gris oscuro */
    }

    a {
      color: #007bff; /* Azul */
      text-decoration: none;
      font-weight: bold;
    }

    a:hover {
      text-decoration: underline;
    }

    .error-code {
      font-size: 10rem;
      margin-bottom: 20px;
      color: #dc3545; /* Rojo oscuro */
    }
</style>