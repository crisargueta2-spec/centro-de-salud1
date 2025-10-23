<?php
require_once __DIR__ . '/includes/conexion.php';

try {
    $stmt = $conn->query("SHOW COLUMNS FROM usuarios");
    $cols = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "<pre>";
    print_r($cols);
    echo "</pre>";
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage();
}
?>
