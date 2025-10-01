<?php

// Habilitar errores para ver problemas en local
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Requerir PHPMailer
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';
require '../PHPMailer/src/Exception.php';

$conexion = new mysqli("localhost", "root", "admin123", "JV");
if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}

$mensaje = "";
$resultado_clientes = $conexion->query("SELECT c.*, m.nom as nombre_membresia, m.Precio as precio_membresia, 
                                               p.Nombres as nombre_promocion, p.Precio as precio_promocion 
                                        FROM Clientes c 
                                        LEFT JOIN Membresias m ON c.membresia = m.codigo 
                                        LEFT JOIN Promociones p ON c.promocion = p.codigoP");

if ($_SERVER["REQUEST_METHOD"] === "POST" && $_POST['tipo'] == 'cliente') {
    $codigoC   = $_POST['codigoC'] ?? '';
    $nombres   = $_POST['Nombres'] ?? '';
    $apellidos = $_POST['Apellidos'] ?? '';
    $correo    = $_POST['correo'] ?? '';
    $telefono  = $_POST['Telefono'] ?? '';
    $registro  = $_POST['Registro'] ?? '';
    $codigoM   = $_POST['codigoM'] ?? '';  // Membresía seleccionada
    $codigoP   = $_POST['codigoP'] ?? '';  // Promoción seleccionada

    // ===== INSERTAR =====
    if (isset($_POST['Ingresar'])) {
        if ($codigoC == "" || $nombres == "" || $apellidos == "" || $correo == "" || $telefono == "" || $registro == "") {
            $mensaje = "⚠️ Debes llenar todos los campos para insertar un cliente.";
        } else {
            // Obtener datos de la membresía seleccionada
            $precio_membresia = 0;
            $fecha_fin_membresia = null;
            $nombre_membresia = '';
            $es_mensual = false;
            
            // Calcular fecha fin según el tipo de membresía
            if (!empty($codigoM)) {
                $stmt_mem = $conexion->prepare("SELECT Precio, nom FROM Membresias WHERE codigo = ?");
                $stmt_mem->bind_param("s", $codigoM);
                $stmt_mem->execute();
                $resultado_mem = $stmt_mem->get_result();
                if ($row_mem = $resultado_mem->fetch_assoc()) {
                    $precio_membresia = floatval($row_mem['Precio']);
                    $nombre_membresia = strtolower($row_mem['nom']);
                    $es_mensual = stripos($nombre_membresia, 'mensualidad') !== false;

                    // Calcular días según el nombre de la membresía
                    if (strpos($nombre_membresia, "dia") !== false) {
                        $fecha_fin_membresia = date('Y-m-d', strtotime($registro . ' +1 day'));
                    } elseif (strpos($nombre_membresia, "semana") !== false) {
                        $fecha_fin_membresia = date('Y-m-d', strtotime($registro . ' +7 days'));
                    } elseif (strpos($nombre_membresia, "quincenal") !== false) {
                        $fecha_fin_membresia = date('Y-m-d', strtotime($registro . ' +15 days'));
                    } elseif (strpos($nombre_membresia, "mes") !== false || strpos($nombre_membresia, "mensual") !== false) {
                        $fecha_fin_membresia = date('Y-m-d', strtotime($registro . ' +30 days'));
                    } elseif (strpos($nombre_membresia, "año") !== false || strpos($nombre_membresia, "anual") !== false) {
                        $fecha_fin_membresia = date('Y-m-d', strtotime($registro . ' +365 days'));
                    } else {
                        $fecha_fin_membresia = date('Y-m-d', strtotime($registro . ' +30 days'));
                    }
                }
                $stmt_mem->close();
            }

            // Obtener datos de la promoción seleccionada (solo si es membresía mensual)
            $descuento_promocion = 0;
            $nombre_promocion = '';
            
            if (!empty($codigoP) && $es_mensual) {
                $stmt_prom = $conexion->prepare("SELECT Precio, Nombres FROM Promociones WHERE codigoP = ?");
                $stmt_prom->bind_param("s", $codigoP);
                $stmt_prom->execute();
                $resultado_prom = $stmt_prom->get_result();
                if ($row_prom = $resultado_prom->fetch_assoc()) {
                    $descuento_promocion = floatval($row_prom['Precio']);
                    $nombre_promocion = $row_prom['Nombres'];
                }
                $stmt_prom->close();
            } elseif (!empty($codigoP) && !$es_mensual) {
                $codigoP = '';
            }

            // Calcular precio final
            $precio_final = $precio_membresia - $descuento_promocion;
            $precio_final = max(0, $precio_final);
             
            // Insertar cliente con Fecha_Fin
            $stmt = $conexion->prepare("INSERT INTO Clientes 
                (codigoC, Nombres, Apellidos, Correo, Telefono, Registro, membresia, promocion, Fecha_Fin) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssssssss", 
                $codigoC, 
                $nombres, 
                $apellidos, 
                $correo, 
                $telefono, 
                $registro, 
                $codigoM, 
                $codigoP, 
                $fecha_fin_membresia
            );
            
            if ($stmt->execute()) {
                $mensaje_detalle = "";
                if ($descuento_promocion > 0 && $es_mensual) {
                    $mensaje_detalle = " | Precio original: $" . number_format($precio_membresia, 2) . 
                                     " | Descuento aplicado (" . $nombre_promocion . "): $" . number_format($descuento_promocion, 2) . 
                                     " | Total con descuento: $" . number_format($precio_final, 2);
                } elseif (!empty($codigoP) && !$es_mensual) {
                    $mensaje_detalle = " | NOTA: Promoción no aplicada (solo válida para membresías mensuales)";
                }
                
                $mensaje = "✅ Cliente insertado correctamente." . $mensaje_detalle;

                // Enviar recibo por correo
                enviarRecibo($correo, $nombres, $apellidos, $registro, $precio_membresia, $descuento_promocion, $precio_final, $nombre_promocion);
            } else {
                $mensaje = "❌ Error al insertar: " . $stmt->error;
            }
            $stmt->close();
        }
    }

    // ===== MODIFICAR =====
    if (isset($_POST['Modificar'])) {
        if ($codigoC == "" || $nombres == "") {
            $mensaje = "⚠️ Debes ingresar el Código y los datos a modificar.";
        } else {
            $stmt = $conexion->prepare("UPDATE Clientes 
                SET Nombres=?, Apellidos=?, Correo=?, Telefono=?, Registro=?, membresia=?, promocion=? 
                WHERE codigoC=?");
            $stmt->bind_param("ssssssss", $nombres, $apellidos, $correo, $telefono, $registro, $codigoM, $codigoP, $codigoC);
            $mensaje = ($stmt->execute()) 
                ? "✅ Cliente modificado correctamente." 
                : "❌ Error al modificar: " . $stmt->error;
            $stmt->close();
        }
    }

    // ===== ELIMINAR =====
    if (isset($_POST['Eliminar'])) {
        if ($codigoC == "" && $nombres == "") {
            $mensaje = "⚠️ Debes ingresar el Código o el Nombre para eliminar.";
        } else {
            if (!empty($codigoC)) {
                $stmt = $conexion->prepare("DELETE FROM Clientes WHERE codigoC=?");
                $stmt->bind_param("s", $codigoC);
            } else {
                $stmt = $conexion->prepare("DELETE FROM Clientes WHERE Nombres=?");
                $stmt->bind_param("s", $nombres);
            }
            $mensaje = ($stmt->execute()) 
                ? "✅ Cliente eliminado correctamente." 
                : "❌ Error al eliminar: " . $stmt->error;
            $stmt->close();
        }
    }

    // ===== BUSCAR =====
    if (isset($_POST['Buscar'])) {
        if ($codigoC == "" && $nombres == "") {
            $mensaje = "⚠️ Ingresa el Código o el Nombre para realizar la búsqueda.";
        } else {
            $stmt = $conexion->prepare("SELECT c.*, m.nom as nombre_membresia, p.Nombres as nombre_promocion 
                                        FROM Clientes c 
                                        LEFT JOIN Membresias m ON c.membresia = m.codigo 
                                        LEFT JOIN Promociones p ON c.promocion = p.codigoP 
                                        WHERE c.codigoC=? OR c.Nombres LIKE ?");
            $busqueda_nombre = "%$nombres%";
            $stmt->bind_param("ss", $codigoC, $busqueda_nombre);
            $stmt->execute();
            $resultado = $stmt->get_result();
            if ($row = $resultado->fetch_assoc()) {
                $mensaje = "🔎 Cliente encontrado:<br>
                            Código: " . $row['codigoC'] . "<br>
                            Nombre: " . $row['Nombres'] . " " . $row['Apellidos'] . "<br>
                            Correo: " . $row['Correo'] . "<br>
                            Teléfono: " . $row['Telefono'] . "<br>
                            Registro: " . $row['Registro'] . "<br>
                            Fecha Fin: " . $row['Fecha_Fin'] . "<br>
                            Membresía: " . ($row['nombre_membresia'] ?? 'N/A') . "<br>
                            Promoción: " . ($row['nombre_promocion'] ?? 'N/A');
            } else {
                $mensaje = "❌ No se encontró cliente con esos datos.";
            }
            $stmt->close();
        }
    }

    // Refrescar tabla
    $resultado_clientes = $conexion->query("SELECT c.*, m.nom as nombre_membresia, m.Precio as precio_membresia, 
                                                   p.Nombres as nombre_promocion, p.Precio as precio_promocion 
                                            FROM Clientes c 
                                            LEFT JOIN Membresias m ON c.membresia = m.codigo 
                                            LEFT JOIN Promociones p ON c.promocion = p.codigoP");
}

$conexion->close();

// ================== FUNCION DE ENVIO DE CORREO ==================
function enviarRecibo($correo, $nombre, $apellido, $registro, $precio_membresia, $descuento, $precio_final, $nombre_promocion) {
    $mail = new PHPMailer\PHPMailer\PHPMailer(true);

    try {
        $mail->SMTPDebug = 0;
        $mail->Debugoutput = 'html';

        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'trabinfofinal25@gmail.com';
        $mail->Password = 'invy orda zsrb zkcr';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;
        $mail->CharSet = 'UTF-8';

        $mail->setFrom('trabinfofinal25@gmail.com', 'Gimnasio JV');
        $mail->addAddress($correo, $nombre . " " . $apellido);

        $mail->isHTML(true);
        $mail->Subject = 'Recibo de Registro - Gimnasio JV';

        $fechaPago = date("Y-m-d");
        $fechaVencimiento = date("Y-m-d", strtotime($registro . " +1 month"));

        $detalle_promocion = "";
        if ($descuento > 0) {
            $detalle_promocion = "
                <p><b>Precio original:</b> $" . number_format($precio_membresia, 2) . "</p>
                <p><b>Descuento aplicado (" . htmlspecialchars($nombre_promocion) . "):</b> -$" . number_format($descuento, 2) . "</p>
                <hr>
            ";
        }

        $mail->Body = "
            <h2>¡Hola " . htmlspecialchars($nombre) . " " . htmlspecialchars($apellido) . "! 👋</h2>
            <p>¡Gracias por registrarte en <b>Gimnasio JV</b>!</p>
            <p><b>Fecha de Registro:</b> " . htmlspecialchars($registro) . "</p>
            <p><b>Fecha de Pago:</b> $fechaPago</p>
            <p><b>Fecha de Vencimiento:</b> $fechaVencimiento</p>
            <hr>
            $detalle_promocion
            <p><b>Total pagado:</b> $" . number_format($precio_final, 2) . "</p>
            <br>
            <p>💪 ¡Sigue entrenando fuerte y mantente motivado!</p>
            <p>Nos vemos en el gimnasio. 🚀</p>
        ";

        $mail->send();
        return true;

    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ Error al enviar correo: " . $mail->ErrorInfo . "</p>";
        error_log("Error al enviar correo: " . $mail->ErrorInfo);
        return false;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Clientes</title>
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
            max-width: 1100px;
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
            display: inline-block;
        }
        .btn:hover { background: #e67e22; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 25px;
        }
        th, td {
            padding: 10px 8px;
            border-bottom: 1px solid #f39c12;
            text-align: center;
            font-size: 14px;
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
            table, th, td { font-size: 12px; }
            .btn { width: 100%; }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Resultado</h1>
        <div class="message"><?php echo $mensaje; ?></div>
        <a href="../MENU/index.php" class="btn">VOLVER</a>

        <h2>Clientes Registrados</h2>
        <table>
            <tr>
                <th>Código</th>
                <th>Nombres</th>
                <th>Apellidos</th>
                <th>Correo</th>
                <th>Teléfono</th>
                <th>Registro</th>
                <th>Membresía</th>
                <th>Promoción</th>
            </tr>
            <?php if ($resultado_clientes && $resultado_clientes->num_rows > 0): ?>
                <?php while ($cliente = $resultado_clientes->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($cliente['codigoC']); ?></td>
                        <td><?php echo htmlspecialchars($cliente['Nombres']); ?></td>
                        <td><?php echo htmlspecialchars($cliente['Apellidos']); ?></td>
                        <td><?php echo htmlspecialchars($cliente['Correo']); ?></td>
                        <td><?php echo htmlspecialchars($cliente['Telefono']); ?></td>
                        <td><?php echo htmlspecialchars($cliente['Registro']); ?></td>
                        <td><?php echo htmlspecialchars($cliente['nombre_membresia'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($cliente['nombre_promocion'] ?? 'N/A'); ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="8">No hay clientes registrados.</td></tr>
            <?php endif; ?>
        </table>
    </div>
</body>
</html>