<?php
require_once __DIR__ . '/includes/conexion.php';

echo "<h3>🔄 Reiniciando usuario admin...</h3>";

try {
    // 1. Borra usuario existente
    $conn->exec("DELETE FROM usuarios WHERE username = 'admin'");

    // 2. Genera hash nuevo directamente en este servidor
    $passwordPlano = '12345';
    $hash = password_hash($passwordPlano, PASSWORD_DEFAULT);

    // 3. Inserta de nuevo
    $stmt = $conn->prepare("INSERT INTO usuarios (username, password, role) VALUES (?, ?, ?)");
    $stmt->execute(['admin', $hash, 'admin']);

    echo "<p style='color:green'>✅ Usuario admin recreado con éxito.</p>";
    echo "<pre>Usuario: admin\nContraseña: 12345</pre>";
    echo "<p>Nuevo hash generado:</p><code>$hash</code>";

} catch (PDOException $e) {
    echo "<p style='color:red'>❌ Error: " . $e->getMessage() . "</p>";
}
?>
