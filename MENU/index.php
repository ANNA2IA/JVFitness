<?php  
$seccion = $_GET['seccion'] ?? 'clientes';
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Menú Principal</title>
  <link rel="stylesheet" href="styles.css">
  <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&display=swap" rel="stylesheet"> <!-- Fuente deportiva -->
  <style>
    body {
      margin: 0;
      font-family: 'Bebas Neue', cursive;
      background-color: #111;
      color: white;
      display: flex;
      flex-direction: column;
      align-items: center;
      background-image: url('modelo.jpg'); /* Reemplazá con el nombre real */
      background-repeat: no-repeat;
      background-size: contain;
      background-position: center top;
      background-attachment: fixed;
    }

    h1 {
      color: red;
      font-size: 60px;
      margin-top: 150px;
      text-shadow: 2px 2px 5px black;
    }

    h2 {
      color: orange;
      margin-top: 20px;
      font-size: 32px;
    }

    form {
      background-color: rgba(0, 0, 0, 0.85);
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0 0 15px #000;
      max-width: 600px;
      width: 90%;
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
      transition: background 0.3s;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .fab:hover {
      background-color: #ff9900;
    }

    .fab-options {
      position: absolute;
      bottom: 70px;
      right: 0;
      flex-direction: column;
      gap: 10px;
      display: flex;
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

<?php if ($seccion == 'clientes'): ?>
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
    <input type="hidden" name="accion" value="ingresar">
    <button type="submit">Ingresar</button>
    <button type="submit">Modificar</button>
    <button type="submit">Eliminar</button>
    <button type="submit">Buscar</button>
  </form>

<?php elseif ($seccion == 'planes'): ?>
  <h2>Formulario de Planes</h2>
  <form method="POST" action="menu.php">
    <input type="text" name="codigoPL" placeholder="Código del plan" required>
    <input type="text" name="Nombres" placeholder="Nombre del plan" required>
    <input type="text" name="Duracion" placeholder="Duración (ej: 30 días)" required>
    <input type="text" name="Precio" placeholder="Precio" required>
    <input type="hidden" name="tipo" value="plan">
    <input type="hidden" name="accion" value="ingresar">
    <button type="submit">Ingresar</button>
    <button type="submit">Modificar</button>
    <button type="submit">Eliminar</button>
    <button type="submit">Buscar</button>
  </form>

<?php elseif ($seccion == 'promociones'): ?>
  <h2>Formulario de Promociones</h2>
  <form method="POST" action="menu.php">
    <input type="text" name="codigoP" placeholder="Código de promoción" required>
    <input type="text" name="Nombres" placeholder="Nombre de promoción" required>
    <input type="text" name="Precio" placeholder="Precio" required>
    <input type="date" name="Fecha_Ini" placeholder="Fecha Inicio" required>
    <input type="date" name="Fecha_Fin" placeholder="Fecha Fin" required>
    <input type="text" name="Descripcion" placeholder="Descripción" required>
    <input type="hidden" name="tipo" value="promocion">
    <input type="hidden" name="accion" value="ingresar">
    <button type="submit">Ingresar</button>
    <button type="submit">Modificar</button>
    <button type="submit">Eliminar</button>
    <button type="submit">Buscar</button>
  </form>

<?php elseif ($seccion == 'administradores'): ?>
  <h2>Formulario de Administradores</h2>
  <form method="POST" action="menu.php">
    <input type="text" name="codigoA" placeholder="Código" required>
    <input type="text" name="Nombres" placeholder="Nombres" required>
    <input type="text" name="Apellidos" placeholder="Apellidos" required>
    <input type="text" name="Usuario" placeholder="Usuario" required>
    <input type="password" name="Contrasena" placeholder="Contraseña" required>
    <input type="hidden" name="tipo" value="admin">
    <input type="hidden" name="accion" value="ingresar">
    <button type="submit">Ingresar</button>
    <button type="submit">Modificar</button>
    <button type="submit">Eliminar</button>
    <button type="submit">Buscar</button>
  </form>
<?php endif; ?>

<!-- FAB -->
<div class="fab-container">
  <div class="fab">+</div>
  <div class="fab-options">
    <a href="?seccion=clientes">Clientes</a>
    <a href="?seccion=planes">Planes</a>
    <a href="?seccion=promociones">Promociones</a>
    <a href="?seccion=administradores">Administradores</a>
  </div>
</div>

<!-- Script -->
<script>
  const fab = document.querySelector('.fab');
  const container = document.querySelector('.fab-container');

  fab.addEventListener('click', () => {
    container.classList.toggle('show');
  });

  document.addEventListener('click', (e) => {
    if (!fab.contains(e.target)) {
      container.classList.remove('show');
    }
  });
</script>

</body>
</html>


