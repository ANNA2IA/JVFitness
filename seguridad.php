<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    // Si no hay sesión, redirige al login
    header("Location: ../LOGIN/login.php");
    exit();
}
?>