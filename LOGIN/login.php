<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>
</head>
<body>
        <?php
//conexion a la base de datos de mysql
$servidor = "localhost";
$usuario = "root";
$clave = "admin123";
$bd = "JV";

$conexion = new mysqli($servidor, $usuario, $clave, $bd);
// Verificar la conexión
if ($conexion->connect_error)
{
    die("Error de conexión: " . $conexion->connect_error);
}
else
{
    echo "Conexión exitosa a la base de datos."; 
}

$conexion->set_charset("utf8"); 

?>
</body>
</html>