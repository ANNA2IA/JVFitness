<?php
$conexion = new mysqli("localhost", "root", "admin123", "JV");

// Verificar conexión
if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}

if (isset($_POST['tipo']) && $_POST['tipo'] == 'plan') {
    $codigoPL = $_POST['codigoPL'] ?? '';
    $nombres  = $_POST['Nombres'] ?? '';
    $duracion = $_POST['Duracion'] ?? '';
    $precio   = $_POST['Precio'] ?? '';

    // Insertar
    if (isset($_POST['Ingresar'])) {
        $sql = "INSERT INTO Planes (codigoPL, Nombres, Duracion, Precio)
                VALUES ('$codigoPL', '$nombres', '$duracion', '$precio')";
        if ($conexion->query($sql) === TRUE) {
            echo "✅ Plan insertado correctamente.";
        } else {
            echo "❌ Error: " . $conexion->error;
        }
    }

    // Modificar
    if (isset($_POST['Modificar'])) {
        $sql = "UPDATE Planes SET 
                    Nombres='$nombres',
                    Duracion='$duracion',
                    Precio='$precio'
                WHERE codigoPL='$codigoPL'";
        if ($conexion->query($sql) === TRUE) {
            echo "✅ Plan modificado correctamente.";
        } else {
            echo "❌ Error: " . $conexion->error;
        }
    }

    // Eliminar
    if (isset($_POST['Eliminar'])) {
        $sql = "DELETE FROM Planes WHERE codigoPL='$codigoPL'";
        if ($conexion->query($sql) === TRUE) {
            echo "✅ Plan eliminado correctamente.";
        } else {
            echo "❌ Error: " . $conexion->error;
        }
    }

    // Buscar
    if (isset($_POST['Buscar'])) {
        $sql = "SELECT * FROM Planes WHERE codigoPL='$codigoPL'";
        $resultado = $conexion->query($sql);
        if ($resultado->num_rows > 0) {
            $row = $resultado->fetch_assoc();
            echo "📋 Plan encontrado:<br>";
            echo "Nombre: " . $row['Nombres'] . "<br>";
            echo "Duración: " . $row['Duracion'] . "<br>";
            echo "Precio: $" . $row['Precio'] . "<br>";
        } else {
            echo "⚠️ No se encontró un plan con ese código.";
        }
    }
}

$conexion->close();
?>
