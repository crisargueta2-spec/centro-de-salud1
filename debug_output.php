<?php
ob_start();

echo "<h2>🧩 Diagnóstico avanzado del flujo de salida</h2>";

function debug_include($file) {
    echo "<hr><h3>📄 Probando incluir: $file</h3>";
    if (!file_exists($file)) {
        echo "❌ No se encontró el archivo.<br>";
        return;
    }

    echo "✅ Archivo encontrado.<br>";
    try {
        include $file;
        echo "<p>✅ Inclusión exitosa de: $file</p>";
    } catch (Throwable $e) {
        echo "<p style='color:red'>❌ Error al incluir $file: " . $e->getMessage() . "</p>";
    }
}

// 1️⃣ Header
debug_include(__DIR__ . '/templates/header.php');

// 2️⃣ Dashboard
echo "<hr><h3>🧩 Incluyendo dashboard...</h3>";
try {
    require_once __DIR__ . '/roles/admin_dashboard.php';
    echo "<p>✅ admin_dashboard.php ejecutado completamente.</p>";
} catch (Throwable $e) {
    echo "<p style='color:red'>❌ Error dentro de admin_dashboard.php: " . $e->getMessage() . "</p>";
}

// 3️⃣ Footer
debug_include(__DIR__ . '/templates/footer.php');

echo "<hr><h2>✅ Diagnóstico completado</h2>";
ob_end_flush();
?>
