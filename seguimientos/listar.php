<?php
include '../includes/conexion.php';

$sql = "SELECT s.id, p.nombre AS nombre_paciente, p.apellido AS apellido_paciente, s.resultado, s.proxima_cita, s.fecha_registro
        FROM seguimientos s
        INNER JOIN pacientes p ON s.paciente_id = p.id
        ORDER BY s.fecha_registro DESC";

$stmt = $conn->query($sql);
$seguimientos = $stmt->fetchAll(PDO::FETCH_ASSOC);

include '../templates/header.php';
?>

<h2>Listado de Seguimientos Clínicos</h2>

<table class="table table-striped">
    <thead>
        <tr>
            <th>ID</th>
            <th>Paciente</th>
            <th>Resultado</th>
            <th>Próxima Cita</th>
            <th>Fecha Registro</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($seguimientos as $seg): ?>
            <tr>
                <td><?= $seg['id'] ?></td>
                <td><?= htmlspecialchars($seg['nombre_paciente'] . ' ' . $seg['apellido_paciente']) ?></td>
                <td><?= htmlspecialchars($seg['resultado']) ?></td>
                <td><?= $seg['proxima_cita'] ?? 'No programada' ?></td>
                <td><?= $seg['fecha_registro'] ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php include '../templates/footer.php'; ?>
