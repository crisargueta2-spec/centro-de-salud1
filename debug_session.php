<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/conexion.php';

echo "<h2>🧠 Estado de la Sesión en Render</h2>";

if (isset($_SESSION['user'])) {
    echo "<p>✅ Sesión activa:</p>";
    echo "<pre>";
    print_r($_SESSION['user']);
    echo "</pre>";
} else {
    echo "<p>⚠️ No hay sesión activa.</p>";
}

$path = __DIR__ . '/roles/admin_dashboard.php';
echo "<hr><p>Verificando archivo: roles/admin_dashboard.php → ";
echo file_exists($path) ? '✅ Existe' : '❌ No encontrado';
echo "</p>";

if (file_exists($path)) {
    echo "<hr><p>Incluyendo dashboard...</p>";
    include $path;
    echo "<hr><p>✅ Dashboard incluido correctamente.</p>";
} else {
    echo "<p>❌ No se puede incluir admin_dashboard.php</p>";
}
?>
