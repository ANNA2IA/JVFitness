<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Login</title>
  
  <link rel="stylesheet" href="styles.css" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css" />
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Montserrat&display=swap" rel="stylesheet" />
  
</head>
<body>

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
            header("Location: ../MENU/menu.php");
            exit();
        } else {
            $mensaje = "Usuario o contraseña incorrectos.<br>";
        }

        $stmt->close();
        $conexion->close();
    } else {
        $mensaje = "Por favor, completa todos los campos.<Br>";
    }
}
?>

<div class="wrapper">
  <form action="login.php" method="POST">
    <h1>Login</h1>

    <div class="input-box">
      <input type="text" name="usuario" placeholder="Usuario" value="<?php echo htmlspecialchars($usuario ?? ''); ?>" />
      <i class='bx bxs-user'></i>
    </div>

    <div class="input-box">
      <input type="password" name="contrasena" placeholder="Contraseña" />
      <i class='bx bxs-lock-alt'></i>
    </div>

    <?php if ($mensaje): ?>
      <div class="error-message"><?php echo $mensaje; ?></div>
    <?php endif; ?>

    <div class="remember-forgot">
      <label><input type="checkbox" /> Recordar</label>
    </div>

    <button type="submit" name="login" class="button">Login</button>
  </form>
</div>

</body>
</html>
