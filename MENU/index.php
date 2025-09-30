<?php
include("../seguridad.php");

$servername = "localhost";
$username = "root";
$password = "admin123";
$dbname = "JV";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");

// Consulta para obtener clientes junto con sus membresías
$sql = "SELECT 
           c.Nombres AS NombreCliente,
           c.Telefono,
           m.nom AS NombreMembresia,
           m.Fecha_Fin
        FROM Clientes c
        LEFT JOIN Membresias m ON c.membresia = m.codigo
        ORDER BY m.Fecha_Fin ASC";

$result = $conn->query($sql);

$clientes = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $fecha_vencimiento = $row['Fecha_Fin'];
        $status = 'Sin membresía';
        $color = 'status-sin-membresia';

        if ($fecha_vencimiento && $fecha_vencimiento !== '0000-00-00') {
            $hoy = new DateTime();
            $vencimiento = new DateTime($fecha_vencimiento);
            $interval = $hoy->diff($vencimiento);
            $dias_restantes = (int)$interval->format('%r%a');

            if ($dias_restantes < 0) {
                $status = 'Vencida';
                $color = 'status-vencida';
            } elseif ($dias_restantes <= 7) {
                $status = 'Por vencer';
                $color = 'status-por-vencer';
            } else {
                $status = 'Activa';
                $color = 'status-activa';
            }
        }

        $clientes[] = [
            'nombre' => $row['NombreCliente'],
            'telefono' => $row['Telefono'],
            'membresia' => $row['NombreMembresia'] ?? 'N/A',
            'vencimiento' => $fecha_vencimiento ?? 'N/A',
            'status' => $status,
            'color' => $color
        ];
    }
}

// Obtener todas las membresías para el select
$sql_membresias = "SELECT codigo, nom, Precio FROM Membresias";
$resultado_membresias = $conn->query($sql_membresias);

// Obtener TODAS las promociones para el select (se validarán por JavaScript)
$sql_promociones = "SELECT codigoP, Nombres, Precio, Fecha_Ini, Fecha_Fin FROM Promociones ORDER BY Fecha_Ini DESC";
$resultado_promociones = $conn->query($sql_promociones);

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <title>Menú Principal</title>
  <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Poppins:wght@300;400;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
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
      padding: 40px;
    }
    .hero-section {
      text-align: center;
      margin-bottom: 30px;
    }
    
    .hero-section h1 {
      font-family: 'Bebas Neue', cursive;
      font-size: 5rem;
      letter-spacing: 3px;
      text-transform: uppercase;
      color: var(--orange);
      text-shadow: 3px 3px 6px var(--shadow-color), 0 0 20px rgba(255, 102, 0, 0.5);
      margin: 0;
      line-height: 1;
      background: linear-gradient(45deg, #ff6600, #ffaa00, #ff6600);
      background-size: 200% 200%;
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
      animation: gradientShift 3s ease-in-out infinite;
    }
    
    .subtitle {
      font-size: 1.5rem;
      color: var(--text-color);
      margin-top: 10px;
      font-weight: 300;
      text-shadow: 1px 1px 3px var(--shadow-color);
    }
    
    @keyframes gradientShift {
      0%, 100% { background-position: 0% 50%; }
      50% { background-position: 100% 50%; }
    }
    
    #clientes-status-section {
      margin-bottom: 20px;
    }
    
    #clientes-status-section h2 {
      color: var(--orange);
      font-size: 2rem;
      text-shadow: 2px 2px 4px var(--shadow-color);
      text-align: center;
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
    select:disabled {
      background: #222;
      color: #666;
      border-color: #444;
      cursor: not-allowed;
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
      background-color: #1e1e1e;
      box-shadow: 0 0 10px rgba(0,0,0,0.4);
    }
    th, td {
      padding: 12px;
      text-align: left;
      border-bottom: 1px solid #333;
    }
    th {
      background-color: #2a2a2a;
      color: var(--orange);
      font-weight: bold;
    }
    tr:hover {
      background-color: #333;
    }
    .status-vencida {
      color: #ff4444;
      font-weight: bold;
    }
    .status-por-vencer {
      color: #ffbb33;
      font-weight: bold;
    }
    .status-activa {
      color: #00ff99;
      font-weight: bold;
    }
    .status-sin-membresia {
      color: #999;
      font-style: italic;
    }
    .price-calculator {
      background: rgba(0, 0, 0, 0.5);
      padding: 15px;
      border-radius: 8px;
      margin-top: 15px;
      border: 1px solid #555;
    }
    .price-details {
      color: var(--orange);
      font-weight: bold;
      margin: 5px 0;
    }
    .discount-info {
      color: #4CAF50;
      font-weight: bold;
    }
    /* Ocultar todos los formularios por defecto */
    #formularios > div {
      display: none;
    }
  </style>
