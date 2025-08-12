<?php
include '../includes/conexion.php';

$stmt = $conn->query("SELECT * FROM pacientes ORDER BY fecha_referencia DESC");
$pacientes = $stmt->fetchAll(PDO::FETCH_ASSOC);

include '../templates/header.php';
?>

<h2>Lista de Pacientes Registrados</h2>

<table class="table table-striped">
  <thead>
    <tr>
      <th>ID</th>
      <th>Nombre</th>
      <th>Apellido</th>
      <th>Fecha de Nacimiento</th>
      <th>Género</th>
      <th>Motivo</th>
      <th>Médico que Refiere</th>
      <th>Fecha de Referencia</th>
      <th>Acciones</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($pacientes as $paciente): ?>
      <tr>
        <td><?= htmlspecialchars($paciente['id']) ?></td>
        <td><?= htmlspecialchars($paciente['nombre']) ?></td>
        <td><?= htmlspecialchars($paciente['apellido']) ?></td>
        <td><?= htmlspecialchars($paciente['fecha_nacimiento']) ?></td>
        <td><?= htmlspecialchars($paciente['genero']) ?></td>
        <td><?= htmlspecialchars($paciente['motivo']) ?></td>
        <td><?= htmlspecialchars($paciente['medico_referente']) ?></td>
        <td><?= htmlspecialchars($paciente['fecha_referencia']) ?></td>
        <td>
          <a href="editar.php?id=<?= $paciente['id'] ?>" class="btn btn-sm btn-warning">Editar</a>
          <a href="eliminar.php?id=<?= $paciente['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Está seguro de eliminar este paciente?');">Eliminar</a>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<?php include '../templates/footer.php'; ?>
