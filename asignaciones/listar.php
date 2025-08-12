<?php
include '../includes/conexion.php';  // Asegúrate de incluir la conexión correctamente

// Consulta para obtener asignaciones con nombres completos y especialidad
$sql = "SELECT a.id, p.nombre AS nombre_paciente, p.apellido AS apellido_paciente,
        e.nombre AS nombre_especialista, e.especialidad, a.fecha_cita, a.prioridad
        FROM asignaciones a
        INNER JOIN pacientes p ON a.paciente_id = p.id
        INNER JOIN especialistas e ON a.especialista_id = e.id
        ORDER BY a.fecha_cita DESC";

// Ejecutamos la consulta
$stmt = $conn->query($sql);
$asignaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);  // Obtenemos todos los resultados

include '../templates/header.php';  // Incluye el encabezado de la página
?>

<h2>Listado de Asignaciones</h2>

<table class="table table-striped">
    <thead>
        <tr>
            <th>ID</th>
            <th>Paciente</th>
            <th>Especialista</th>
            <th>Especialidad</th>
            <th>Fecha de Cita</th>
            <th>Prioridad</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($asignaciones as $asignacion): ?>
            <tr>
                <td><?= $asignacion['id'] ?></td>
                <td><?= htmlspecialchars($asignacion['nombre_paciente'] . ' ' . $asignacion['apellido_paciente']) ?></td>
                <td><?= htmlspecialchars($asignacion['nombre_especialista']) ?></td>
                <td><?= htmlspecialchars($asignacion['especialidad']) ?></td>
                <td><?= $asignacion['fecha_cita'] ?></td>
                <td><?= ucfirst($asignacion['prioridad']) ?></td>
                <td>
                    <a href="editar.php?id=<?= $asignacion['id'] ?>">Editar</a> |
                    <a href="eliminar.php?id=<?= $asignacion['id'] ?>" onclick="return confirm('¿Seguro que quieres eliminar esta asignación?')">Eliminar</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php include '../templates/footer.php';  // Incluye el pie de página ?>
