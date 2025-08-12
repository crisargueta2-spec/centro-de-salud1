<?php
include '../includes/conexion.php';

$id = $_GET['id'] ?? null;

if ($id) {
    // Eliminar asignación
    $stmt = $conn->prepare("DELETE FROM asignaciones WHERE id = ?");
    $stmt->execute([$id]);
    echo "Asignación eliminada correctamente.";
}

header('Location: listar.php');  // Redirige al listado de asignaciones
exit;
