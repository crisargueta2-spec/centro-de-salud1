<?php
require_once __DIR__ . '/includes/conexion.php';

try {
    $stmt = $conexion->query("DESCRIBE pacientes");
    echo "<pre>";
    print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
    echo "</pre>";
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage();
}
?>
