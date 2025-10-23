<?php
// Evita “headers already sent”
if (!headers_sent()) {
  ob_start();
}

// Inicia la sesión si no está activa
if (session_status() === PHP_SESSION_NONE) {
  ini_set('session.cookie_httponly', 1);
  ini_set('session.use_strict_mode', 1);
  ini_set('session.cookie_samesite', 'Lax');
  session_start();
}
?>
