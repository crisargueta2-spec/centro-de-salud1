<?php
// ✅ Configuración dinámica según el entorno
if (strpos($_SERVER['HTTP_HOST'], 'localhost') !== false) {
    // Entorno local (XAMPP)
    define('APP_BASE', '/Centro%20de%20salud%20sur/');
    define('APP_URL', 'http://localhost/Centro%20de%20salud%20sur/');
} else {
    // Entorno Railway o servidor remoto
    define('APP_BASE', '/');
    define('APP_URL', 'https://' . $_SERVER['HTTP_HOST'] . '/');
}

// ✅ Asegurarse de que APP_URL siempre termine con /
if (substr(APP_URL, -1) !== '/') {
    define('APP_URL', APP_URL . '/');
}
?>