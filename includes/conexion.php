<?php
// Configuración de conexión con PDO
$DB_HOST = getenv('DB_HOST') ?: 'bmsfmzdnvckn5uxir27y-mysql.services.clever-cloud.com';
$DB_NAME = getenv('DB_NAME') ?: 'bmsfmzdnvckn5uxir27y';
$DB_USER = getenv('DB_USER') ?: 'urqukvdxszfjarta';
$DB_PASS = getenv('DB_PASS') ?: 'N2lXXDcixGlmW5HxavGs';
$DB_PORT = getenv('DB_PORT') ?: '3306';

try {
    // Crear conexión PDO
    $conexion = new PDO(
        "mysql:host=$DB_HOST;port=$DB_PORT;dbname=$DB_NAME;charset=utf8mb4",
        $DB_USER,
        $DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
} catch (PDOException $e) {
    // Mostrar error detallado solo en desarrollo
    echo "❌ Error de conexión: " . $e->getMessage();
    $conexion = null;
}
?>
