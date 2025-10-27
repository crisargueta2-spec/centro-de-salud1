<?php
require_once __DIR__ . '/conexion.php';

try {
    $stmt = $conexion->query("SELECT username FROM usuarios LIMIT 1");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    echo "✅ Conexión exitosa. Primer usuario: " . htmlspecialchars($row['username'] ?? 'sin datos');
} catch (PDOException $e) {
    echo "❌ Error de conexión: " . $e->getMessage();
}
?>