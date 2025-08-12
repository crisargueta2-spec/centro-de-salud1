<?php
session_start();  // Inicia la sesión

// Destruir todas las variables de sesión
$_SESSION = array();

// Si se desea destruir la sesión completamente, borra la cookie de sesión
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"], $params["secure"], $params["httponly"]
    );
}

// Destruir la sesión
session_destroy();

// Redirigir al login
header("Location: login.php");
exit;
/hospital-web
    logout.php   // Este archivo destruirá la sesión y redirigirá al login
    /roles
        admin_dashboard.php
        medico_dashboard.php
        secretaria_dashboard.php
    /login
        login.php
    index.php
