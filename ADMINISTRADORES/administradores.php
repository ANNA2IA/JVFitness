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
       <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #000000;
            color: #f39c12;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }

        .container {
            background-color: #1e1e1e;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 0 20px rgba(255, 165, 0, 0.2);
            width: 90%;
            max-width: 800px;
            text-align: center;
        }

        h1, h2 {
            color: #f39c12;
            margin-bottom: 20px;
        }

        .message {
            font-size: 18px;
            margin-bottom: 20px;
            color: #f39c12;
        }

        .btn {
            background-color: #f39c12;
            color: black;
            border: none;
            padding: 12px 25px;
            margin-bottom: 20px;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }

        .btn:hover {
            background-color: #d35400;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th, td {
            padding: 12px;
            border: 1px solid #f39c12;
            text-align: center;
        }

        th {
            background-color: #2c2c2c;
            color: #f39c12;
        }

        td {
            background-color: #1c1c1c;
            color: white;
        }

        @media (max-width: 600px) {
            .container {
                padding: 15px;
            }

            .btn {
                width: 100%;
            }

            table, th, td {
                font-size: 14px;
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


