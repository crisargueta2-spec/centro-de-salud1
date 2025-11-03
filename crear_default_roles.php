<?php
require_once __DIR__ . '/includes/conexion.php';
require_once __DIR__ . '/includes/auth.php';
require_role('admin'); // solo admin puede ejecutar

// Usuarios por defecto
$users = [
    ['username' => 'medico1', 'password' => '123', 'role' => 'medico'],
    ['username' => 'secretaria', 'password' => '321', 'role' => 'secretaria'],
];

foreach ($users as $u) {
    $hash = password_hash($u['password'], PASSWORD_DEFAULT);
    // REPLACE INTO o INSERT IGNORE según quieras sobreescribir
    $stmt = $conexion->prepare("INSERT INTO usuarios (username, password, role) VALUES (?, ?, ?)
                                ON DUPLICATE KEY UPDATE password = VALUES(password), role = VALUES(role)");
    $stmt->execute([$u['username'], $hash, $u['role']]);
    echo "Usuario {$u['username']} creado/actualizado.\n";
}
echo "Listo.\n";
