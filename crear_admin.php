<?php
require_once __DIR__ . '/includes/conexion.php';

echo "<h3>🛠 Creando usuario admin...</h3>";

try {
    $conn->exec("DELETE FROM usuarios WHERE username = 'admin'");

    $passwordPlano = '12345';
    $hash = password_hash($passwordPlano, PASSWORD_BCRYPT);

    $stmt = $conn->prepare("INSERT INTO usuarios (username, password, role) VALUES (?, ?, ?)");
    $stmt->execute(['admin', $hash, 'admin']);

    echo "<p style='color:green'>✅ Usuario <b>admin</b> creado correctamente.</p>";
    echo "<pre>Usuario: admin\nContraseña: 12345</pre>";
    echo "<p>Hash almacenado:</p>";
    echo "<code>$hash</code>";

} catch (PDOException $e) {
    echo "<p style='color:red'>❌ Error al crear usuario: " . $e->getMessage() . "</p>";
}
?>
