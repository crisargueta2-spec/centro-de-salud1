<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h3>🧩 Diagnóstico admin_dashboard</h3>";

require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/includes/conexion.php';

echo "<p>✅ Archivos cargados correctamente.</p>";

if (isset($_SESSION['user'])) {
    echo "<p>👤 Usuario activo: " . htmlspecialchars($_SESSION['user']['username']) . "</p>";
    echo "<p>Rol: " . htmlspecialchars($_SESSION['user']['rol'] ?? 'no definido') . "</p>";
} else {
    echo "<p>⚠️ No hay sesión iniciada.</p>";
}

$path = __DIR__ . '/roles/admin_dashboard.php';
if (file_exists($path)) {
    echo "<p>📂 Archivo admin_dashboard.php encontrado en roles/.</p>";
    include $path;
} else {
    echo "<p>❌ No se encontró roles/admin_dashboard.php.</p>";
}
?>
