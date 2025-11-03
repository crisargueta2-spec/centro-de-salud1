<?php
require_once __DIR__ . '/../includes/auth.php';
require_role('admin'); // solo admin maneja usuarios
require_once __DIR__ . '/../includes/conexion.php';

$stmt = $conexion->query("SELECT id, username, role, created_at FROM usuarios ORDER BY id ASC");
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

include __DIR__ . '/../templates/header.php';
?>

<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Usuarios</h3>
    <a href="../usuarios/crear.php" class="btn btn-primary">Crear usuario</a>
  </div>

  <table class="table table-striped">
    <thead>
      <tr>
        <th>ID</th><th>Usuario</th><th>Rol</th><th>Creado</th><th>Acciones</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($usuarios as $u): ?>
        <tr>
          <td><?= htmlspecialchars($u['id']); ?></td>
          <td><?= htmlspecialchars($u['username']); ?></td>
          <td><?= htmlspecialchars($u['role']); ?></td>
          <td><?= htmlspecialchars($u['created_at']); ?></td>
          <td>
            <a href="../usuarios/editar.php?id=<?= $u['id']; ?>" class="btn btn-sm btn-outline-secondary">Editar</a>
            <a href="../usuarios/eliminar.php?id=<?= $u['id']; ?>" class="btn btn-sm btn-outline-danger"
               onclick="return confirm('Eliminar usuario <?= htmlspecialchars($u['username']); ?> ?')">Eliminar</a>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<?php include __DIR__ . '/../templates/footer.php'; ?>

