<?php
session_start();

// Verificar si el usuario está logueado y tiene el rol adecuado
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php"); // Si no está logueado o no es admin, redirigir al login
    exit;
}

echo "Bienvenido, " . $_SESSION['username']; // Muestra el nombre del usuario
?>
<!-- Aquí puedes añadir el contenido específico del administrador -->
<h1>Bienvenido a la Página del Administrador</h1>
<a href="logout.php">Cerrar sesión</a>
