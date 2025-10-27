<?php
echo "<h2>🧭 Diagnóstico de Rutas Render</h2>";
echo "<pre>";
echo "Document root: " . $_SERVER['DOCUMENT_ROOT'] . "\n";
echo "Current dir: " . __DIR__ . "\n";
echo "Current file: " . __FILE__ . "\n";
echo "PHP Version: " . phpversion() . "\n";
echo "Base URL guess: http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME']) . "\n";
echo "</pre>";
?>
