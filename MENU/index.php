<?php

include("../seguridad.php");

$servername = "localhost";
$username = "root"; 
$password = "admin123"; 
$dbname = "JV";


$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Lógica para obtener clientes y su estado de membresía
$sql = "SELECT 
            c.Nombres, 
            c.Telefono, 
            MAX(m.Fecha_Fin) AS Fecha_Fin
        FROM Clientes c
        LEFT JOIN Membresias m ON c.codigoC = m.codigoC
        GROUP BY c.codigoC
        ORDER BY Fecha_Fin ASC";

$result = $conn->query($sql);

$clientes = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $fecha_vencimiento = $row['Fecha_Fin'];
        $status = 'Sin membresía';
        if ($fecha_vencimiento) {
            $hoy = new DateTime();
            $vencimiento = new DateTime($fecha_vencimiento);
            $interval = $hoy->diff($vencimiento);
            
            if ($vencimiento < $hoy) {
                $status = 'Vencida';
            } elseif ($interval->days <= 7) {
                $status = 'Por vencer';
            } else {
                $status = 'Activa';
            }
        }
        
        $clientes[] = [
            'nombre' => $row['Nombres'],
            'telefono' => $row['Telefono'],
            'status' => $status,
            'vencimiento' => $fecha_vencimiento
        ];
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Menú Principal</title>
  <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Poppins:wght@300;400;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <style>
    :root {
      --orange: #ff6600;
      --dark-gray: #1a1a1a;
      --light-gray: #f5f5f5;
      --text-color: #e0e0e0;
      --shadow-color: rgba(0, 0, 0, 0.5);
    }
    body {
      margin: 0;
      font-family: 'Poppins', sans-serif;
      background-color: var(--dark-gray);
      color: var(--text-color);
      display: flex;
      flex-direction: column;
      align-items: center;
      min-height: 100vh;
      background-image: url('img/logo3.jpg');
      background-repeat: no-repeat;
      background-size: cover;
      background-position: center;
      background-attachment: fixed;
    }
    h1 {
      font-family: 'Bebas Neue', cursive;
      font-size: 7rem;
      letter-spacing: 4px;
      text-transform: uppercase;
      color: var(--light-gray);
      text-shadow: 4px 4px 8px var(--shadow-color);
      margin-top: 50px;
      text-align: center;
      line-height: 1;
    }
    h2 {
      color: var(--orange);
      margin-top: 50px;
      font-size: 2.5rem;
      text-shadow: 2px 2px 4px var(--shadow-color);
    }
    .form-container, .table-container {
      background-color: rgba(0, 0, 0, 0.85);
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 0 20px var(--shadow-color);
      max-width: 800px;
      width: 90%;
      margin-bottom: 50px;
      backdrop-filter: blur(5px);
    }
    form {
      display: grid;
      gap: 15px;
    }
    input, select {
      width: 100%;
      padding: 12px;
      background: #333;
      color: var(--text-color);
      border: 1px solid #555;
      border-radius: 6px;
      box-sizing: border-box;
      transition: border-color 0.3s;
    }
    input:focus, select:focus {
      border-color: var(--orange);
      outline: none;
    }
    .form-actions {
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
      justify-content: center;
      margin-top: 20px;
    }
    button {
      padding: 12px 25px;
      background: var(--orange);
      border: none;
      border-radius: 6px;
      cursor: pointer;
      color: black;
      font-weight: bold;
      font-family: 'Poppins', sans-serif;
      transition: background 0.3s, transform 0.2s;
    }
    button:hover {
      background: #ff8533;
      transform: translateY(-2px);
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
      background-color: var(--orange);
      border-radius: 50%;
      color: black;
      font-size: 30px;
      cursor: pointer;
      box-shadow: 0 4px 10px var(--shadow-color);
      display: flex;
      align-items: center;
      justify-content: center;
      transition: transform 0.3s;
    }
    .fab:hover {
      transform: scale(1.1);
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
      color: var(--text-color);
      padding: 12px 18px;
      border-radius: 8px;
      text-decoration: none;
      font-size: 14px;
      white-space: nowrap;
      transition: background 0.3s, color 0.3s;
      box-shadow: 0 2px 5px var(--shadow-color);
      display: flex;
      align-items: center;
      gap: 8px;
    }
    .fab-options a:hover {
      background-color: var(--orange);
      color: black;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
    }
    th, td {
      padding: 12px;
      text-align: left;
      border-bottom: 1px solid #444;
    }
    th {
      background-color: #222;
      color: var(--orange);
      font-weight: bold;
    }
    tr:hover {
      background-color: #333;
    }
    .status-vencida {
      color: #ff6666;
      font-weight: bold;
    }
    .status-por-vencer {
      color: #ffda00;
      font-weight: bold;
    }
    .status-activa {
      color: #66ff66;
      font-weight: bold;
    }
    .status-sin-membresia {
        color: #aaaaaa;
        font-style: italic;
    }
  </style>
