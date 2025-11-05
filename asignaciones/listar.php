<?php
require_once __DIR__.'/../includes/auth.php';
require_role(['admin','secretaria','medico']);
require_once __DIR__.'/../includes/conexion.php';
include __DIR__.'/../templates/header.php';

$q      = trim($_GET['q'] ?? '');
$scope  = $_GET['scope'] ?? 'today';
$day    = $_GET['day']   ?? date('Y-m-d');
$month  = $_GET['month'] ?? date('Y-m');

$whereParts = [];
$params = [];

// Buscar
if ($q !== '') {
    $like = "%".str_replace(" ", "%", $q)."%";

    $whereParts[] = "(p.nombre LIKE ?
                      OR p.apellido LIKE ?
                      OR CONCAT(p.nombre,' ',p.apellido) LIKE ?
                      OR e.nombre LIKE ?
                      OR e.especialidad LIKE ?
                      OR a.prioridad LIKE ?
                      OR a.estado LIKE ?
                      OR DATE(a.fecha_cita)=?)";

    $params = array_merge($params, [$like,$like,$like,$like,$like,$like,$like,$q]);
}

// Filtro fecha
switch ($scope) {
    case 'day':
        $whereParts[] = "DATE(a.fecha_cita) = ?";
        $params[] = preg_match('/^\d{4}-\d{2}-\d{2}$/',$day) ? $day : date('Y-m-d');
        break;

    case 'month':
        $first = preg_match('/^\d{4}-\d{2}$/',$month) ? "$month-01" : (date('Y-m')."-01");
        $whereParts[] = "DATE(a.fecha_cita) BETWEEN ? AND LAST_DAY(?)";
        $params[] = $first;
        $params[] = $first;
        break;

    case 'all':
        break;

    default:
    case 'today':
        $whereParts[] = "DATE(a.fecha_cita) = CURDATE()";
        break;
}

$where = $whereParts ? ("WHERE ".implode(" AND ", $whereParts)) : "";

// ✅ USAMOS $conexion, NO $conn
$sql = "SELECT a.id, a.fecha_cita, a.prioridad, a.estado,
               p.nombre AS nombre_paciente, p.apellido AS apellido_paciente,
               e.nombre AS nombre_especialista, e.especialidad
        FROM asignaciones a
        JOIN pacientes p ON p.id = a.paciente_id
        JOIN especialistas e ON e.id = a.especialista_id
        $where
        ORDER BY a.fecha_cita DESC, a.id DESC";

$stmt = $conexion->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<div class="topbar">
  <h2 class="mb-0">Asignaciones</h2>
  <div class="d-flex gap-2">
    <form class="d-flex gap-2" method="get" action="listar.php">

      <input class="form-control" style="min-width:260px" type="search"
             name="q" placeholder="Buscar por paciente, especialista, prioridad..."
             value="<?= htmlspecialchars($q) ?>">

      <select class="form-select" name="scope" onchange="this.form.submit()">
        <option value="today" <?= $scope==='today'?'selected':'' ?>>Hoy</option>
        <option value="day"   <?= $scope==='day'?'selected':'' ?>>Un día</option>
        <option value="month" <?= $scope==='month'?'selected':'' ?>>Un mes</option>
        <option value="all"   <?= $scope==='all'?'selected':'' ?>>Todos</option>
      </select>

      <input class="form-control" type="date" name="day"
             value="<?= htmlspecialchars($day) ?>" <?= $scope==='day'?'':'disabled' ?>>

      <input class="form-control" type="month" name="month"
             value="<?= htmlspecialchars($month) ?>" <?= $scope==='month'?'':'disabled' ?>>

      <button class="btn btn-outline-secondary"><i class="bi bi-search"></i></button>

      <?php if ($q !== '' || $scope !== 'today'): ?>
        <a class="btn btn-outline-dark" href="listar.php">Limpiar</a>
      <?php endif; ?>

    </form>

    <a class="btn btn-primary" href="crear.php">
      <i class="bi bi-plus-circle"></i> Nueva
    </a>
  </div>
</div>

<div class="small-muted mb-2">
  Se muestran las citas <strong>de hoy</strong> por defecto.
</div>

<div class="table-card">
  <div class="table-responsive">
    <table class="table table-hover table-bordered align-middle">
      <thead class="table-primary">
        <tr>
          <th>Paciente</th>
          <th>Especialista</th>
          <th>Prioridad</th>
          <th>Fecha</th>
          <th>Estado</th>
          <th class="no-print" style="width:260px">Acciones</th>
        </tr>
      </thead>
      <tbody>

        <?php foreach($rows as $r): ?>
        <tr>
          <td><?= htmlspecialchars($r['nombre_paciente']." ".$r['apellido_paciente']) ?></td>
          <td><?= htmlspecialchars($r['nombre_especialista']." — ".$r['especialidad']) ?></td>
          <td><?= htmlspecialchars($r['prioridad']) ?></td>
          <td><?= htmlspecialchars($r['fecha_cita']) ?></td>
          <td><?= htmlspecialchars($r['estado']) ?></td>

          <td class="no-print">
            <a class="btn btn-sm btn-outline-secondary"
               href="editar.php?id=<?= $r['id'] ?>">Editar</a>

            <a class="btn btn-sm btn-outline-danger"
               href="eliminar.php?id=<?= $r['id'] ?>"
               onclick="return confirm('¿Eliminar asignación?')">Eliminar</a>
          </td>
        </tr>
        <?php endforeach; ?>

        <?php if(empty($rows)): ?>
        <tr><td colspan="6" class="text-center text-muted">Sin resultados</td></tr>
        <?php endif; ?>

      </tbody>
    </table>
  </div>

  <form class="d-flex gap-2 mt-3 no-print" method="get" action="listar.php">

    <input type="hidden" name="q" value="<?= htmlspecialchars($q) ?>">

    <label class="form-label m-0">Ver por día/mes:</label>

    <input class="form-control" type="date" name="day" value="<?= htmlspecialchars($day) ?>">
    <input type="hidden" name="scope" value="day">
    <button class="btn btn-secondary">Ver día</button>

    <div class="vr mx-2"></div>

    <input class="form-control" type="month" name="month" value="<?= htmlspecialchars($month) ?>">
    <input type="hidden" name="scope" value="month">
    <button class="btn btn-secondary">Ver mes</button>

    <a class="btn btn-outline-dark ms-auto" href="listar.php">Hoy</a>
    <a class="btn btn-outline-dark" href="listar.php?scope=all">Todos</a>
  </form>
</div>

<?php include __DIR__.'/../templates/footer.php'; ?>
