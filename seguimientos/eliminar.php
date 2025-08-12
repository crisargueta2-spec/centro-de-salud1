<?php
include '../includes/conexion.php';

// Verificar que se ha pasado el ID del seguimiento a eliminar
$id = $_GET['id'] ?? null;

if ($id) {
    // Eliminar el seguimiento de la base de datos
    $stmt = $conn->prepare("DELETE FROM seguimientos WHERE id = ?");
    $stmt->execute([$id]);

    // Redirigir al listado de seguimientos
    header("Location: listar.php");
    exit();
}

$conn = null;  // Cerrar la conexión
?>
