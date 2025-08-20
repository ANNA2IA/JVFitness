<?php  
$seccion = $_GET['seccion'] ?? 'clientes';
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Menú Principal</title>
  <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&display=swap" rel="stylesheet">
  <style>
    body {
      margin: 0;
      font-family: 'Bebas Neue', cursive;
      background-color: black;
      color: white;
      display: flex;
      flex-direction: column;
      align-items: center;
      background-image: url('img/logo3.jpg'); /* Imagen de fondo */
      background-repeat: no-repeat;
      background-size: contain;
      background-position: center top;
      background-attachment: fixed;
    }

  h1 {
  font-size: 72px;
  font-family: 'Impact', 'Arial Black', sans-serif;
  letter-spacing: 4px;
  text-transform: uppercase;
  color: #aca7a7ff;
  text-shadow:
    4px 4px 8px #000000,
    0 0 20px rgba(255, 255, 255, 0.35),
    0 0 30px rgba(255, 255, 255, 0.36);
  margin-top: 40px;
  text-align: center;
}



    h2 {
      color: orange;
      margin-top: 50px;
      font-size: 32px;
    }

    form {
      background-color: rgba(0, 0, 0, 0.85);
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0 0 15px #000;
      max-width: 600px;
      width: 100%;
      margin-bottom: 50px;
    }

    input {
      display: block;
      width: 100%;
      margin: 10px 0;
      padding: 10px;
      background: #333;
      color: white;
      border: none;
      border-radius: 5px;
    }

    button {
      padding: 10px 15px;
      background: orange;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      color: black;
      font-weight: bold;
      margin-right: 10px;
    }

    button:hover {
      background: #ff9900;
    }

    .fab-container {
      position: fixed;
      bottom: 30px;
      right: 30px;
      z-index: 1000;
    }

    .fab {
  width: 60px;
  height: 60px;
  background-color: orange;
  border-radius: 50%;
  text-align: center;
  color: black;
  font-size: 30px;
  cursor: pointer;
  box-shadow: 0 2px 5px rgba(0,0,0,0.3);
  display: flex;
  align-items: center;
  justify-content: center;
  transition: background 0.3s, transform 0.3s;
}
.fab:hover {
  background-color: #ff9900;
}
.fab-container.show .fab {
  transform: rotate(45deg);
}



   .fab-options {
  position: absolute;
  bottom: 70px;
  right: 0;
  flex-direction: column;
  gap: 10px;
  display: flex;
  opacity: 0;
  transform: translateY(20px);
  transition: opacity 0.3s ease, transform 0.3s ease;
  pointer-events: none;
}
.fab-container.show .fab-options {
  opacity: 1;
  transform: translateY(0);
  pointer-events: auto;
}


    .fab-options a {
      background-color: #333;
      color: white;
      padding: 10px 15px;
      border-radius: 8px;
      text-decoration: none;
      font-size: 14px;
      white-space: nowrap;
      transition: background 0.3s;
    }

    .fab-options a:hover {
      background-color: orange;
      color: black;
    }
  </style>
</head>
<body>

<h1>Menú Principal</h1> 