</head>
<body>
  <div class="hero-section">
    <h1>🏋️‍♂️ GIMNASIO JV CENTER 🏋️‍♂️</h1>
  </div>
  
  <div id="clientes-status-section">
    <h2>📊 Clientes y Estatus de Membresía</h2>
  </div>

  <div id="formularios">
    <!-- Tabla de clientes -->
    <div id="form-clientes-tabla" class="table-container">
      <table>
        <thead>
          <tr>
            <th>Nombre</th>
            <th>Teléfono</th>
            <th>Membresía</th>
            <th>Vencimiento</th>
            <th>Estatus</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($clientes as $cliente): ?>
            <tr>
              <td><?= htmlspecialchars($cliente['nombre']) ?></td>
              <td><?= htmlspecialchars($cliente['telefono']) ?></td>
              <td><?= htmlspecialchars($cliente['membresia']) ?></td>
              <td><?= htmlspecialchars($cliente['vencimiento']) ?></td>
              <td class="<?= $cliente['color'] ?>"><?= $cliente['status'] ?></td>
            </tr>
          <?php endforeach; ?>
          <?php if (empty($clientes)): ?>
            <tr><td colspan="5">No hay clientes registrados.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <!-- Formulario Clientes -->
    <div id="form-clientes" class="form-container"> 
      <h2>Formulario de Clientes</h2> 
      <form method="POST" action="../CLIENTES/clientes.php">
        <input type="text" name="codigoC" placeholder="Código" >
        <input type="text" name="Nombres" placeholder="Nombres" >
        <input type="text" name="Apellidos" placeholder="Apellidos" > 
        
        <!-- Select de Membresías -->
        <select name="codigoM" id="membresiaSelect" onchange="calcularPrecio()">
          <option value="">-- Selecciona una Membresía --</option> 
          <?php if ($resultado_membresias && $resultado_membresias->num_rows > 0): ?>
            <?php while ($membresia = $resultado_membresias->fetch_assoc()): ?>
              <option value="<?php echo htmlspecialchars($membresia['codigo']); ?>" 
                      data-precio="<?php echo htmlspecialchars($membresia['Precio']); ?>">
                <?php echo htmlspecialchars($membresia['nom']) . " - $" . htmlspecialchars($membresia['Precio']); ?>
              </option>
            <?php endwhile; ?>
          <?php else: ?>
            <option disabled>No hay membresías registradas</option>
          <?php endif; ?>
        </select>

        <!-- Select de Promociones -->
        <select name="codigoP" id="promocionSelect" onchange="calcularPrecio()">
          <option value="">-- Selecciona una Promoción --</option>
          <?php if ($resultado_promociones && $resultado_promociones->num_rows > 0): ?>
            <?php while ($promo = $resultado_promociones->fetch_assoc()): ?>
              <option value="<?php echo htmlspecialchars($promo['codigoP']); ?>" 
                      data-precio="<?php echo htmlspecialchars($promo['Precio']); ?>"
                      data-inicio="<?php echo htmlspecialchars($promo['Fecha_Ini']); ?>"
                      data-fin="<?php echo htmlspecialchars($promo['Fecha_Fin']); ?>">
                <?php echo htmlspecialchars($promo['Nombres']) . " - $" . htmlspecialchars($promo['Precio']) . " (" . htmlspecialchars($promo['Fecha_Ini']) . " al " . htmlspecialchars($promo['Fecha_Fin']) . ")"; ?>
              </option>
            <?php endwhile; ?>
          <?php else: ?>
            <option disabled>No hay promociones registradas</option>
          <?php endif; ?>
        </select>

        <!-- Calculadora de precios -->
        <div id="priceCalculator" class="price-calculator" style="display: none;">
          <div class="price-details" id="precioOriginal"></div>
          <div class="discount-info" id="descuentoInfo"></div>
          <div class="price-details" id="precioFinal"></div>
        </div>

        <input type="email" name="correo" placeholder="Correo" >
        <input type="text" name="Telefono" placeholder="Teléfono" >
        <input type="date" name="Registro" placeholder="Fecha de Pago" > 
        <input type="hidden" name="tipo" value="cliente">
        <div class="form-actions">
          <button type="submit" name="Ingresar">Insertar</button> 
          <button type="submit" name="Modificar">Modificar</button>
          <button type="submit" name="Eliminar">Eliminar</button> 
          <button type="submit" name="Buscar">Buscar</button> 
        </div> 
      </form> 
    </div>

    <!-- Formulario Membresías -->
    <div id="form-membresias" class="form-container"> 
      <h2>Formulario de Membresías</h2> 
      <form method="POST" action="../MEMBRESIAS/membresias.php"> 
        <input type="text" name="codigo" placeholder="Código de Membresía" > 
        <input type="text" name="nom" placeholder="Nombre de Membresía" >
        <input type="text" name="Precio" placeholder="Precio" > 
        <input type="hidden" name="tipo" value="membresia">
        <div class="form-actions">
          <button type="submit" name="Ingresar">Insertar</button>
          <button type="submit" name="Modificar">Modificar</button> 
          <button type="submit" name="Eliminar">Eliminar</button>
          <button type="submit" name="Buscar">Buscar</button> 
        </div>
      </form> 
    </div>

    <!-- Formulario Promociones -->
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

    <!-- Formulario Administradores -->
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

  <!-- Menú flotante FAB -->
  <div class="fab-container" id="fabContainer">
    <div class="fab" id="fab"><i class="fas fa-plus"></i></div>
    <div class="fab-options">
      <a href="#" data-target="clientes"><i class="fas fa-user-plus"></i> Nuevo Cliente</a>
      <a href="#" data-target="clientes-tabla"><i class="fas fa-users"></i> Clientes</a>
      <a href="#" data-target="membresias"><i class="fas fa-id-card"></i> Membresías</a>
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

    fab.addEventListener('click', () => {
      fabContainer.classList.toggle('show');
    });

    links.forEach(link => {
      link.addEventListener('click', (e) => {
        e.preventDefault();
        const target = link.getAttribute('data-target');
        formularios.forEach(f => f.style.display = 'none');
        
        const clientesStatusSection = document.getElementById('clientes-status-section');
        if (target === 'clientes-tabla') {
          clientesStatusSection.style.display = 'block';
        } else {
          clientesStatusSection.style.display = 'none';
        }

        const seleccionado = document.getElementById('form-' + target);
        if (seleccionado) {
          seleccionado.style.display = 'block';
        }
        fabContainer.classList.remove('show');
      });
    });

    document.addEventListener('DOMContentLoaded', () => {
      formularios.forEach(f => f.style.display = 'none');
      document.getElementById('form-clientes-tabla').style.display = 'block';
      document.getElementById('clientes-status-section').style.display = 'block';
      validarPromociones();
      agregarValidaciones();
    });

    function agregarValidaciones() {
      const camposTexto = document.querySelectorAll('input[name="Nombres"], input[name="Apellidos"]');
      camposTexto.forEach(campo => {
        campo.addEventListener('input', function() {
          this.value = this.value.replace(/[^a-záéíóúüñA-ZÁÉÍÓÚÜÑ\s]/g, '');
        });
      });
const campoTelefono = document.querySelector('input[name="Telefono"]');
if (campoTelefono) {
  campoTelefono.addEventListener('input', function () {
    this.value = this.value.replace(/[^0-9]/g, '');
  });
}

      const camposPrecio = document.querySelectorAll('input[name="Precio"]');
      camposPrecio.forEach(campo => {
        campo.addEventListener('input', function() {
          this.value = this.value.replace(/[^0-9.]/g, '');
          const partes = this.value.split('.');
          if (partes.length > 2) {
            this.value = partes[0] + '.' + partes.slice(1).join('');
          }
        });
      });
      const campoNombreMembresia = document.querySelector('input[name="nom"]');
if (campoNombreMembresia) {
  campoNombreMembresia.addEventListener('input', function () {
    this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]/g, '');
  });
}
    }

    function validarPromociones() {
      const hoy = new Date().toISOString().split('T')[0];
      const promocionSelect = document.getElementById('promocionSelect');
      
      if (promocionSelect) {
        const opciones = promocionSelect.querySelectorAll('option[data-inicio]');
        opciones.forEach(opcion => {
          const fechaInicio = opcion.getAttribute('data-inicio');
          const fechaFin = opcion.getAttribute('data-fin');
          
          if (fechaInicio && fechaFin) {
            if (hoy >= fechaInicio && hoy <= fechaFin) {
              opcion.disabled = false;
              opcion.style.color = '#00ff99';
            } else {
              opcion.disabled = true;
              opcion.style.color = '#666';
              opcion.textContent += ' [No disponible]';
            }
          }
        });
      }
    }

    function calcularPrecio() {
      const membresiaSelect = document.getElementById('membresiaSelect');
      const promocionSelect = document.getElementById('promocionSelect');
      const calculator = document.getElementById('priceCalculator');
      const precioOriginal = document.getElementById('precioOriginal');
      const descuentoInfo = document.getElementById('descuentoInfo');
      const precioFinal = document.getElementById('precioFinal');

      const membresiaSeleccionada = membresiaSelect.options[membresiaSelect.selectedIndex];
      const promocionSeleccionada = promocionSelect.options[promocionSelect.selectedIndex];

      if (membresiaSeleccionada.value) {
        const precioMembresia = parseFloat(membresiaSeleccionada.getAttribute('data-precio') || 0);
        let descuento = 0;
        let nombrePromocion = '';

        if (promocionSeleccionada.value && !promocionSeleccionada.disabled) {
          descuento = parseFloat(promocionSeleccionada.getAttribute('data-precio') || 0);
          nombrePromocion = promocionSeleccionada.text.split(' - ')[0];
        }

        const total = Math.max(0, precioMembresia - descuento);

        precioOriginal.textContent = `Precio de Membresía: $${precioMembresia.toFixed(2)}`;
        
        if (descuento > 0) {
          descuentoInfo.innerHTML = `Descuento aplicado (${nombrePromocion}): -$${descuento.toFixed(2)}`;
          precioFinal.textContent = `Total a pagar: $${total.toFixed(2)}`;
        } else {
          descuentoInfo.textContent = '';
          precioFinal.textContent = `Total a pagar: $${total.toFixed(2)}`;
        }

        calculator.style.display = 'block';
      } else {
        calculator.style.display = 'none';
      }
    }

    window.addEventListener('load', validarPromociones);
  </script>
</body>
</html>