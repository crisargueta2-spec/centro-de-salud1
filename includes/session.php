<?php
// Evita problemas de “headers already sent” si hubo espacios/BOM
if (!headers_sent()) { ob_start(); }

if (session_status() === PHP_SESSION_NONE) {
  ini_set('session.cookie_httponly', 1);
  ini_set('session.use_strict_mode', 1);
  session_start();
}
