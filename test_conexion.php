<?php
require_once "includes/conexion.php";

try {
    $stmt = $conexion->query("SELECT usuario FROM usuarios LIMIT 1");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "✅ Conexión exitosa. Primer usuario: " . $row['usuario'];
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage();
}
?>
