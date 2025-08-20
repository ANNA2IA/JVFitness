<?php
$conexion = new mysqli("localhost", "root", "admin123", "JV");

// Verificar conexión
if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}

// Verificar que viene del formulario de clientes
if ($_POST['tipo'] == 'cliente') {
    $codigoC   = $_POST['codigoC'] ?? '';
    $nombres   = $_POST['Nombres'] ?? '';
    $apellidos = $_POST['Apellidos'] ?? '';
    $fechaNac  = $_POST['Fecha_Nac'] ?? '';
    $correo    = $_POST['correo'] ?? '';
    $telefono  = $_POST['Telefono'] ?? '';
    $registro  = $_POST['Registro'] ?? '';

    // Insertar
    if (isset($_POST['Ingresar'])) {
        $sql = "INSERT INTO Clientes (codigoC, Nombres, Apellidos, Fecha_Nac, Correo, Telefono, Registro)
                VALUES ('$codigoC', '$nombres', '$apellidos', '$fechaNac', '$correo', '$telefono', '$registro')";
        if ($conexion->query($sql) === TRUE) {
            echo "✅ Cliente insertado correctamente.";
        } else {
            echo "❌ Error: " . $conexion->error;
        }
    }

    // Modificar
    if (isset($_POST['Modificar'])) {
        $sql = "UPDATE Clientes SET 
                    Nombres='$nombres',
                    Apellidos='$apellidos',
                    Fecha_Nac='$fechaNac',
                    Correo='$correo',
                    Telefono='$telefono',
                    Registro='$registro'
                WHERE codigoC='$codigoC'";
        if ($conexion->query($sql) === TRUE) {
            echo "✅ Cliente modificado correctamente.";
        } else {
            echo "❌ Error: " . $conexion->error;
        }
    }

    // Eliminar
    if (isset($_POST['Eliminar'])) {
        $sql = "DELETE FROM Clientes WHERE codigoC='$codigoC'";
        if ($conexion->query($sql) === TRUE) {
            echo "✅ Cliente eliminado correctamente.";
        } else {
            echo "❌ Error: " . $conexion->error;
        }
    }

    // Buscar
    if (isset($_POST['Buscar'])) {
        $sql = "SELECT * FROM Clientes WHERE codigoC='$codigoC'";
        $resultado = $conexion->query($sql);
        if ($resultado->num_rows > 0) {
            $row = $resultado->fetch_assoc();
            echo "👤 Cliente encontrado:<br>";
            echo "Nombre: " . $row['Nombres'] . " " . $row['Apellidos'] . "<br>";
            echo "Correo: " . $row['Correo'] . "<br>";
            echo "Teléfono: " . $row['Telefono'] . "<br>";
        } else {
            echo "⚠️ No se encontró cliente con ese código.";
        }
    }
}

$conexion->close();
?>

