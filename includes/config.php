<?php
if (strpos($_SERVER['HTTP_HOST'], 'localhost') !== false) {
    // Entorno local
    define('APP_BASE', '/Centro%20de%20salud%20sur/');
    define('APP_URL',  'http://localhost/Centro%20de%20salud%20sur/');
} else {
    // Entorno Railway (producción)
    define('APP_BASE', '/');
    define('APP_URL', 'https://' . $_SERVER['HTTP_HOST'] . '/');
}
?>
