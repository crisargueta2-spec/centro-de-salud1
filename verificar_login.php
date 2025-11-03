<?php
require_once __DIR__ . '/includes/conexion.php';

$username = 'admin';
$password = '1234';

try {
    $stmt = $conexion->prepare("SELECT * FROM usuarios WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        echo "🔍 Usuario encontrado: " . htmlspecialchars($user['username']) . "<br>";
        if (password_verify($password, $user['password'])) {
            echo "✅ Contraseña válida, login correcto.";
        } else {
            echo "❌ Contraseña incorrecta (el hash no coincide).";
        }
    } else {
        echo "❌ No existe el usuario $username en la base de datos.";
    }
} catch (PDOException $e) {
    echo "❌ Error en la consulta: " . $e->getMessage();
}
?>
