<?php
// Detectar entorno automáticamente
if (strpos($_SERVER['HTTP_HOST'], 'localhost') !== false) {
    // Config local
    define('APP_URL', (isset($_SERVER['HTTPS']) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . '/');

} else {
    // Config producción (Railway u otro hosting)
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    define('APP_URL', $protocol . '://' . $_SERVER['HTTP_HOST'] . '/');
}

define('APP_BASE', '/');
?>