<!-- CONTENEDOR GENERAL DE FORMULARIOS -->
<div id="formularios">

  <div id="form-clientes">
    <h2>Formulario de Clientes</h2>
    <form method="POST" action="menu.php">
      <input type="text" name="codigoC" placeholder="Código" required>
      <input type="text" name="Nombres" placeholder="Nombres" required>
      <input type="text" name="Apellidos" placeholder="Apellidos" required>
      <input type="date" name="Fecha_Nac" placeholder="Fecha de nacimiento" required>
      <input type="email" name="correo" placeholder="Correo" required>
      <input type="text" name="Telefono" placeholder="Teléfono" required>
      <input type="text" name="Registro" placeholder="Fecha de registro" required>
      <input type="hidden" name="tipo" value="cliente">
      <button type="submit" name="Ingresar">Insertar</button>
      <button type="submit" name="Modificar">Modificar</button>
      <button type="submit" name="Eliminar">Eliminar</button>
      <button type="submit" name="Buscar">Buscar</button>

    </form>
  </div>

  <div id="form-planes">
    <h2>Formulario de Planes</h2>
    <form method="POST" action="menu.php">
      <input type="text" name="codigoPL" placeholder="Código del plan" required>
      <input type="text" name="Nombres" placeholder="Nombre del plan" required>
      <input type="text" name="Duracion" placeholder="Duración (ej: 30 días)" required>
      <input type="text" name="Precio" placeholder="Precio" required>
      <input type="hidden" name="tipo" value="plan">
      <button type="submit" name="Ingresar">Insertar</button>
      <button type="submit" name="Modificar">Modificar</button>
      <button type="submit" name="Eliminar">Eliminar</button>
      <button type="submit" name="Buscar">Buscar</button>
    </form>
  </div>

  <div id="form-promociones">
    <h2>Formulario de Promociones</h2>
    <form method="POST" action="menu.php">
      <input type="text" name="codigoP" placeholder="Código de promoción" required>
      <input type="text" name="Nombres" placeholder="Nombre de promoción" required>
      <input type="text" name="Precio" placeholder="Precio" required>
      <input type="date" name="Fecha_Ini" placeholder="Fecha Inicio" required>
      <input type="date" name="Fecha_Fin" placeholder="Fecha Fin" required>
      <input type="text" name="Descripcion" placeholder="Descripción" required>
      <input type="hidden" name="tipo" value="promocion">
       <button type="submit" name="Ingresar">Insertar</button>
      <button type="submit" name="Modificar">Modificar</button>
      <button type="submit" name="Eliminar">Eliminar</button>
      <button type="submit" name="Buscar">Buscar</button>
    </form>
  </div>

  <div id="form-administradores">
    <h2>Formulario de Administradores</h2>
    <form method="POST" action="../ADMINISTRADORES/administradores.php">
      <input type="text" name="codigoA" placeholder="Código" required>
      <input type="text" name="Nombres" placeholder="Nombres" required>
      <input type="text" name="Apellidos" placeholder="Apellidos" required>
      <input type="text" name="Usuario" placeholder="Usuario" required>
      <input type="password" name="Contrasena" placeholder="Contraseña" required>
      <input type="hidden" name="tipo" value="admin">
       <button type="submit" name="Ingresar">Insertar</button>
      <button type="submit" name="Modificar">Modificar</button>
      <button type="submit" name="Eliminar">Eliminar</button>
      <button type="submit" name="Buscar">Buscar</button>
    </form>
  </div>

</div>

<!-- FAB -->
<div class="fab-container" id="fabContainer">
  <div class="fab" id="fab">+</div>
  <div class="fab-options">
    <a href="#" data-target="clientes">Clientes</a>
    <a href="#" data-target="planes">Planes</a>
    <a href="#" data-target="promociones">Promociones</a>
    <a href="#" data-target="administradores">Administradores</a>
    <a href="#" id="cerrar-formulario">Cerrar</a>
  </div>
</div>

<!-- Script -->
<script>
  const fabContainer = document.getElementById('fabContainer');
  const fab = document.getElementById('fab');
  const links = document.querySelectorAll('.fab-options a[data-target]');
  const formularios = document.querySelectorAll('#formularios > div');
  const btnCerrar = document.getElementById('cerrar-formulario');

  fab.addEventListener('click', () => {
    fabContainer.classList.toggle('show');
  });

  links.forEach(link => {
    link.addEventListener('click', (e) => {
      e.preventDefault();
      const target = link.getAttribute('data-target');

      // Ocultar todos los formularios
      formularios.forEach(f => f.style.display = 'none');

      // Mostrar solo el formulario correspondiente
      const seleccionado = document.getElementById('form-' + target);
      if (seleccionado) {
        seleccionado.style.display = 'block';
      }

      // Cerrar menú flotante
      fabContainer.classList.remove('show');
    });
  });

  // Cerrar formulario activo
  btnCerrar.addEventListener('click', (e) => {
  e.preventDefault();
  window.location.href = '../LOGIN/index.html'; // Asegúrate de que este sea el nombre correcto del archivo de login
});

</script>

<!-- CSS para ocultar formularios al inicio -->
<style>
  #formularios > div {
    display: none;
  }
</style>
