<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: index.html");
    exit();
}

echo "Bienvenido, " . $_SESSION['usuario'];
?>