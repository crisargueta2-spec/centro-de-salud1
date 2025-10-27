<?php
require_once __DIR__.'/../includes/auth.php';
require_role('admin');
require_once __DIR__.'/../includes/conexion.php';
include __DIR__.'/../templates/header.php';

$stmt = $conn->query("SELECT id, username, role, created_at FROM usuarios ORDER BY id DESC");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="topbar">
  <h2 class="mb-0">Usuarios</h2>
  <a href="usuarios/crear.php" class="btn btn-primary">
    <i class="bi bi-plus-circle"></i> Nuevo Usuario
  </a>
</div>

<div class="table-card">
  <div class="table-responsive">
    <table class="table table-hover table-bordered align-middle">
      <thead class="table-primary">
        <tr>
          <th style="width:80px">ID</th>
          <th>Usuario</th>
          <th>Rol</th>
          <th>Creado</th>
          <th style="width:170px">Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($rows as $u): ?>
          <tr>
            <td><?= $u['id'] ?></td>
            <td><?= htmlspecialchars($u['username']) ?></td>
            <td><?= htmlspecialchars($u['role']) ?></td>
            <td><?= htmlspecialchars($u['created_at']) ?></td>
            <td class="text-center">
              <a class="btn btn-sm btn-outline-secondary" href="usuarios/editar.php?id=<?= $u['id'] ?>" title="Editar">
                <i class="bi bi-pencil"></i>
              </a>
              <a class="btn btn-sm btn-outline-danger" href="usuarios/eliminar.php?id=<?= $u['id'] ?>"
                 onclick="return confirm('¿Eliminar usuario?')" title="Eliminar">
                <i class="bi bi-trash"></i>
              </a>
            </td>
          </tr>
        <?php endforeach; ?>
        <?php if (empty($rows)): ?>
          <tr><td colspan="5" class="text-center text-muted">No hay usuarios registrados.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php include __DIR__.'/../templates/footer.php'; ?>
