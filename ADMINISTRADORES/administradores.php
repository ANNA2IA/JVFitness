<?php
$conexion = new mysqli("localhost", "root", "admin123", "JV");

if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}

$codigoA = $_POST['codigoA'] ?? '';
$nombres = $_POST['Nombres'] ?? '';
$apellidos = $_POST['Apellidos'] ?? '';
$usuario = $_POST['Usuario'] ?? '';
$contrasena = $_POST['Contrasena'] ?? '';

$mensaje = "";

//  Ingresar
if (isset($_POST['Ingresar'])) {
     $mensaje = "Administrador ingresado con éxito.";
    $verificar = $conexion->prepare("SELECT Usuario FROM Administradores WHERE Usuario = ?");
    $verificar->bind_param("s", $usuario);
    $verificar->execute();
    $verificar->store_result();

    if ($verificar->num_rows > 0) {
        $mensaje = "Error: El usuario '$usuario' ya existe.";
    } else {
        $stmt = $conexion->prepare("INSERT INTO Administradores (codigoA, Nombres, Apellidos, Usuario, Contrasenya) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $codigoA, $nombres, $apellidos, $usuario, $contrasena);
        if ($stmt->execute()) {
            $mensaje = "Administrador ingresado con éxito.";
        } else {
            $mensaje = "Error al ingresar: " . $stmt->error;
        }
        $stmt->close();
    }
    $verificar->close();
}

//  Modificar
if (isset($_POST['Modificar'])) {
    $mensaje = "Administrador modificado con éxito.";
    $stmt = $conexion->prepare("UPDATE Administradores SET codigoA=?, Nombres=?, Apellidos=?, Contrasenya=? WHERE Usuario=?");
    $stmt->bind_param("sssss", $codigoA, $nombres, $apellidos, $contrasena, $usuario);
    if ($stmt->execute()) {
        $mensaje = "Administrador modificado con éxito.";
    } else {
        $mensaje = "Error al modificar: " . $stmt->error;
    }
    $stmt->close();
}

//  Eliminar
if (isset($_POST['Eliminar'])) {
     $mensaje = "Administrador eliminado con éxito.";
    $stmt = $conexion->prepare("DELETE FROM Administradores WHERE Usuario=?");
    $stmt->bind_param("s", $usuario);
    if ($stmt->execute()) {
        $mensaje = "Administrador eliminado con éxito.";
    } else {
        $mensaje = "Error al eliminar: " . $stmt->error;
    }
    $stmt->close();
}

//  Buscar
if (isset($_POST['Buscar'])) {
    $stmt = $conexion->prepare("SELECT * FROM Administradores WHERE Usuario=?");
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $resultado = $stmt->get_result();
    if ($fila = $resultado->fetch_assoc()) {
        $mensaje = "Código: " . $fila['codigoA'] . "<br>Nombre: " . $fila['Nombres'] . "<br>Apellido: " . $fila['Apellidos'] . "<br>Usuario: " . $fila['Usuario'];
    } else {
        $mensaje = "No se encontró el administrador.";
    }
    $stmt->close();
}

// Obtener todos los administradores
$resultado_admins = $conexion->query("SELECT * FROM Administradores");

$conexion->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Administradores</title>
       <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&display=swap" rel="stylesheet">
<style>
    * {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
    }

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
        transition: 0.3s ease-in-out;
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
.message + a.btn {
    position: absolute;
    bottom: 40px;
    left: 50%;
    transform: translateX(-50%);
    z-index: 1;
}
.container {
    position: relative;
    padding-bottom: 100px; 
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
    }

    .btn:hover {
        background: #e67e22;
    }

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
        .container {
            padding: 20px;
        }

        table, th, td {
            font-size: 14px;
        }

        .btn {
            width: 100%;
        }
    }
</style>

</head>
<body>
    <div class="container">
        <h1>Resultado</h1>
        <div class="message"><?php echo $mensaje; ?></div>
        <a href="../MENU/index.php" class="btn">VOLVER</a>

        <h2>Administradores Registrados</h2>
        <table>
            <tr>
                <th>Código</th>
                <th>Nombres</th>
                <th>Apellidos</th>
                <th>Usuario</th>
            </tr>
            <?php while ($admin = $resultado_admins->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($admin['codigoA']); ?></td>
                    <td><?php echo htmlspecialchars($admin['Nombres']); ?></td>
                    <td><?php echo htmlspecialchars($admin['Apellidos']); ?></td>
                    <td><?php echo htmlspecialchars($admin['Usuario']); ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>
</body>
</html>



