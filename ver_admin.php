<?php
echo "Archivo ver_admin.php cargado correctamente<br>";
require_once __DIR__ . '/includes/conexion.php';

try {
    $stmt = $conexion->query("SELECT id_usuario, username, password, role FROM usuarios LIMIT 5");
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "<pre>";
    print_r($usuarios);
    echo "</pre>";
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage();
}
?>
    