<?php
include '../includes/conexion.php';

$id = $_GET['id'] ?? null;

if ($id) {
    // Eliminar asignaciones del paciente
    $stmt = $conn->prepare("DELETE FROM asignaciones WHERE paciente_id = ?");
    $stmt->execute([$id]);

    // Eliminar seguimientos del paciente
    $stmt = $conn->prepare("DELETE FROM seguimientos WHERE paciente_id = ?");
    $stmt->execute([$id]);

    // Finalmente eliminar el paciente
    $stmt = $conn->prepare("DELETE FROM pacientes WHERE id = ?");
    $stmt->execute([$id]);
}

header('Location: listar.php');
exit;
