<?php
require_once __DIR__ . '/includes/conexion.php';

try {
    // Consultar los primeros 5 usuarios
    $stmt = $conexion->query("SELECT id_usuario, username, password, role FROM usuarios LIMIT 5");
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "<h3>✅ Usuarios encontrados en la base de datos:</h3>";
    echo "<pre>";
    print_r($usuarios);
    echo "</pre>";

} catch (PDOException $e) {
    echo "❌ Error al consultar usuarios: " . $e->getMessage();
}
?>
