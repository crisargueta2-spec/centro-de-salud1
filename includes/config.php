<?php
// Detectar entorno automáticamente
if (strpos($_SERVER['HTTP_HOST'], 'localhost') !== false) {
    // 🔹 Config local
    $app_url = 'http://localhost/Centro%20de%20salud%20sur/';
} else {
    // 🔹 Config producción (Railway u otro hosting)
    $app_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http');
    $app_url .= '://' . $_SERVER['HTTP_HOST'] . '/';
}

// 🔹 Asegurar que termine con una sola barra /
if (substr($app_url, -1) !== '/') {
    $app_url .= '/';
}

define('APP_URL', $app_url);
define('APP_BASE', '/');
?>