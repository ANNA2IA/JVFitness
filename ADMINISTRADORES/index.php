<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Administradores</title>
    <link rel="stylesheet" href="estilos_admin.css">
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
