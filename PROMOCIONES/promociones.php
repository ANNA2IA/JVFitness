<?php
$conexion = new mysqli("localhost", "root", "admin123", "JV");

if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}

$mensaje = "";
$resultado_promos = $conexion->query("SELECT * FROM Promociones");

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['tipo']) && $_POST['tipo'] == 'promocion') {
    $codigoP     = $_POST['codigoP'] ?? '';
    $nombres     = $_POST['Nombres'] ?? '';
    $precio      = $_POST['Precio'] ?? '';
    $fechaIni    = $_POST['Fecha_Ini'] ?? '';
    $fechaFin    = $_POST['Fecha_Fin'] ?? '';
    $descripcion = $_POST['Descripcion'] ?? '';

    if (isset($_POST['Ingresar'])) {
        $sql = "INSERT INTO Promociones (codigoP, Nombres, Precio, Fecha_Ini, Fecha_Fin, Descripcion)
                VALUES ('$codigoP', '$nombres', '$precio', '$fechaIni', '$fechaFin', '$descripcion')";
        $mensaje = ($conexion->query($sql) === TRUE)
            ? "Promoción insertada correctamente."
            : "Error: " . $conexion->error;
    }

    if (isset($_POST['Modificar'])) {
        $sql = "UPDATE Promociones SET 
                    Nombres='$nombres',
                    Precio='$precio',
                    Fecha_Ini='$fechaIni',
                    Fecha_Fin='$fechaFin',
                    Descripcion='$descripcion'
                WHERE codigoP='$codigoP'";
        $mensaje = ($conexion->query($sql) === TRUE)
            ? "Promoción modificada correctamente."
            : "Error: " . $conexion->error;
    }

    if (isset($_POST['Eliminar'])) {
        $sql = "DELETE FROM Promociones WHERE codigoP='$codigoP'";
        $mensaje = ($conexion->query($sql) === TRUE)
            ? "Promoción eliminada correctamente."
            : "Error: " . $conexion->error;
    }

    if (isset($_POST['Buscar'])) {
        $sql = "SELECT * FROM Promociones WHERE codigoP='$codigoP'";
        $resultado = $conexion->query($sql);
        if ($resultado->num_rows > 0) {
            $row = $resultado->fetch_assoc();
            $mensaje = "🎉 Promociones:<br>
                        Nombre: " . $row['Nombres'] . "<br>
                        Precio: $" . $row['Precio'] . "<br>
                        Fecha Inicio: " . $row['Fecha_Ini'] . "<br>
                        Fecha Fin: " . $row['Fecha_Fin'] . "<br>
                        Descripción: " . $row['Descripcion'];
        } else {
            $mensaje = "No se encontró una promoción con ese código.";
        }
    }

    // Refrescar tabla después de operaciones
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
        <a href="../MENU/index.php" class="btn">VOLVER</a>

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
