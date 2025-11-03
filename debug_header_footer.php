<?php
echo "<h2>🧩 Diagnóstico de header y footer</h2>";

$header = __DIR__ . '/templates/header.php';
$footer = __DIR__ . '/templates/footer.php';

foreach ([$header, $footer] as $file) {
    echo "<h3>📄 Revisando: " . basename($file) . "</h3>";
    if (file_exists($file)) {
        echo "✅ Archivo encontrado<br>";
        echo "📏 Tamaño: " . filesize($file) . " bytes<br>";
        echo "🔍 Primeras líneas:<pre>";
        $lines = file($file);
        echo htmlspecialchars(implode('', array_slice($lines, 0, 10)));
        echo "</pre><hr>";
    } else {
        echo "❌ No se encontró.<hr>";
    }
}
?>
