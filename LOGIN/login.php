<?php
session_start();

$mensaje = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = $_POST['usuario'] ?? '';
    $contrasena = $_POST['contrasena'] ?? '';

    if (!empty($usuario) && !empty($contrasena)) {
        $conexion = new mysqli("localhost", "root", "admin123", "JV");
        if ($conexion->connect_error) {
            die("Error de conexión: " . $conexion->connect_error);
        }

        $conexion->set_charset("utf8");

        $sql = "SELECT * FROM Administradores WHERE Usuario = ? AND Contrasenya = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("ss", $usuario, $contrasena);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows === 1) {
            $_SESSION['usuario'] = $usuario;
            header("Location: ../MENU/index.php");
            exit();
        } else {
            $mensaje = "Usuario o contraseña incorrectos.";
        }

        $stmt->close();
        $conexion->close();
    } else {
        $mensaje = "Por favor, completa todos los campos.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login</title>
  <link rel="stylesheet" href="styles.css" />
  <link href="https://fonts.googleapis.com/css2?family=Montserrat&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css">
</head>
<body>
  <div class="container">
    <div class="login-box">
      <h2>Iniciar Sesión</h2>
      <form action="login.php" method="POST">
        <div class="input-box">
          <i class='bx bxs-user'></i>
          <input type="text" name="usuario" placeholder="Usuario" value="<?php echo htmlspecialchars($usuario ?? ''); ?>"  />
        </div>

        <div class="input-box">
          <i class='bx bxs-lock-alt'></i>
          <input type="password" name="contrasena" placeholder="Contraseña"  />
        </div>

     <?php if ($mensaje): ?>
    <div class="error-message"><?php echo $mensaje; ?></div>
    <?php endif; ?>


        <button type="submit" class="btn">Entrar</button>
      </form>
    </div>
  </div>
</body>
</html>
