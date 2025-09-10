<?php $contrasena = $_POST['contrasena'] ?? '';
include("../seguridad.php");
    if (!empty($usuario) && !empty($contrasena)) {
        if ($conexion->connect_error) {
            die("Error de conexión: " . $conexion->connect_error);
        }

        $conexion->set_charset("utf8");
    }
        ?>