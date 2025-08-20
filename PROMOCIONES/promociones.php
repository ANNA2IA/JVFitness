<?php
$conexion = new mysqli("localhost", "root", "admin123", "JV");

// Verificar conexión
if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}

if (isset($_POST['tipo']) && $_POST['tipo'] == 'promocion') {
    $codigoP    = $_POST['codigoP'] ?? '';
    $nombres    = $_POST['Nombres'] ?? '';
    $precio     = $_POST['Precio'] ?? '';
    $fechaIni   = $_POST['Fecha_Ini'] ?? '';
    $fechaFin   = $_POST['Fecha_Fin'] ?? '';
    $descripcion= $_POST['Descripcion'] ?? '';

    // Insertar
    if (isset($_POST['Ingresar'])) {
        $sql = "INSERT INTO Promociones (codigoP, Nombres, Precio, Fecha_Ini, Fecha_Fin, Descripcion)
                VALUES ('$codigoP', '$nombres', '$precio', '$fechaIni', '$fechaFin', '$descripcion')";
        if ($conexion->query($sql) === TRUE) {
            echo "✅ Promoción insertada correctamente.";
        } else {
            echo "❌ Error: " . $conexion->error;
        }
    }

    // Modificar
    if (isset($_POST['Modificar'])) {
        $sql = "UPDATE Promociones SET 
                    Nombres='$nombres',
                    Precio='$precio',
                    Fecha_Ini='$fechaIni',
                    Fecha_Fin='$fechaFin',
                    Descripcion='$descripcion'
                WHERE codigoP='$codigoP'";
        if ($conexion->query($sql) === TRUE) {
            echo "✅ Promoción modificada correctamente.";
        } else {
            echo "❌ Error: " . $conexion->error;
        }
    }

    // Eliminar
    if (isset($_POST['Eliminar'])) {
        $sql = "DELETE FROM Promociones WHERE codigoP='$codigoP'";
        if ($conexion->query($sql) === TRUE) {
            echo "✅ Promoción eliminada correctamente.";
        } else {
            echo "❌ Error: " . $conexion->error;
        }
    }

    // Buscar
    if (isset($_POST['Buscar'])) {
        $sql = "SELECT * FROM Promociones WHERE codigoP='$codigoP'";
        $resultado = $conexion->query($sql);
        if ($resultado->num_rows > 0) {
            $row = $resultado->fetch_assoc();
            echo "🎉 Promoción encontrada:<br>";
            echo "Nombre: " . $row['Nombres'] . "<br>";
            echo "Precio: $" . $row['Precio'] . "<br>";
            echo "Fecha Inicio: " . $row['Fecha_Ini'] . "<br>";
            echo "Fecha Fin: " . $row['Fecha_Fin'] . "<br>";
            echo "Descripción: " . $row['Descripcion'] . "<br>";
        } else {
            echo "⚠️ No se encontró una promoción con ese código.";
        }
    }
}

$conexion->close();
?>
