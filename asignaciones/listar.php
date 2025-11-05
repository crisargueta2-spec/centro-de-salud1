<?php 
require_once __DIR__ . '/../includes/auth.php';
require_role(['admin', 'secretaria', 'medico']);
require_once __DIR__ . '/../includes/conexion.php';
include __DIR__ . '/../templates/header.php';

$q      = trim($_GET['q'] ?? '');
$scope  = $_GET['scope'] ?? 'today';
$day    = $_GET['day']   ?? date('Y-m-d');
$month  = $_GET['month'] ?? date('Y-m');

$whereParts = [];
$params = [];

// BÚSQUEDA
if ($q !== '') {

  $like = '%' . str_replace(' ', '%', $q) . '%';

  $parts = [
    "p.nombre LIKE ?",
    "p.apellido LIKE ?",
    "CONCAT(p.nombre,' ',p.apellido) LIKE ?",
    "a.estado LIKE ?",
    "a.prioridad LIKE ?"
  ];

  // fecha exacta
  if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $q)) {
    $parts[] = "DATE(a.fecha_cita) = ?";
    $params = array_merge($params, [$like,$like,$like,$like,$like,$q]);
  } else {
    $params = array_merge($params, [$like,$like,$like,$like,$like]);
  }

  if (ctype_digit($q)) {
    $parts[] = "a.id = ?";
    $params[] = (int)$q;
  }

  $whereParts[] = '(' . implode(' OR ', $parts) . ')';
}

// FILTROS
switch ($scope) {
  case 'day':
    $whereParts[] = "DATE(a.fecha_cita) = ?";
    $params[] = $day;
    break;

  case 'month':
    $first = $month . '-01';
    $whereParts[] = "DATE(a.fecha_cita) BETWEEN ? AND LAST_DAY(?)";
    $params[] = $first;
    $params[] = $first;
    break;

  case 'all':
    break;

  default: // today
    $whereParts[] = "DATE(a.fecha_cita) = CURDATE()";
    break;
}

$where = $whereParts ? ('WHERE ' . implode(' AND ', $whereParts)) : '';

$sql = "SELECT 
          a.id, a.paciente_id, a.especialista_id, a.fecha_cita,
          a.prioridad, a.estado,
          p.nombre AS pac_nombre, p.apellido AS pac_apellido
        FROM asignaciones a
        LEFT JOIN pacientes p ON p.id = a.paciente_id
        {$where}
        ORDER BY a.id DESC";

$stmt = $conexion->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="topbar">
  <h2>Asignaciones</h2>
  <div class="d-flex gap-2">
    <form class="d-flex gap-2" method="get" action="">
      <input class="form-control" type="search" name="q" placeholder="Buscar..."
             value="<?= htmlspecialchars($q) ?>">

      <select class="form-select" name="scope" onchange="this.form.submit()">
        <option value="today" <?= $scope==='today'?'selected':'' ?>>Hoy</option>
        <option value="day"   <?= $scope==='day'?'selected':'' ?>>Un día</option>
        <option value="month" <?= $scope==='month'?'selected':'' ?>>Un mes</option>
        <option value="all"   <?= $scope==='all'?'selected':'' ?>>Todos</option>
      </select>

      <input class="form-control" type="date" name="day" value="<?= $day ?>" <?= $scope==='day'?'':'disabled' ?>>
      <input class="form-control" type="month" name="month" value="<?= $month ?>" <?= $scope==='month'?'':'disabled' ?>>

      <button class="btn btn-outline-secondary">Buscar</button>

      <?php if ($q !== '' || $scope !== 'today'): ?>
        <a class="btn btn-outline-dark" href="listar.php">Limpiar</a>
      <?php endif; ?>
    </form>

    <a class="btn btn-primary" href="crear.php">
      <i class="bi bi-plus-circle"></i> Nueva
    </a>
  </div>
</div>

<div class="table-card mt-3">
  <div class="table-responsive">
    <table class="table table-hover table-bordered">
      <thead class="table-info">
        <tr>
          <th>#</th>
          <th>Paciente</th>
          <th>Fecha cita</th>
          <th>Prioridad</th>
          <th>Estado</th>
          <th class="no-print">Acciones</th>
        </tr>
      </thead>
      <tbody>

<?php if (!empty($rows)): ?>
<?php foreach ($rows as $r): ?>
        <tr>
          <td><?= $r['id'] ?></td>
          <td><?= htmlspecialchars($r['pac_nombre'].' '.$r['pac_apellido']) ?></td>
          <td><?= htmlspecialchars($r['fecha_cita']) ?></td>
          <td><?= htmlspecialchars($r['prioridad']) ?></td>
          <td><?= htmlspecialchars($r['estado']) ?></td>
          <td>
            <a class="btn btn-sm btn-outline-secondary" href="editar.php?id=<?= $r['id'] ?>">Editar</a>
            <a class="btn btn-sm btn-outline-danger"
               href="eliminar.php?id=<?= $r['id'] ?>"
               onclick="return confirm('¿Eliminar?')">Borrar</a>
          </td>
        </tr>
<?php endforeach; ?>
<?php else: ?>
        <tr>
          <td colspan="6" class="text-center text-muted">Sin resultados</td>
        </tr>
<?php endif; ?>

      </tbody>
    </table>
  </div>
</div>

<?php include __DIR__.'/../templates/footer.php';
