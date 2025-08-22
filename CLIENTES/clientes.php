<?php
$conexion = new mysqli("localhost", "root", "admin123", "JV");

if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}

$mensaje = "";
$resultado_clientes = $conexion->query("SELECT * FROM Clientes");

if ($_SERVER["REQUEST_METHOD"] === "POST" && $_POST['tipo'] == 'cliente') {
    $codigoC   = $_POST['codigoC'] ?? '';
    $nombres   = $_POST['Nombres'] ?? '';
    $apellidos = $_POST['Apellidos'] ?? '';
    $fechaNac  = $_POST['Fecha_Nac'] ?? '';
    $correo    = $_POST['correo'] ?? '';
    $telefono  = $_POST['Telefono'] ?? '';
    $registro  = $_POST['Registro'] ?? '';

    // ===== INSERTAR =====
    if (isset($_POST['Ingresar'])) {
        if ($codigoC == "" || $nombres == "" || $apellidos == "" || $fechaNac == "" || $correo == "" || $telefono == "" || $registro == "") {
            $mensaje = "⚠️ Debes llenar todos los campos para insertar un cliente.";
        } else {
            $sql = "INSERT INTO Clientes (codigoC, Nombres, Apellidos, Fecha_Nac, Correo, Telefono, Registro)
                    VALUES ('$codigoC', '$nombres', '$apellidos', '$fechaNac', '$correo', '$telefono', '$registro')";
            $mensaje = ($conexion->query($sql) === TRUE) 
                ? "✅ Cliente insertado correctamente." 
                : "❌ Error al insertar: " . $conexion->error;
        }
    }

    // ===== MODIFICAR =====
    if (isset($_POST['Modificar'])) {
        if ($codigoC == "" || $nombres == "") {
            $mensaje = "⚠️ Debes ingresar el Código y los datos a modificar.";
        } else {
            $sql = "UPDATE Clientes SET 
                        Nombres='$nombres',
                        Apellidos='$apellidos',
                        Fecha_Nac='$fechaNac',
                        Correo='$correo',
                        Telefono='$telefono',
                        Registro='$registro'
                    WHERE codigoC='$codigoC'";
            $mensaje = ($conexion->query($sql) === TRUE) 
                ? "✅ Cliente modificado correctamente." 
                : "❌ Error al modificar: " . $conexion->error;
        }
    }

    // ===== ELIMINAR =====
    if (isset($_POST['Eliminar'])) {
        if ($codigoC == "") {
            $mensaje = "⚠️ Debes ingresar el Código para eliminar.";
        } else {
            $sql = "DELETE FROM Clientes WHERE codigoC='$codigoC'";
            $mensaje = ($conexion->query($sql) === TRUE) 
                ? "✅ Cliente eliminado correctamente." 
                : "❌ Error al eliminar: " . $conexion->error;
        }
    }

    // ===== BUSCAR =====
    if (isset($_POST['Buscar'])) {
        if ($codigoC == "" && $nombres == "") {
            $mensaje = "⚠️ Ingresa el Código o el Nombre para realizar la búsqueda.";
        } else {
            $sql = "SELECT * FROM Clientes WHERE codigoC='$codigoC' OR Nombres LIKE '%$nombres%'";
            $resultado = $conexion->query($sql);
            if ($resultado->num_rows > 0) {
                $row = $resultado->fetch_assoc();
                $mensaje = "🔎 Cliente encontrado:<br>
                            Código: " . $row['codigoC'] . "<br>
                            Nombre: " . $row['Nombres'] . " " . $row['Apellidos'] . "<br>
                            Fecha Nac: " . $row['Fecha_Nac'] . "<br>
                            Correo: " . $row['Correo'] . "<br>
                            Teléfono: " . $row['Telefono'] . "<br>
                            Registro: " . $row['Registro'];
            } else {
                $mensaje = "❌ No se encontró cliente con esos datos.";
            }
        }
    }

    // Refrescar tabla
    $resultado_clientes = $conexion->query("SELECT * FROM Clientes");
}

$conexion->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Clientes</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #0f0f0f, #1a1a1a);
            color: #f39c12;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .container {
            background: rgba(30, 30, 30, 0.7);
            backdrop-filter: blur(10px);
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(255, 165, 0, 0.2);
            width: 90%;
            max-width: 900px;
            text-align: center;
            position: relative;
            padding-bottom: 100px;
        }
        h1, h2 {
            color: #f39c12;
            margin-bottom: 20px;
            font-weight: 600;
        }
        .message {
            font-size: 18px;
            margin-bottom: 20px;
            color: #ffd700;
            font-weight: 500;
        }
        .btn {
            background: linear-gradient(145deg, #f39c12, #d35400);
            color: #fff;
            border: none;
            padding: 12px 28px;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            text-decoration: none;
            transition: 0.3s ease;
            position: absolute;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%);
        }
        .btn:hover { background: #e67e22; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 25px;
        }
        th, td {
            padding: 14px 12px;
            border-bottom: 1px solid #f39c12;
            text-align: center;
        }
        th {
            background-color: #2e2e2e;
            color: #f39c12;
            font-weight: 600;
        }
        td {
            background-color: rgba(255, 255, 255, 0.05);
            color: #ecf0f1;
        }
        @media (max-width: 600px) {
            .container { padding: 20px; }
            table, th, td { font-size: 14px; }
            .btn { width: 100%; }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Resultado</h1>
        <div class="message"><?php echo $mensaje; ?></div>
        <a href="../MENU/index.php" class="btn">VOLVER</a>

        <h2>Clientes Registrados</h2>
        <table>
            <tr>
                <th>Código</th>
                <th>Nombres</th>
                <th>Apellidos</th>
                <th>Correo</th>
                <th>Teléfono</th>
            </tr>
            <?php if ($resultado_clientes && $resultado_clientes->num_rows > 0): ?>
                <?php while ($cliente = $resultado_clientes->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($cliente['codigoC']); ?></td>
                        <td><?php echo htmlspecialchars($cliente['Nombres']); ?></td>
                        <td><?php echo htmlspecialchars($cliente['Apellidos']); ?></td>
                        <td><?php echo htmlspecialchars($cliente['Correo']); ?></td>
                        <td><?php echo htmlspecialchars($cliente['Telefono']); ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="5">No hay clientes registrados.</td></tr>
            <?php endif; ?>
        </table>
    </div>
</body>
</html>
