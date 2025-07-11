<?php
// Determinar la sección actual (clientes por defecto)
$seccion = $_GET['seccion'] ?? 'clientes';
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Menú Principal</title>
  <link rel="stylesheet" href="styles.css">
  <style>
    body {
      margin: 0;
      font-family: Arial, sans-serif;
      background-color: #1e1e1e;
      color: white;
      display: flex;
    }

    .sidebar {
      width: 220px;
      background-color: #111;
      padding-top: 60px;
      position: fixed;
      top: 0;
      left: 0;
      height: 100%;
      box-shadow: 2px 0 5px rgba(0,0,0,0.5);
    }

    .sidebar h2 {
      color: orange;
      text-align: center;
    }

    .sidebar a {
      display: block;
      color: orange;
      padding: 15px 20px;
      text-decoration: none;
      font-weight: bold;
    }

    .sidebar a:hover,
    .sidebar a.active {
      background-color: #333;
    }

    .main {
      margin-left: 220px;
      padding: 30px;
      width: 100%;
    }

    h1 {
      color: orange;
    }

    form {
      background-color: #222;
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0 0 15px #000;
      max-width: 600px;
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
  </style>
</head>
<body>

<div class="sidebar">
  <h2>Menú</h2>
  <a href="?seccion=clientes" class="<?= $seccion == 'clientes' ? 'active' : '' ?>">Clientes</a>
  <a href="?seccion=planes" class="<?= $seccion == 'planes' ? 'active' : '' ?>">Planes</a>
  <a href="?seccion=promociones" class="<?= $seccion == 'promociones' ? 'active' : '' ?>">Promociones</a>
  <a href="?seccion=administradores" class="<?= $seccion == 'administradores' ? 'active' : '' ?>">Administradores</a>
</div>

<div class="main">
  <h1>Menú Principal - Sistema de Control</h1>

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

</div>
</body>
</html>
