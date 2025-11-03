<?php
// Evitar redirecciones automáticas
ob_start();

echo "<h2>🧩 Diagnóstico del flujo de salida</h2>";

function safe_include($file) {
    echo "<p>🔹 Intentando incluir: <strong>$file</strong></p>";
    if (file_exists($file)) {
        echo "✅ Archivo encontrado<br>";
        try {
            include $file;
            echo "<p>✅ Incluido correctamente: $file</p>";
        } catch (Throwable $e) {
            echo "<p>❌ Error al incluir $file: " . $e->getMessage() . "</p>";
        }
    } else {
        echo "❌ No se encontró: $file<br>";
    }
}

$dashboard = __DIR__ . '/roles/admin_dashboard.php';
$header = __DIR__ . '/templates/header.php';
$footer = __DIR__ . '/templates/footer.php';

safe_include($header);
echo "<hr>";
safe_include($dashboard);
echo "<hr>";
safe_include($footer);

echo "<h3>✅ Diagnóstico finalizado</h3>";
ob_end_flush();
?>
