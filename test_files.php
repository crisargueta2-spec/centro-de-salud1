<?php
echo "<pre>";
print_r(scandir(__DIR__));
echo "</pre>";

if (is_dir(__DIR__ . '/roles')) {
    echo "\n\nContenido de /roles:\n";
    print_r(scandir(__DIR__ . '/roles'));
} else {
    echo "\n\n❌ No existe la carpeta /roles en este deploy.";
}
?>