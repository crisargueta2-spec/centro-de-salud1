<?php
echo "<h3>📂 Archivos reales en /var/www/html:</h3><pre>";
$root = '/var/www/html';
print_r(scandir($root));

echo "\n\n📁 Contenido de includes/: \n";
if (is_dir("$root/includes")) {
    print_r(scandir("$root/includes"));
} else {
    echo "No existe la carpeta includes/";
}
echo "</pre>";
?>
