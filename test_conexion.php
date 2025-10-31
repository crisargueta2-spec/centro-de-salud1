<?php
require_once __DIR__ . '/includes/conexion.php';

try {
    $stmt = $conexion->query("SELECT username, role FROM usuarios LIMIT 1");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        echo "✅ Conexión exitosa.<br>";
        echo "👤 Primer usuario: " . htmlspecialchars($row['username']) . " (" . htmlspecialchars($row['role']) . ")";
    } else {
        echo "⚠️ Conectado, pero sin datos en tabla usuarios.";
    }
} catch (PDOException $e) {
    echo "❌ Error de conexión: " . $e->getMessage();
}
?>
