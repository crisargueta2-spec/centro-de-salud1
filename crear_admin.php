<?php
require_once __DIR__ . '/includes/conexion.php';

$username = 'admin';
$password = '1234';
$role = 'admin';

// Genera un nuevo hash de la contraseña
$hash = password_hash($password, PASSWORD_DEFAULT);

// Reemplaza o inserta el usuario admin con el nuevo hash
$stmt = $conexion->prepare("REPLACE INTO usuarios (id, username, password, role) VALUES (1, ?, ?, ?)");
$stmt->execute([$username, $hash, $role]);

echo "✅ Usuario admin regenerado correctamente con contraseña 1234.";
?>
