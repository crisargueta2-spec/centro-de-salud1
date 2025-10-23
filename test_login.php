<?php
require_once __DIR__ . '/includes/conexion.php';

$stmt = $conn->query("SELECT username FROM usuarios");
$users = $stmt->fetchAll(PDO::FETCH_COLUMN);

echo "<pre>";
echo "Usuarios encontrados:\n";
print_r($users);
echo "</pre>";
