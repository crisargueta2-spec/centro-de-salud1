<?php
require_once __DIR__ . '/conexion.php';

$username = 'admin';
$password = '1234';

$stmt = $conn->prepare("SELECT * FROM usuarios WHERE username = ?");
$stmt->execute([$username]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

echo "<pre>";
print_r($user);
if ($user && password_verify($password, $user['password'])) {
    echo "✅ Contraseña correcta\n";
} else {
    echo "❌ Contraseña incorrecta\n";
}
echo "</pre>";
