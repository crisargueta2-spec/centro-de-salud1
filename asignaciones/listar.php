<?php 
require_once __DIR__ . '/../includes/auth.php';
require_role(['admin','secretaria','medico']);
require_once __DIR__ . '/../includes/conexion.php';
include __DIR__ . '/../templates/header.php';

$q      = trim($_GET['q'] ?? '');
$scope  = $_GET['scope'] ?? 'today';
$day    = $_GET['day']   ?? date('Y-m-d');
$month  = $_GET['month'] ?? date('Y-m');

$whereParts = [];
$params = [];

if ($q !== '') {
  $like = '%' . str_replace(' ', '%', $q) . '%';
  $parts = [
    "(a.estado LIKE ? OR a.nota LIKE ?)",
    // Búsqueda por nombre de paciente (si traemos join)
    "(CONCAT(p.nombre,' ',p.apellido) LIKE ?)"
  ];

  if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $q)) {
    $parts[] = "(DATE(a.fecha_asignacion)=?)";
    $params = array_merge($params, [$like,$like,$like,$q]);
  } else {
    $params = array_merge($params, [$like,$like,$like]);
  }

  if (ctype_digit($q)) {
    // Buscar por id de asignación o por id de paciente si fuera útil
    $parts[] = "a.id = ?";
    $params[] = (int)$q;
  }

  $whereParts[] = '(' . implode(' OR ', $parts) . ')';
}

switch ($scope) {
  case 'day':
    $whereParts[] = "DATE(a.fecha_asignacion) = ?";
    $params[] = preg_match('/^\d{4}-\d{2}-\d{2}$/', $day) ? $day : date('Y-m-d');
    break;
  case 'month':
    $first = preg_match('/^\d{4}-\d{2}$/', $month) ? ($month . '-01') : (date('Y-m') . '-01');
    $whereParts[] = "DATE(a.fecha_asignacion) BETWEEN ? AND LAST_DAY(?)";
    $params[] = $first;
    $params[] = $first;
    break;
  case 'all':
    break;
  default:
    $whereParts[] = "DATE(a.fecha_asignacion) = CURDATE()";
    break;
}

$where = $whereParts ? ('WHERE ' . implode(' AND ', $whereParts)) : '';

$sql = "SELECT 
          a.id, a.paciente_id, a.especialista_id, a.fecha_asignacion, a.estado, a.nota,
          p.nombre AS pac_nombre, p.apellido AS pac_apellido
        FROM asignaciones a
        LEFT JOIN pacientes p ON p.id = a.paciente_id
        {$where}
        ORDER BY a.id DESC";

$stmt = $conexion->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

$user = user();
$rol  = strtolower($user['rol'] ?? ($user['role'] ?? ''));
?>
<div class="topbar">
  <h2 class="mb-0">Asignaciones</h2>
  <div class="d-flex gap-2">
    <form class="d-flex gap-2" method="get" action="asignaciones/listar.php">
      <input class="form-control" style="min-width:260px" type="search" name="q"
             placeholder="Buscar por estado, nota, paciente, fecha..."
             value="<?= htmlspecialchars($q) ?>">

      <select class="form-select" name="scope" onchange="this.form.submit()">
        <option value="today" <?= $scope === 'today' ? 'selected' : '' ?>>Hoy</option>
        <option value="day"   <?= $scope === 'day' ? 'selected' : '' ?>>Un día</option>
        <option value="month" <?= $scope === 'month' ? 'selected' : '' ?>>Un mes</option>
        <option value="all"   <?= $scope === 'all' ? 'selected' : '' ?>>Todos</option>
      </select>

      <input class="form-control" type="date"  name="day"   value="<?= htmlspecialchars($day) ?>"   <?= $scope === 'day' ? '' : 'disabled' ?>>
      <input class="form-control" type="month" name="month" value="<?= htmlspecialchars($month) ?>" <?= $scope === 'month' ? '' : 'disabled' ?>>

      <button class="btn btn-outline-secondary" type="submit">
        <i class="bi bi-search"></i>
      </button>

      <?php if ($q !== '' || $scope !== 'today'): ?>
        <a class="btn btn-outline-dark" href="asignaciones/listar.php">Limpiar</a>
      <?php endif; ?>
    </form>

    <?php if (in_array($rol, ['admin','secretaria'])): ?>
      <a class="btn btn-primary" href="asignaciones/crear.php">
        <i class="bi bi-plus-circle"></i> Nueva
      </a>
    <?php endif; ?>
  </div>
</div>

<div class="small-muted mb-2">
  Se muestran por defecto las asignaciones <strong>con fecha de hoy</strong>.
</div>

<div class="table-card">
  <div class="table-responsive">
    <table class="table table-hover table-bordered align-middle">
      <thead class="table-info">
        <tr>
          <th>#</th><th>Paciente</th><th>Fecha</th><th>Estado</th><th>Nota</th>
          <?php if ($rol !== 'medico'): ?>
            <th class="no-print" style="width:160px">Acciones</th>
          <?php endif; ?>
        </tr>
      </thead>
      <tbody>
        <?php foreach($rows as $r): ?>
          <tr>
            <td><?= (int)$r['id'] ?></td>
            <td><?= htmlspecialchars(trim(($r['pac_nombre'] ?? '').' '.($r['pac_apellido'] ?? ''))) ?></td>
            <td><?= htmlspecialchars($r['fecha_asignacion']) ?></td>
            <td><?= htmlspecialchars($r['estado']) ?></td>
            <td><?= nl2br(htmlspecialchars($r['nota'])) ?></td>
            <?php if ($rol !== 'medico'): ?>
              <td class="no-print">
                <a class="btn btn-sm btn-outline-secondary" href="asignaciones/editar.php?id=<?= (int)$r['id'] ?>">Editar</a>
                <a class="btn btn-sm btn-outline-danger" href="asignaciones/eliminar.php?id=<?= (int)$r['id'] ?>" onclick="return confirm('¿Eliminar asignación?')">Eliminar</a>
              </td>
            <?php endif; ?>
          </tr>
        <?php endforeach; if(empty($rows)): ?>
          <tr><td colspan="6" class="text-center text-muted">Sin resultados</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <form class="d-flex gap-2 mt-3 no-print" method="get" action="asignaciones/listar.php">
    <input type="hidden" name="q" value="<?= htmlspecialchars($q) ?>">
    <label class="form-label m-0 align-self-center">Ver por día/mes:</label>
    <input class="form-control" type="date"  name="day" value="<?= htmlspecialchars($day) ?>">
    <input type="hidden" name="scope" value="day">
    <button class="btn btn-secondary">Ver día</button>

    <div class="vr mx-2"></div>

    <input class="form-control" type="month" name="month" value="<?= htmlspecialchars($month) ?>">
    <input type="hidden" name="scope" value="month">
    <button class="btn btn-secondary">Ver mes</button>

    <a class="btn btn-outline-dark ms-auto" href="asignaciones/listar.php">Hoy</a>
    <a class="btn btn-outline-dark" href="asignaciones/listar.php?scope=all">Todos</a>
  </form>
</div>

<?php include __DIR__ . '/../templates/footer.php'; ?>
