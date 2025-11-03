<?php
require_once __DIR__ . '/includes/conexion.php';

echo "<h2>Prueba de conexión a la base de datos</h2>";

try {
    // Verificamos si el objeto $conn existe y es válido
    if (!isset($conn)) {
        throw new Exception("Variable \$conn no inicializada");
    }

    // Ejecutamos una consulta simple
    $stmt = $conn->query("SELECT NOW() AS fecha_actual");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    echo "<p><b>✅ Conexión exitosa.</b></p>";
    echo "<p>Servidor respondió con la fecha/hora actual:</p>";
    echo "<pre>" . htmlspecialchars($row['fecha_actual']) . "</pre>";

} catch (Throwable $e) {
    echo "<p><b>❌ Error de conexión:</b></p>";
    echo "<pre>" . htmlspecialchars($e->getMessage()) . "</pre>";
}
?>
