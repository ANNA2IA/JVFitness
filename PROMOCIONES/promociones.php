<?php
$conexion = new mysqli("localhost", "root", "admin123", "JV");
if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}
$conexion->set_charset("utf8mb4");

$mensaje = "";
$resultado_promos = $conexion->query("SELECT * FROM Promociones");

if ($_SERVER["REQUEST_METHOD"] === "POST" && ($_POST['tipo'] ?? '') === 'promocion') {
    $codigoP     = $_POST['codigoP'] ?? '';
    $nombres     = $_POST['Nombres'] ?? '';
    $precio      = $_POST['Precio'] ?? '';
    $fechaIni    = $_POST['Fecha_Ini'] ?? '';
    $fechaFin    = $_POST['Fecha_Fin'] ?? '';
    $descripcion = $_POST['Descripcion'] ?? '';

    if (isset($_POST['Ingresar'])) {
        $stmt = $conexion->prepare("
            INSERT INTO Promociones (codigoP, Nombres, Precio, Fecha_Ini, Fecha_Fin, Descripcion)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("ssssss", $codigoP, $nombres, $precio, $fechaIni, $fechaFin, $descripcion);
        $mensaje = ($stmt->execute())
            ? "✅ Promoción insertada correctamente."
            : "❌ Error al insertar: " . $stmt->error;
        $stmt->close();
    }

    if (isset($_POST['Modificar'])) {
        $stmt = $conexion->prepare("
            UPDATE Promociones SET 
                Nombres = ?, Precio = ?, Fecha_Ini = ?, Fecha_Fin = ?, Descripcion = ?
            WHERE codigoP = ?
        ");
        $stmt->bind_param("ssssss", $nombres, $precio, $fechaIni, $fechaFin, $descripcion, $codigoP);
        $mensaje = ($stmt->execute())
            ? "✅ Promoción modificada correctamente."
            : "❌ Error al modificar: " . $stmt->error;
        $stmt->close();
    }

    if (isset($_POST['Eliminar'])) {
        if ($codigoP == "" && $nombres == "") {
            $mensaje = "⚠️ Debes ingresar el Código o el Nombre para eliminar.";
        } else {
            if (!empty($codigoP)) {
                $stmt = $conexion->prepare("DELETE FROM Promociones WHERE codigoP = ?");
                $stmt->bind_param("s", $codigoP);
            } else {
                $stmt = $conexion->prepare("DELETE FROM Promociones WHERE Nombres = ?");
                $stmt->bind_param("s", $nombres);
            }
            $mensaje = ($stmt->execute())
                ? "✅ Promoción eliminada correctamente."
                : "❌ Error al eliminar: " . $stmt->error;
            $stmt->close();
        }
    }

    if (isset($_POST['Buscar'])) {
        if ($codigoP == "" && $nombres == "") {
            $mensaje = "⚠️ Debes ingresar el Código o el Nombre para buscar.";
        } else {
            if (!empty($codigoP)) {
                $stmt = $conexion->prepare("SELECT * FROM Promociones WHERE codigoP = ?");
                $stmt->bind_param("s", $codigoP);
            } else {
                $stmt = $conexion->prepare("SELECT * FROM Promociones WHERE Nombres LIKE ?");
                $busqueda_nombre = "%$nombres%";
                $stmt->bind_param("s", $busqueda_nombre);
            }
            
            $stmt->execute();
            $resultado = $stmt->get_result();
            if ($row = $resultado->fetch_assoc()) {
                $mensaje = "🎉 Promoción encontrada:<br>
                            Código: " . htmlspecialchars($row['codigoP']) . "<br>
                            Nombre: " . htmlspecialchars($row['Nombres']) . "<br>
                            Precio: $" . htmlspecialchars($row['Precio']) . "<br>
                            Fecha Inicio: " . htmlspecialchars($row['Fecha_Ini']) . "<br>
                            Fecha Fin: " . htmlspecialchars($row['Fecha_Fin']) . "<br>
                            Descripción: " . htmlspecialchars($row['Descripcion']);
            } else {
                $mensaje = "❌ No se encontró una promoción con esos datos.";
            }
            $stmt->close();
        }
    }

    $resultado_promos = $conexion->query("SELECT * FROM Promociones");
}

$conexion->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Promociones</title>
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
            max-width: 1000px;
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
        <a onclick="history.back()" class="btn">VOLVER</a>

        <h2>Promociones Registradas</h2>
        <table>
            <tr>
                <th>Código</th>
                <th>Nombre</th>
                <th>Precio</th>
                <th>Fecha Inicio</th>
                <th>Fecha Fin</th>
                <th>Descripción</th>
            </tr>
            <?php if ($resultado_promos && $resultado_promos->num_rows > 0): ?>
                <?php while ($promo = $resultado_promos->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($promo['codigoP']); ?></td>
                        <td><?php echo htmlspecialchars($promo['Nombres']); ?></td>
                        <td>$<?php echo htmlspecialchars($promo['Precio']); ?></td>
                        <td><?php echo htmlspecialchars($promo['Fecha_Ini']); ?></td>
                        <td><?php echo htmlspecialchars($promo['Fecha_Fin']); ?></td>
                        <td><?php echo htmlspecialchars($promo['Descripcion']); ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="6">No hay promociones registradas.</td></tr>
            <?php endif; ?>
        </table>
    </div>
</body>
</html>