<?php
require_once __DIR__ . '/includes/conexion.php';

try {
    // Insertar usuario de prueba
    $username = 'prueba_' . rand(100,999);
    $password = password_hash('1234', PASSWORD_DEFAULT);
    $role = 'medico';

    $stmt = $conexion->prepare("INSERT INTO usuarios (username, password, role) VALUES (?, ?, ?)");
    $stmt->execute([$username, $password, $role]);

    echo "✅ Inserción exitosa.<br>";
    echo "👤 Usuario creado: <strong>$username</strong><br>";

    // Verificar que se guardó
    $stmt2 = $conexion->query("SELECT id, username, role FROM usuarios ORDER BY id DESC LIMIT 5");
    $usuarios = $stmt2->fetchAll(PDO::FETCH_ASSOC);

    echo "<pre>";
    print_r($usuarios);
    echo "</pre>";

} catch (PDOException $e) {
    echo "❌ Error al insertar: " . $e->getMessage();
}
?>
