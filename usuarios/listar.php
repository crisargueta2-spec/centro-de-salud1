<?php
require_once __DIR__ . '/../includes/auth.php';
require_role('admin'); // solo admin maneja usuarios
require_once __DIR__ . '/../includes/conexion.php';

try {
    $stmt = $conexion->query("SELECT id, username, role, created_at FROM usuarios ORDER BY id ASC");
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("❌ Error al obtener usuarios: " . $e->getMessage());
}

include __DIR__ . '/../templates/header.php';
?>

<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3><i class="bi bi-people-fill me-2"></i>Usuarios</h3>
    <a href="../usuarios/crear.php" class="btn btn-success"><i class="bi bi-plus-lg"></i> Crear usuario</a>
  </div>

  <table class="table table-hover align-middle">
    <thead class="table-dark">
      <tr>
        <th>ID</th><th>Usuario</th><th>Rol</th><th>Creado</th><th>Acciones</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($usuarios as $u): ?>
        <tr>
          <td><?= htmlspecialchars($u['id']); ?></td>
          <td><?= htmlspecialchars($u['username']); ?></td>
          <td><?= htmlspecialchars(ucfirst($u['role'])); ?></td>
          <td><?= htmlspecialchars($u['created_at']); ?></td>
          <td>
            <a href="../usuarios/editar.php?id=<?= $u['id']; ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
            <a href="../usuarios/eliminar.php?id=<?= $u['id']; ?>" class="btn btn-sm btn-outline-danger"
               onclick="return confirm('¿Eliminar usuario <?= htmlspecialchars($u['username']); ?>?')"><i class="bi bi-trash"></i></a>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<?php include __DIR__ . '/../templates/footer.php'; ?>
