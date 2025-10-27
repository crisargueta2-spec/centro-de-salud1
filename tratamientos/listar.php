<?php
require_once __DIR__.'/../includes/auth.php';
require_role(['admin','secretaria','medico']);
require_once __DIR__.'/../includes/conexion.php';
include __DIR__.'/../templates/header.php';

$q      = trim($_GET['q'] ?? '');
$scope  = $_GET['scope'] ?? 'today';
$day    = $_GET['day']   ?? date('Y-m-d');
$month  = $_GET['month'] ?? date('Y-m');

$whereParts=[]; $params=[];

// Búsqueda
if ($q!=='') {
  $like = '%'.str_replace(' ','%',$q).'%';
  $whereParts[] = "(p.nombre LIKE ? OR p.apellido LIKE ? OR CONCAT(p.nombre,' ',p.apellido) LIKE ?
                    OR t.diagnostico LIKE ? OR t.plan LIKE ? OR t.estado LIKE ?
                    OR DATE(t.fecha_inicio)=? OR DATE(t.fecha_fin)=?)";
  $params = array_merge($params, [$like,$like,$like,$like,$like,$like,$q,$q]);
}

// Fecha (por defecto hoy, por fecha_inicio)
switch ($scope) {
  case 'day':
    $whereParts[] = "DATE(t.fecha_inicio) = ?";
    $params[] = preg_match('/^\d{4}-\d{2}-\d{2}$/',$day)?$day:date('Y-m-d');
    break;
  case 'month':
    $first = preg_match('/^\d{4}-\d{2}$/',$month)?($month.'-01'):(date('Y-m').'-01');
    $whereParts[] = "DATE(t.fecha_inicio) BETWEEN ? AND LAST_DAY(?)";
    $params[] = $first; $params[] = $first;
    break;
  case 'all':
    break;
  case 'today':
  default:
    $whereParts[] = "DATE(t.fecha_inicio) = CURDATE()";
    break;
}
$where = $whereParts ? ('WHERE '.implode(' AND ',$whereParts)) : '';

$sql = "SELECT t.id, t.paciente_id, t.diagnostico, COALESCE(t.plan, t.tratamiento) AS plan_texto,
               t.estado, t.fecha_inicio, t.fecha_fin,
               p.nombre, p.apellido
        FROM tratamientos t
        JOIN pacientes p ON p.id = t.paciente_id
        {$where}
        ORDER BY COALESCE(t.fecha_inicio, t.id) DESC";
$stmt=$conn->prepare($sql); $stmt->execute($params); $rows=$stmt->fetchAll(PDO::FETCH_ASSOC);

$canWrite = in_array(strtolower(user()['rol'] ?? (user()['role'] ?? '')), ['admin','medico']);
?>
<div class="topbar">
  <h2 class="mb-0">Tratamientos</h2>
  <div class="d-flex gap-2">
    <form class="d-flex gap-2" method="get" action="tratamientos/listar.php">
      <input class="form-control" style="min-width:260px" type="search" name="q" placeholder="Buscar por paciente, diagnóstico, plan, estado, fecha..."
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
        <a class="btn btn-outline-dark" href="tratamientos/listar.php">Limpiar</a>
      <?php endif; ?>
    </form>
    <?php if ($canWrite): ?>
      <a href="tratamientos/crear.php" class="btn btn-primary"><i class="bi bi-plus-circle"></i> Nuevo</a>
    <?php endif; ?>
  </div>
</div>

<div class="small-muted mb-2">Por defecto se muestran los **tratamientos con inicio hoy**.</div>

<div class="table-card">
  <div class="table-responsive">
    <table class="table table-hover table-bordered align-middle">
      <thead class="table-primary">
        <tr>
          <th>Paciente</th><th>Diagnóstico</th><th>Plan</th><th>Estado</th><th>Inicio</th><th>Fin</th>
          <th class="no-print" style="width:260px">Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($rows as $r): ?>
          <tr>
            <td><?= htmlspecialchars($r['nombre'].' '.$r['apellido']) ?></td>
            <td><?= htmlspecialchars($r['diagnostico']) ?></td>
            <td><?= nl2br(htmlspecialchars($r['plan_texto'] ?? '')) ?></td>
            <td><span class="badge text-bg-<?= $r['estado']==='activo'?'success':($r['estado']==='suspendido'?'warning':'secondary') ?>"><?= htmlspecialchars($r['estado']) ?></span></td>
            <td><?= htmlspecialchars($r['fecha_inicio']) ?></td>
            <td><?= htmlspecialchars($r['fecha_fin']) ?></td>
            <td class="no-print">
              <?php if ($canWrite): ?>
                <a class="btn btn-sm btn-outline-secondary" href="tratamientos/editar.php?id=<?= $r['id'] ?>"><i class="bi bi-pencil"></i> Editar</a>
                <a class="btn btn-sm btn-outline-danger" href="tratamientos/eliminar.php?id=<?= $r['id'] ?>" onclick="return confirm('¿Eliminar tratamiento?')"><i class="bi bi-trash"></i></a>
                <a class="btn btn-sm btn-outline-primary" href="recetas/crear.php?paciente_id=<?= $r['paciente_id'] ?>&tratamiento_id=<?= $r['id'] ?>"><i class="bi bi-file-medical"></i> Receta</a>
              <?php else: ?><span class="text-muted">—</span><?php endif; ?>
            </td>
          </tr>
        <?php endforeach; if(empty($rows)): ?>
          <tr><td colspan="7" class="text-center text-muted">Sin resultados</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <!-- Filtros rápidos abajo -->
  <form class="d-flex gap-2 mt-3 no-print" method="get" action="tratamientos/listar.php">
    <input type="hidden" name="q" value="<?= htmlspecialchars($q) ?>">
    <label class="form-label m-0 align-self-center">Ver por día/mes:</label>
    <input class="form-control" type="date"  name="day" value="<?= htmlspecialchars($day) ?>">
    <input type="hidden" name="scope" value="day">
    <button class="btn btn-secondary">Ver día</button>

    <div class="vr mx-2"></div>

    <input class="form-control" type="month" name="month" value="<?= htmlspecialchars($month) ?>">
    <input type="hidden" name="scope" value="month">
    <button class="btn btn-secondary">Ver mes</button>

    <a class="btn btn-outline-dark ms-auto" href="tratamientos/listar.php">Hoy</a>
    <a class="btn btn-outline-dark" href="tratamientos/listar.php?scope=all">Todos</a>
  </form>
</div>
<?php include __DIR__.'/../templates/footer.php'; ?>
