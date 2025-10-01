<?php
include("../seguridad.php");

$conexion = new mysqli("localhost", "root", "admin123", "JV");
if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}
$conexion->set_charset("utf8mb4");

$mensaje = "";
$resultado_membresias = $conexion->query("SELECT * FROM Membresias");

if ($_SERVER["REQUEST_METHOD"] === "POST" && ($_POST['tipo'] ?? '') === 'membresia') {
    $codigo = trim($_POST['codigo'] ?? '');
    $Nombre = trim($_POST['nom'] ?? '');
    $precio = trim($_POST['Precio'] ?? '');

    if (isset($_POST['Ingresar'])) {
        if ($codigo === "" || $Nombre === "" || $precio === "") {
            $mensaje = "⚠️ Debes llenar todos los campos para insertar una membresía.";
        } elseif (!is_numeric($precio)) {
            $mensaje = "⚠️ El precio debe ser numérico.";
        } else {
            $stmt = $conexion->prepare("INSERT INTO Membresias (codigo, nom, Precio) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $codigo, $Nombre, $precio);
            $mensaje = ($stmt->execute())
                ? "✅ Membresía insertada correctamente."
                : "❌ Error al insertar: " . $stmt->error;
            $stmt->close();
        }
    } elseif (isset($_POST['Modificar'])) {
        if ($codigo === "") {
            $mensaje = "⚠️ Debes ingresar el Código de membresía para modificar.";
        } else {
            $stmt = $conexion->prepare("UPDATE Membresias SET nom = ?, Precio = ? WHERE codigo = ?");
            $stmt->bind_param("sss", $Nombre, $precio, $codigo);
            $mensaje = ($stmt->execute())
                ? "✅ Membresía modificada correctamente."
                : "❌ Error al modificar: " . $stmt->error;
            $stmt->close();
        }
    } elseif (isset($_POST['Eliminar'])) {
        if ($codigo === "" && $Nombre === "") {
            $mensaje = "⚠️ Debes ingresar el Código o el Nombre para eliminar.";
        } else {
            if (!empty($codigo)) {
                $stmt = $conexion->prepare("DELETE FROM Membresias WHERE codigo = ?");
                $stmt->bind_param("s", $codigo);
            } else {
                $stmt = $conexion->prepare("DELETE FROM Membresias WHERE nom = ?");
                $stmt->bind_param("s", $Nombre);
            }
            $mensaje = ($stmt->execute())
                ? "✅ Membresía eliminada correctamente."
                : "❌ Error al eliminar: " . $stmt->error;
            $stmt->close();
        }
    } elseif (isset($_POST['Buscar'])) {
        if ($codigo === "" && $Nombre === "") {
            $mensaje = "⚠️ Ingresa el Código o el Nombre para buscar la membresía.";
        } else {
            if (!empty($codigo)) {
                $stmt = $conexion->prepare("SELECT * FROM Membresias WHERE codigo = ?");
                $stmt->bind_param("s", $codigo);
            } else {
                $stmt = $conexion->prepare("SELECT * FROM Membresias WHERE nom LIKE ?");
                $busqueda_nombre = "%$Nombre%";
                $stmt->bind_param("s", $busqueda_nombre);
            }
            
            $stmt->execute();
            $resultado_b = $stmt->get_result();
            if ($row = $resultado_b->fetch_assoc()) {
                $mensaje = "🎉 Membresía encontrada:<br>
                            Código: " . htmlspecialchars($row['codigo']) . "<br>
                            Membresía: " . htmlspecialchars($row['nom']) . "<br>
                            Precio: $" . htmlspecialchars($row['Precio']) . "<br>";
            } else {
                $mensaje = "❌ No se encontró una membresía con esos datos.";
            }
            $stmt->close();
        }
    }

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
        <a onclick="history.back()" class="btn">VOLVER</a>

        <h2>Membresías Registradas</h2>
        <table>
            <tr>
                <th>Código</th>
                <th>Nombre</th>
                <th>Precio</th>
            </tr>
            <?php if ($resultado_membresias && $resultado_membresias->num_rows > 0): ?>
                <?php while ($m = $resultado_membresias->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($m['codigo']); ?></td>
                        <td><?php echo htmlspecialchars($m['nom']); ?></td>
                        <td>$<?php echo htmlspecialchars($m['Precio']); ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="3">No hay membresías registradas.</td></tr>
            <?php endif; ?>
        </table>
    </div>
</body>
</html>