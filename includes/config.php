<?php
// Detectar entorno automáticamente
if (strpos($_SERVER['HTTP_HOST'], 'localhost') !== false) {
    define('APP_URL', (isset($_SERVER['HTTPS']) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . '/');
} else {
    // Producción (Railway)
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    define('APP_URL', $protocol . '://' . $_SERVER['HTTP_HOST'] . '/');
}

// No necesitamos APP_BASE por ahora
?>
