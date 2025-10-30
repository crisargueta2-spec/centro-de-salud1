<?php
echo "📂 RUTA ACTUAL: " . __DIR__ . "<br>";
echo "✅ Archivos encontrados:<br>";

$files = scandir(__DIR__);
foreach ($files as $file) {
    echo htmlspecialchars($file) . "<br>";
}
?>
Agrego test_paths.php directamente desde GitHub para Render