</head>
<body>

<h1>Menú Principal</h1>

<div id="formularios">
  
  <div id="form-clientes-tabla" class="table-container">
    <h2>Clientes y Estatus de Membresía</h2>
    <table>
      <thead>
        <tr>
          <th>Nombre</th>
          <th>Teléfono</th>
          <th>Estatus</th>
          <th>Vencimiento</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($clientes as $cliente): ?>
          <tr class="cliente-row">
            <td><?php echo htmlspecialchars($cliente['nombre']); ?></td>
            <td><?php echo htmlspecialchars($cliente['telefono']); ?></td>
            <td class="status-<?php echo strtolower(str_replace(' ', '-', $cliente['status'])); ?>">
              <?php echo htmlspecialchars($cliente['status']); ?>
            </td>
            <td><?php echo htmlspecialchars($cliente['vencimiento'] ? $cliente['vencimiento'] : 'N/A'); ?></td>
          </tr>
        <?php endforeach; ?>
        <?php if (empty($clientes)): ?>
          <tr><td colspan="4">No hay clientes registrados.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
  
  <div id="form-clientes" class="form-container">
    <h2>Formulario de Clientes</h2>
    <form method="POST" action="../CLIENTES/clientes.php">
      <input type="text" name="codigoC" placeholder="Código" >
      <input type="text" name="Nombres" placeholder="Nombres" >
      <input type="text" name="Apellidos" placeholder="Apellidos" >
      <input type="date" name="Fecha_Nac" placeholder="Fecha de nacimiento" >
      <input type="email" name="correo" placeholder="Correo" >
      <input type="text" name="Telefono" placeholder="Teléfono" >
      <input type="text" name="Registro" placeholder="Fecha de registro" >
      <input type="hidden" name="tipo" value="cliente">
      <div class="form-actions">
        <button type="submit" name="Ingresar">Insertar</button>
        <button type="submit" name="Modificar">Modificar</button>
        <button type="submit" name="Eliminar">Eliminar</button>
        <button type="submit" name="Buscar">Buscar</button>
      </div>
    </form>
  </div>

  <div id="form-membresias" class="form-container">
    <h2>Formulario de Membresías</h2>
    <form method="POST" action="../MEMBRESIAS/membresias.php">
      <input type="text" name="codigo" placeholder="Código de Membresía" >
      <input type="text" name="codigoC" placeholder="Código del Cliente" >
      <input type="text" name="codigoPL" placeholder="Código del Plan" >
      <input type="date" name="Fecha_Ini" placeholder="Fecha de Inicio" >
      <input type="date" name="Fecha_Fin" placeholder="Fecha de Vencimiento" >
      <input type="text" name="Precio" placeholder="Precio" >
      <input type="text" name="metodo" placeholder="Método de pago" >
      <input type="hidden" name="tipo" value="membresia">
      <div class="form-actions">
        <button type="submit" name="Ingresar">Insertar</button>
        <button type="submit" name="Modificar">Modificar</button>
        <button type="submit" name="Eliminar">Eliminar</button>
        <button type="submit" name="Buscar">Buscar</button>
      </div>
    </form>
  </div>
  
  <div id="form-planes" class="form-container">
    <h2>Formulario de Planes</h2>
    <form method="POST" action="../PLANES/planes.php">
      <input type="text" name="codigoPL" placeholder="Código del plan" >
      <input type="text" name="Nombres" placeholder="Nombre del plan" >
      <input type="text" name="Duracion" placeholder="Duración (ej: 30 días)" >
      <input type="text" name="Precio" placeholder="Precio" >
      <input type="hidden" name="tipo" value="plan">
      <div class="form-actions">
        <button type="submit" name="Ingresar">Insertar</button>
        <button type="submit" name="Modificar">Modificar</button>
        <button type="submit" name="Eliminar">Eliminar</button>
        <button type="submit" name="Buscar">Buscar</button>
      </div>
    </form>
  </div>
  
  <div id="form-promociones" class="form-container">
    <h2>Formulario de Promociones</h2>
    <form method="POST" action="../PROMOCIONES/promociones.php">
      <input type="text" name="codigoP" placeholder="Código de promoción" >
      <input type="text" name="Nombres" placeholder="Nombre de promoción" >
      <input type="text" name="Precio" placeholder="Precio" >
      <input type="date" name="Fecha_Ini" placeholder="Fecha Inicio" >
      <input type="date" name="Fecha_Fin" placeholder="Fecha Fin" >
      <input type="text" name="Descripcion" placeholder="Descripción" >
      <input type="hidden" name="tipo" value="promocion">
      <div class="form-actions">
        <button type="submit" name="Ingresar">Insertar</button>
        <button type="submit" name="Modificar">Modificar</button>
        <button type="submit" name="Eliminar">Eliminar</button>
        <button type="submit" name="Buscar">Buscar</button>
      </div>
    </form>
  </div>
  
  <div id="form-administradores" class="form-container">
    <h2>Formulario de Administradores</h2>
    <form method="POST" action="../ADMINISTRADORES/administradores.php">
      <input type="text" name="codigoA" placeholder="Código" >
      <input type="text" name="Nombres" placeholder="Nombres" >
      <input type="text" name="Apellidos" placeholder="Apellidos" >
      <input type="text" name="Usuario" placeholder="Usuario" >
      <input type="password" name="Contrasena" placeholder="Contraseña" >
      <input type="hidden" name="tipo" value="admin">
      <div class="form-actions">
        <button type="submit" name="Ingresar">Insertar</button>
        <button type="submit" name="Modificar">Modificar</button>
        <button type="submit" name="Eliminar">Eliminar</button>
        <button type="submit" name="Buscar">Buscar</button>
      </div>
    </form>
  </div>
