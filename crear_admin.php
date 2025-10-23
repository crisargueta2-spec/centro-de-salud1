<?php
require_once __DIR__ . '/includes/conexion.php';

try {
    // Elimina si ya existía un usuario "admin"
    $conn->prepare("DELETE FROM usuarios WHERE username = 'admin'")->execute();

    // Inserta nuevo usuario admin / 12345
    $stmt = $conn->prepare("INSERT INTO usuarios (username, password, role) VALUES (?, ?, ?)");
    $stmt->execute([
        'admin',
        '$2y$10$txYij8mXjJzJfCuN1zHWiuc0a7tEX9q4xWZLsh6TzvUBU0swQhWni', // contraseña: 12345
        'admin'
    ]);

    echo "<h3 style='color:green'>✅ Usuario admin creado correctamente.</h3>";
    echo "<p>Usa estos datos para ingresar:</p>";
    echo "<pre>Usuario: admin\nContraseña: 12345</pre>";
} catch (PDOException $e) {
    echo "<p style='color:red'>❌ Error al crear usuario: " . $e->getMessage() . "</p>";
}
?>
