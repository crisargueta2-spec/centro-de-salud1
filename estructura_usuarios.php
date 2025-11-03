<?php
require_once __DIR__ . '/includes/conexion.php';

try {
    $stmt = $conexion->query("DESCRIBE usuarios");
    $estructura = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "<h3>📊 Estructura de la tabla usuarios:</h3><pre>";
    print_r($estructura);
    echo "</pre>";
} catch (PDOException $e) {
    echo "❌ Error al describir la tabla: " . $e->getMessage();
}
?>