</div>

<div class="fab-container" id="fabContainer">
  <div class="fab" id="fab"><i class="fas fa-plus"></i></div>
  <div class="fab-options">
    <a href="#" data-target="clientes"><i class="fas fa-user-plus"></i> Nuevo Cliente</a>
    <a href="#" data-target="clientes-tabla"><i class="fas fa-users"></i> Clientes</a>
    <a href="#" data-target="membresias"><i class="fas fa-id-card"></i> Membresías</a>
    <a href="#" data-target="planes"><i class="fas fa-dumbbell"></i> Planes</a>
    <a href="#" data-target="promociones"><i class="fas fa-tag"></i> Promociones</a>
    <a href="#" data-target="administradores"><i class="fas fa-user-shield"></i> Administradores</a>
    <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a>

  </div>
</div>

<script>
  const fabContainer = document.getElementById('fabContainer');
  const fab = document.getElementById('fab');
  const links = document.querySelectorAll('.fab-options a[data-target]');
  const formularios = document.querySelectorAll('#formularios > div');
  const btnCerrarSesion = document.getElementById('cerrar-sesion');

  fab.addEventListener('click', () => {
    fabContainer.classList.toggle('show');
  });

  links.forEach(link => {
    link.addEventListener('click', (e) => {
      e.preventDefault();
      const target = link.getAttribute('data-target');

      formularios.forEach(f => f.style.display = 'none');

      const seleccionado = document.getElementById('form-' + target);
      if (seleccionado) {
        seleccionado.style.display = 'block';
      }

      fabContainer.classList.remove('show');
    });
  });

  btnCerrarSesion.addEventListener('click', (e) => {
    e.preventDefault();
    window.location.href = '../LOGIN/index.html';
  });
  
  document.addEventListener('DOMContentLoaded', () => {
    formularios.forEach(f => f.style.display = 'none');
    document.getElementById('form-clientes-tabla').style.display = 'block';
  });
</script>

<style>
  #formularios > div {
    display: none;
  }
</style>
</body>
</html>