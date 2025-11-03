<?php
require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/includes/auth.php';

echo "<h2>🧭 Diagnóstico del Dashboard Admin</h2>";

// Estado de sesión
if (isset($_SESSION['user'])) {
    echo "<p>✅ Sesión detectada:</p><pre>";
    print_r($_SESSION['user']);
    echo "</pre>";
} else {
    echo "<p>⚠️ No hay sesión activa.</p>";
}

// Archivo destino
$path = __DIR__ . '/roles/admin_dashboard.php';
if (file_exists($path)) {
    echo "<p>📂 Archivo encontrado: <code>$path</code></p>";
    echo "<hr><h3>🧩 Contenido visible de admin_dashboard.php:</h3><pre>";
    echo htmlspecialchars(file_get_contents($path));
    echo "</pre>";
} else {
    echo "<p>❌ No se encontró el dashboard.</p>";
}
?>
