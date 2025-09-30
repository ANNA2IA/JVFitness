<?php
session_start();
session_destroy();
header("Location: ../PROYECTO2025/LOGIN/login.php"); 
exit();
?>
