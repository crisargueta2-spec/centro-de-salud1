<?php
// 🔹 Primero intenta leer variables de entorno (para Render)
$host = getenv('DB_HOST') ?: 'localhost';
$port = getenv('DB_PORT') ?: '3306';
$user = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASS') ?: '';
$dbname = getenv('DB_NAME') ?: 'centro_de_salud'; // <-- cambia al nombre de tu BD local

try {
    $conexion = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8", $user, $password);
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}
?>
