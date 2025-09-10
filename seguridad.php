<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    // Si no hay sesión, redirige al login
    header("Location: /JVFitness/LOGIN/login.php");
    exit();
}
?>