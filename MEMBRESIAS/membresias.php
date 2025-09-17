<?php
$conexion = new mysqli("localhost", "root", "admin123", "JV");

if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}

$mensaje = "";
$resultado_membresias = $conexion->query("SELECT * FROM Membresias");

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['tipo']) && $_POST['tipo'] == 'membresia') {
    $codigo      = $_POST['codigo'] ?? '';
    $codigoC     = $_POST['codigoC'] ?? '';
    $codigoP     = $_POST['codigoP'] ?? '';
    $codigoPL    = $_POST['codigoPL'] ?? '';
    $fechaIni    = $_POST['Fecha_Ini'] ?? '';
    $fechaFin    = $_POST['Fecha_Fin'] ?? '';
    $precio      = $_POST['Precio'] ?? '';
    $metodo      = $_POST['metodo'] ?? '';

    if (isset($_POST['Ingresar'])) {
        $sql = "INSERT INTO Membresias (codigo, codigoC, codigoP, codigoPL, Fecha_Ini, Fecha_Fin, Precio, metodo)
                VALUES ('$codigo', '$codigoC', '$codigoP', '$codigoPL', '$fechaIni', '$fechaFin', '$precio', '$metodo')";
        $mensaje = ($conexion->query($sql) === TRUE)
            ? "Membresía insertada correctamente."
            : "Error: " . $conexion->error;
    }

    if (isset($_POST['Modificar'])) {
        $sql = "UPDATE Membresias SET 
                    codigoC='$codigoC',
                    codigoP='$codigoP',
                    codigoPL='$codigoPL',
                    Fecha_Ini='$fechaIni',
                    Fecha_Fin='$fechaFin',
                    Precio='$precio',
                    metodo='$metodo'
                WHERE codigo='$codigo'";
        $mensaje = ($conexion->query($sql) === TRUE)
            ? "Membresía modificada correctamente."
            : "Error: " . $conexion->error;
    }

    if (isset($_POST['Eliminar'])) {
        $sql = "DELETE FROM Membresias WHERE codigo='$codigo'";
        $mensaje = ($conexion->query($sql) === TRUE)
            ? "Membresía eliminada correctamente."
            : "Error: " . $conexion->error;
    }

    if (isset($_POST['Buscar'])) {
        $sql = "SELECT * FROM Membresias WHERE codigo='$codigo'";
        $resultado = $conexion->query($sql);
        if ($resultado->num_rows > 0) {
            $row = $resultado->fetch_assoc();
            $mensaje = "🎉 Membresía encontrada:<br>
                        Cliente: " . $row['codigoC'] . "<br>
                        Promoción: " . $row['codigoP'] . "<br>
                        Plan: " . $row['codigoPL'] . "<br>
                        Fecha Inicio: " . $row['Fecha_Ini'] . "<br>
                        Fecha Fin: " . $row['Fecha_Fin'] . "<br>
                        Precio: $" . $row['Precio'] . "<br>
                        Método de pago: " . $row['metodo'];
        } else {
            $mensaje = "No se encontró una membresía con ese código.";
        }
    }

    // Refrescar tabla después de operaciones
    $resultado_membresias = $conexion->query("SELECT * FROM Membresias");
}

$conexion->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Membresías</title>
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

        <h2>Membresías Registradas</h2>
        <table>
            <tr>
                <th>Código</th>
                <th>Cliente</th>
                <th>Promoción</th>
                <th>Plan</th>
                <th>Fecha Inicio</th>
                <th>Fecha Fin</th>
                <th>Precio</th>
                <th>Método</th>
            </tr>
            <?php if ($resultado_membresias && $resultado_membresias->num_rows > 0): ?>
                <?php while ($membresia = $resultado_membresias->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($membresia['codigo']); ?></td>
                        <td><?php echo htmlspecialchars($membresia['codigoC']); ?></td>
                        <td><?php echo htmlspecialchars($membresia['codigoP']); ?></td>
                        <td><?php echo htmlspecialchars($membresia['codigoPL']); ?></td>
                        <td><?php echo htmlspecialchars($membresia['Fecha_Ini']); ?></td>
                        <td><?php echo htmlspecialchars($membresia['Fecha_Fin']); ?></td>
                        <td>$<?php echo htmlspecialchars($membresia['Precio']); ?></td>
                        <td><?php echo htmlspecialchars($membresia['metodo']); ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="8">No hay membresías registradas.</td></tr>
            <?php endif; ?>
        </table>
    </div>
</body>
</html>
