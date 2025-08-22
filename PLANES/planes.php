<?php
$conexion = new mysqli("localhost", "root", "admin123", "JV");

if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}

$mensaje = "";
$resultado_planes = $conexion->query("SELECT * FROM Planes");

if ($_SERVER["REQUEST_METHOD"] === "POST" && $_POST['tipo'] == 'plan') {
    $codigoPL = $_POST['codigoPL'] ?? '';
    $nombres  = $_POST['Nombres'] ?? '';
    $duracion = $_POST['Duracion'] ?? '';
    $precio   = $_POST['Precio'] ?? '';

    // ===== INSERTAR =====
    if (isset($_POST['Ingresar'])) {
        if ($codigoPL == "" || $nombres == "" || $duracion == "" || $precio == "") {
            $mensaje = "⚠️ Debes llenar todos los campos para insertar un plan.";
        } else {
            $sql = "INSERT INTO Planes (codigoPL, Nombres, Duracion, Precio)
                    VALUES ('$codigoPL', '$nombres', '$duracion', '$precio')";
            $mensaje = ($conexion->query($sql) === TRUE) 
                ? "✅ Plan insertado correctamente." 
                : "❌ Error al insertar: " . $conexion->error;
        }
    }

    // ===== MODIFICAR =====
    if (isset($_POST['Modificar'])) {
        if ($codigoPL == "" || $nombres == "") {
            $mensaje = "⚠️ Debes ingresar al menos el Código y el Nombre para modificar.";
        } else {
            $sql = "UPDATE Planes SET 
                        Nombres='$nombres',
                        Duracion='$duracion',
                        Precio='$precio'
                    WHERE codigoPL='$codigoPL'";
            $mensaje = ($conexion->query($sql) === TRUE) 
                ? "✅ Plan modificado correctamente." 
                : "❌ Error al modificar: " . $conexion->error;
        }
    }

    // ===== ELIMINAR =====
    if (isset($_POST['Eliminar'])) {
        if ($codigoPL == "") {
            $mensaje = "⚠️ Debes ingresar el Código del plan para eliminar.";
        } else {
            $sql = "DELETE FROM Planes WHERE codigoPL='$codigoPL'";
            $mensaje = ($conexion->query($sql) === TRUE) 
                ? "✅ Plan eliminado correctamente." 
                : "❌ Error al eliminar: " . $conexion->error;
        }
    }

    // ===== BUSCAR =====
    if (isset($_POST['Buscar'])) {
        if ($codigoPL == "" && $nombres == "") {
            $mensaje = "⚠️ Ingresa el Código o el Nombre para realizar la búsqueda.";
        } else {
            // Construir la consulta según los campos que estén llenos
            if ($codigoPL != "" && $nombres != "") {
                $sql = "SELECT * FROM Planes WHERE codigoPL='$codigoPL' OR Nombres LIKE '%$nombres%'";
            } elseif ($codigoPL != "") {
                $sql = "SELECT * FROM Planes WHERE codigoPL='$codigoPL'";
            } else { // solo nombre
                $sql = "SELECT * FROM Planes WHERE Nombres LIKE '%$nombres%'";
            }

            $resultado = $conexion->query($sql);
            if ($resultado->num_rows > 0) {
                $row = $resultado->fetch_assoc();
                $mensaje = "🔎 Plan encontrado:<br>
                            Código: " . $row['codigoPL'] . "<br>
                            Nombre: " . $row['Nombres'] . "<br>
                            Duración: " . $row['Duracion'] . "<br>
                            Precio: $" . $row['Precio'];
            } else {
                $mensaje = "❌ No se encontró plan con esos datos.";
            }
        }
    }

    // Refrescar tabla
    $resultado_planes = $conexion->query("SELECT * FROM Planes");
}

$conexion->close();
?>



<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Planes</title>
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

        <h2>Planes Registrados</h2>
        <table>
            <tr>
                <th>Código</th>
                <th>Nombre</th>
                <th>Duración</th>
                <th>Precio</th>
            </tr>
            <?php if ($resultado_planes && $resultado_planes->num_rows > 0): ?>
                <?php while ($plan = $resultado_planes->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($plan['codigoPL']); ?></td>
                        <td><?php echo htmlspecialchars($plan['Nombres']); ?></td>
                        <td><?php echo htmlspecialchars($plan['Duracion']); ?></td>
                        <td>$<?php echo htmlspecialchars($plan['Precio']); ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="4">No hay planes registrados.</td></tr>
            <?php endif; ?>
        </table>
    </div>
</body>
</html>
