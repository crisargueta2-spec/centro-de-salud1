<?php
require_once __DIR__ . '/includes/conexion.php';

try {
    $stmt = $conexion->query("SELECT id, username, password, role FROM usuarios LIMIT 5");
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "<h3>✅ Usuarios encontrados en la base de datos:</h3><pre>";
    print_r($usuarios);
    echo "</pre>";
} catch (PDOException $e) {
    echo "❌ Error al consultar usuarios: " . $e->getMessage();
}
?>
