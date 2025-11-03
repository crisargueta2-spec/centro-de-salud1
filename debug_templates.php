<?php
$dir = __DIR__ . '/templates';
if (is_dir($dir)) {
    echo "📁 Directorio 'templates' encontrado.<br><br>";
    $files = scandir($dir);
    echo "<pre>";
    print_r($files);
    echo "</pre>";
} else {
    echo "❌ No existe el directorio 'templates'.";
}
?>
