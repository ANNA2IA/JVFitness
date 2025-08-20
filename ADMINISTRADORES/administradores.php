<?php
$conexion = new mysqli("localhost", "root", "", "JV");

if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}

$codigoA = $_POST['codigoA'] ?? '';
$nombres = $_POST['Nombres'] ?? '';
$apellidos = $_POST['Apellidos'] ?? '';
$usuario = $_POST['Usuario'] ?? '';
$contrasena = $_POST['Contrasena'] ?? '';

// ✅ Ingresar
if (isset($_POST['Ingresar'])) {
    // Verificar si el usuario ya existe
    $verificar = $conexion->prepare("SELECT Usuario FROM Administradores WHERE Usuario = ?");
    $verificar->bind_param("s", $usuario);
    $verificar->execute();
    $verificar->store_result();

    if ($verificar->num_rows > 0) {
        echo "Error: El usuario '$usuario' ya existe.";
    } else {
        $stmt = $conexion->prepare("INSERT INTO Administradores (codigoA, Nombres, Apellidos, Usuario, Contrasenya) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $codigoA, $nombres, $apellidos, $usuario, $contrasena);
        if ($stmt->execute()) {
            echo "Administrador ingresado con éxito.";
        } else {
            echo "Error al ingresar: " . $stmt->error;
        }
        $stmt->close();
    }
    $verificar->close();
}

// ✅ Modificar
if (isset($_POST['Modificar'])) {
    $stmt = $conexion->prepare("UPDATE Administradores SET codigoA=?, Nombres=?, Apellidos=?, Contrasenya=? WHERE Usuario=?");
    $stmt->bind_param("sssss", $codigoA, $nombres, $apellidos, $contrasena, $usuario);
    if ($stmt->execute()) {
        echo "Administrador modificado con éxito.";
    } else {
        echo "Error al modificar: " . $stmt->error;
    }
    $stmt->close();
}

// ✅ Eliminar
if (isset($_POST['Eliminar'])) {
    $stmt = $conexion->prepare("DELETE FROM Administradores WHERE Usuario=?");
    $stmt->bind_param("s", $usuario);
    if ($stmt->execute()) {
        echo "Administrador eliminado con éxito.";
    } else {
        echo "Error al eliminar: " . $stmt->error;
    }
    $stmt->close();
}

// ✅ Buscar
if (isset($_POST['Buscar'])) {
    $stmt = $conexion->prepare("SELECT * FROM Administradores WHERE Usuario=?");
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $resultado = $stmt->get_result();
    if ($fila = $resultado->fetch_assoc()) {
        echo "Código: " . $fila['codigoA'] . "<br>";
        echo "Nombre: " . $fila['Nombres'] . "<br>";
        echo "Apellido: " . $fila['Apellidos'] . "<br>";
        echo "Usuario: " . $fila['Usuario'] . "<br>";
    } else {
        echo "No se encontró el administrador.";
    }
    $stmt->close();
}

$conexion->close();
?>

