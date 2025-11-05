<?php
require_once __DIR__ . '/includes/conexion.php';

try {
    $stmt = $conexion->query("SHOW TABLES");
    $tablas = $stmt->fetchAll(PDO::FETCH_COLUMN);

    echo "✅ Conexión establecida correctamente.<br><br>";
    echo "📋 Tablas en la base de datos:<br>";
    echo "<pre>";
    print_r($tablas);
    echo "</pre>";
} catch (PDOException $e) {
    echo "❌ Error de conexión o consulta: " . $e->getMessage();
}
?>
