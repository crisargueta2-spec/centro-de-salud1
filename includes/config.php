<?php
/**
 * Configuración general del sistema
 * Compatible con entorno local (XAMPP) y producción (Railway)
 */

// Detectar entorno automáticamente
if (strpos($_SERVER['HTTP_HOST'], 'localhost') !== false) {
    // 🖥️ Entorno local
    define('APP_URL', (isset($_SERVER['HTTPS']) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . '/centrodesaludsur/');
} else {
    // ☁️ Entorno producción (Railway, hosting, etc.)
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    define('APP_URL', $protocol . '://' . $_SERVER['HTTP_HOST'] . '/');
}

define('APP_BASE', '/');

// ✅ Conexión a base de datos Railway
$DB_HOST = 'maglev.proxy.rlwy.net';
$DB_PORT = '45098';
$DB_NAME = 'railway';
$DB_USER = 'root';
$DB_PASS = 'gIEWCrtENrUZBedRLzuonZaLqaCHBnMC';

// Conexión PDO
try {
    $conn = new PDO(
        "mysql:host=$DB_HOST;port=$DB_PORT;dbname=$DB_NAME;charset=utf8mb4",
        $DB_USER,
        $DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (PDOException $e) {
    die("❌ Error de conexión a la base de datos: " . $e->getMessage());
}
?>
