<?php
require_once __DIR__.'/../includes/auth.php';
require_role(['admin','secretaria']);
require_once __DIR__.'/../includes/conexion.php';
include __DIR__.'/../templates/header.php';

$q = trim($_GET['q'] ?? '');
$params = [];
$where  = '';

if ($q !== '') {
  $like = '%'.str_replace(' ','%',$q).'%';
  $where = "WHERE (e.nombre LIKE ? OR e.especialidad LIKE ?)";
  $params = [$like, $like];
}

$sql = "SELECT e.id, e.nombre, e.especialidad
        FROM especialistas e
        $where
        ORDER BY e.id DESC";
$stmt = $conn->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<div class="topbar">
  <h2 class="mb-0">Especialistas</h2>
  <div class="d-flex gap-2">
    <form class="d-flex" method="get" action="especialista/listar.php">
      <input class="form-control me-2" style="min-width:260px" type="search" name="q"
             placeholder="Buscar por nombre o especialidad..." value="<?= htmlspecialchars($q) ?>">
      <button class="btn btn-outline-secondary" type="submit"><i class="bi bi-search"></i></button>
      <?php if ($q !== '') { ?>
        <a class="btn btn-outline-dark ms-2" href="especialista/listar.php">Limpiar</a>
      <?php } ?>
    </form>
    <a class="btn btn-primary" href="especialista/crear.php"><i class="bi bi-plus-circle"></i> Nuevo</a>
  </div>
</div>

<div class="table-card">
  <div class="table-responsive">
    <table class="table table-hover table-bordered align-middle">
      <thead class="table-primary">
        <tr>
          <th>Nombre</th>
          <th>Especialidad</th>
          <th class="no-print" style="width:150px">Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($rows as $r) { ?>
          <tr>
            <td><?= htmlspecialchars($r['nombre']) ?></td>
            <td><?= htmlspecialchars($r['especialidad']) ?></td>
            <td class="no-print">
              <a class="btn btn-sm btn-outline-secondary" href="especialista/editar.php?id=<?= $r['id'] ?>">Editar</a>
              <a class="btn btn-sm btn-outline-danger" href="especialista/eliminar.php?id=<?= $r['id'] ?>"
                 onclick="return confirm('¿Eliminar especialista?')">Eliminar</a>
            </td>
          </tr>
        <?php } if (empty($rows)) { ?>
          <tr><td colspan="3" class="text-center text-muted">Sin resultados</td></tr>
        <?php } ?>
      </tbody>
    </table>
  </div>
</div>
<?php include __DIR__.'/../templates/footer.php'; ?>
