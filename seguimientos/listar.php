<?php
require_once __DIR__.'/../includes/auth.php';
require_role(['admin','medico']);
require_once __DIR__.'/../includes/conexion.php';
include __DIR__.'/../templates/header.php';

$q      = trim($_GET['q'] ?? '');
$scope  = $_GET['scope'] ?? 'today';
$day    = $_GET['day']   ?? date('Y-m-d');
$month  = $_GET['month'] ?? date('Y-m');

$whereParts = [];
$params = [];

/* 🔎 Búsqueda robusta: solo compara fechas si $q es YYYY-MM-DD */
if ($q !== '') {
  $like = '%'.str_replace(' ','%',$q).'%';

  $parts  = [
    "(p.nombre LIKE ?
      OR p.apellido LIKE ?
      OR CONCAT(p.nombre,' ',p.apellido) LIKE ?
      OR s.resultado LIKE ?)"
  ];
  $params = array_merge($params, [$like,$like,$like,$like]);

  // Solo agregar condiciones de fecha si $q es yyyy-mm-dd
  if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $q)) {
    $parts[] = "DATE(s.fecha_registro)=? OR DATE(s.proxima_cita)=?";
    $params[] = $q;
    $params[] = $q;
  }

  $whereParts[] = '(' . implode(' OR ', $parts) . ')';
}

/* 📅 Filtro por fecha_registro */
switch ($scope) {
  case 'day':
    $whereParts[] = "DATE(s.fecha_registro) = ?";
    $params[] = preg_match('/^\d{4}-\d{2}-\d{2}$/',$day)?$day:date('Y-m-d');
    break;

  case 'month':
    $first = preg_match('/^\d{4}-\d{2}$/',$month)?($month.'-01'):(date('Y-m').'-01');
    $whereParts[] = "DATE(s.fecha_registro) BETWEEN ? AND LAST_DAY(?)";
    $params[] = $first; $params[] = $first;
    break;

  case 'all':
    // sin filtro adicional
    break;

  case 'today':
  default:
    $whereParts[] = "DATE(s.fecha_registro) = CURDATE()";
    break;
}

$where = $whereParts ? ('WHERE '.implode(' AND ',$whereParts)) : '';

$sql = "SELECT s.id, s.fecha_registro, s.resultado, s.proxima_cita,
               p.nombre, p.apellido
        FROM seguimientos s
        JOIN pacientes p ON p.id = s.paciente_id
        {$where}
        ORDER BY s.fecha_registro DESC, s.id DESC";

$stmt = $conexion->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<div class="topbar">
  <h2 class="mb-0">Seguimientos</h2>
  <div class="d-flex gap-2">
    <form class="d-flex gap-2" method="get" action="/seguimientos/listar.php">
      <input class="form-control" style="min-width:260px" type="search" name="q"
             placeholder="Buscar por paciente, resultado o fecha (YYYY-MM-DD)"
             value="<?= htmlspecialchars($q) ?>">

      <select class="form-select" name="scope" onchange="this.form.submit()">
        <option value="today" <?= $scope==='today'?'selected':'' ?>>Hoy</option>
        <option value="day"   <?= $scope==='day'?'selected':'' ?>>Un día</option>
        <option value="month" <?= $scope==='month'?'selected':'' ?>>Un mes</option>
        <option value="all"   <?= $scope==='all'?'selected':'' ?>>Todos</option>
      </select>

      <input class="form-control" type="date"  name="day"   value="<?= htmlspecialchars($day) ?>"   <?= $scope==='day'?'':'disabled' ?>>
      <input class="form-control" type="month" name="month" value="<?= htmlspecialchars($month) ?>" <?= $scope==='month'?'':'disabled' ?>>

      <button class="btn btn-outline-secondary" type="submit"><i class="bi bi-search"></i></button>
      <?php if($q!=='' || $scope!=='today'): ?>
        <a class="btn btn-outline-dark" href="/seguimientos/listar.php">Limpiar</a>
      <?php endif; ?>
    </form>

    <a class="btn btn-primary" href="/seguimientos/crear.php">
      <i class="bi bi-plus-circle"></i> Nuevo
    </a>
  </div>
</div>

<div class="small-muted mb-2">Se muestran por defecto los <strong>seguimientos de hoy</strong>.</div>

<div class="table-card">
  <div class="table-responsive">
    <table class="table table-hover table-bordered align-middle">
      <thead class="table-primary">
        <tr>
          <th>Fecha</th><th>Paciente</th><th>Resultado</th><th>Próxima cita</th>
          <th class="no-print" style="width:230px">Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($rows as $r): ?>
          <tr>
            <td><?= htmlspecialchars($r['fecha_registro']) ?></td>
            <td><?= htmlspecialchars($r['nombre'].' '.$r['apellido']) ?></td>
            <td><?= nl2br(htmlspecialchars($r['resultado'])) ?></td>
            <td><?= htmlspecialchars($r['proxima_cita']) ?></td>
            <td class="no-print">
              <a class="btn btn-sm btn-outline-secondary" href="/seguimientos/editar.php?id=<?= $r['id'] ?>">Editar</a>
              <a class="btn btn-sm btn-outline-danger" href="/seguimientos/eliminar.php?id=<?= $r['id'] ?>" onclick="return confirm('¿Eliminar seguimiento?')">Eliminar</a>
              <a class="btn btn-sm btn-outline-primary" href="/seguimientos/constancia.php?id=<?= $r['id'] ?>"><i class="bi bi-printer"></i> Constancia</a>
            </td>
          </tr>
        <?php endforeach; if(empty($rows)): ?>
          <tr><td colspan="5" class="text-center text-muted">Sin resultados</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <!-- Filtros rápidos abajo -->
  <form class="d-flex gap-2 mt-3 no-print" method="get" action="/seguimientos/listar.php">
    <input type="hidden" name="q" value="<?= htmlspecialchars($q) ?>">
    <label class="form-label m-0 align-self-center">Ver por día/mes:</label>
    <input class="form-control" type="date"  name="day" value="<?= htmlspecialchars($day) ?>">
    <input type="hidden" name="scope" value="day">
    <button class="btn btn-secondary">Ver día</button>

    <div class="vr mx-2"></div>

    <input class="form-control" type="month" name="month" value="<?= htmlspecialchars($month) ?>">
    <input type="hidden" name="scope" value="month">
    <button class="btn btn-secondary">Ver mes</button>

    <a class="btn btn-outline-dark ms-auto" href="/seguimientos/listar.php">Hoy</a>
    <a class="btn btn-outline-dark" href="/seguimientos/listar.php?scope=all">Todos</a>
  </form>
</div>

<?php include __DIR__.'/../templates/footer.php'; ?>
