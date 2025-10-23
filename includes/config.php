<?php
// 🔹 Detección automática del entorno
if (isset($_SERVER['HTTP_HOST']) && strpos($_SERVER['HTTP_HOST'], 'localhost') !== false) {
    // 🔸 Entorno local (XAMPP)
    define('APP_URL', 'http://localhost/Centro%20de%20salud%20sur/');
} else {
    // 🔸 Entorno producción (Railway)
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'centro-de-salud1-production.up.railway.app';
    define('APP_URL', $scheme . '://' . $host . '/');
}

// 🔹 Base del proyecto (para rutas internas)
define('APP_BASE', '/');

// 🔹 (DEBUG opcional)
if (isset($_GET['debug_url'])) {
    echo "APP_URL: " . APP_URL;
    exit;
}
?>
