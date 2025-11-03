<?php
// Mostrar todos los errores visibles
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>🧩 Diagnóstico del Dashboard</h2>";

// Cargar dependencias clave
require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/conexion.php';

if (isset($_SESSION['user'])) {
    echo "<p>✅ Sesión activa de: <b>" . htmlspecialchars($_SESSION['user']['username']) . "</b> (" . htmlspecialchars($_SESSION['user']['rol'] ?? 'sin rol') . ")</p>";
} else {
    echo "<p>⚠️ No hay sesión activa</p>";
}

$path = __DIR__ . '/roles/admin_dashboard.php';

if (!file_exists($path)) {
    die("<p>❌ No se encontró el archivo <code>roles/admin_dashboard.php</code></p>");
}

echo "<hr><h3>📄 Cargando admin_dashboard.php...</h3>";

try {
    include $path;
    echo "<hr><p>✅ admin_dashboard.php se cargó sin errores fatales.</p>";
} catch (Throwable $e) {
    echo "<p>❌ Error detectado: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
?>
