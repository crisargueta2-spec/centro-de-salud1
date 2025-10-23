<?php
require_once __DIR__ . '/includes/conexion.php';

echo "<h3>🔍 Verificación de usuarios en la base de datos:</h3>";

try {
    $stmt = $conn->query("SELECT id, username, role FROM usuarios");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (!$rows) {
        echo "<p style='color:red'>❌ No hay usuarios en la tabla 'usuarios'.</p>";
    } else {
        echo "<pre>";
        print_r($rows);
        echo "</pre>";
    }
} catch (PDOException $e) {
    echo "<p style='color:red'>Error en la consulta: " . $e->getMessage() . "</p>";
}
