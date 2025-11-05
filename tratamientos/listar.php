<?php
require_once __DIR__.'/../includes/auth.php';
require_role(['admin','secretaria','medico']);
require_once __DIR__.'/../includes/conexion.php';
include __DIR__.'/../templates/header.php';

$q      = trim($_GET['q'] ?? '');
$scope  = $_GET['scope'] ?? 'today';
$day    = $_GET['day']   ?? date('Y-m-d');
$month  = $_GET['month'] ?? date('Y-m');

$whereParts=[]; 
$params=[];

// ✅ Búsqueda segura
if ($q !== '') {
    $like = '%' . str_replace(' ', '%', $q) . '%';

    $parts = [
        "(p.nombre LIKE ? OR p.apellido LIKE ? OR CONCAT(p.nombre,' ',p.apellido) LIKE ?)",
        "(t.diagnostico LIKE ? OR t.plan LIKE ? OR t.estado LIKE ?)"
    ];

    // Comparar fechas solo si $q es una fecha válida
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $q)) {
        $parts[] = "(DATE(t.fecha_inicio)=? OR DATE(t.fecha_fin)=?)";
        $params = array_merge($params, [$like,$like,$like,$like,$like,$like,$q,$q]);
    } else {
        $params = array_merge($params, [$like,$like,$like,$like,$like,$like]);
    }

    $whereParts[] = '(' . implode(' OR ', $parts) . ')';
}

// ✅ Filtro por fechas
switch ($scope) {
    case 'day':
        $whereParts[] = "DATE(t.fecha_inicio) = ?";
        $params[] = $day;
        break;

    case 'month':
        $first = $month . "-01";
        $whereParts[] = "DATE(t.fecha_inicio) BETWEEN ? AND LAST_DAY(?)";
        $params[] = $first;
        $params[] = $first;
        break;

    case 'all':
        break;

    default:
    case 'today':
        $whereParts[] = "DATE(t.fecha_inicio) = CURDATE()";
        break;
}

$where = $whereParts ? ("WHERE ".implode(" AND ", $whereParts)) : "";

// ✅ Consulta final
$sql = "SELECT 
            t.id, t.paciente_id, t.diagnostico,
            COALESCE(t.plan,'') AS plan_texto,
            t.estado, t.fecha_inicio, t.fecha_fin,
            p.nombre, p.apellido
        FROM tratamientos t
        JOIN pacientes p ON p.id = t.paciente_id
        $where
        ORDER BY t.fecha_inicio DESC, t.id DESC";

$stmt = $conexion->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

// permisos
$rol = strtolower(user()['rol'] ?? '');
$canWrite = in_array($rol, ['admin','medico']);
?>
<div class="topbar">
  <h2 class="mb-0">Tratamientos</h2>
  <div class="d-flex gap-2">
    
    <!-- ✅ BÚSQUEDA CORRECTA -->
    <form class="d-flex gap-2" method="get">
      <input class="form-control" style="min-width:260px"
             type="search" name="q"
             placeholder="Buscar por paciente, diagnóstico, estado..."
             value="<?= htmlspecialchars($q) ?>">

      <select class="form-select" name="scope" onchange="this.form.submit()">
        <option value="today" <?= $scope==='today'?'selected':'' ?>>Hoy</option>
        <option value="day"   <?= $scope==='day'?'selected':'' ?>>Un día</option>
        <option value="month" <?= $scope==='month'?'selected':'' ?>>Un mes</option>
        <option value="all"   <?= $scope==='all'?'selected':'' ?>>Todos</option>
      </select>

      <input class="form-control" type="date" name="day"
             value="<?= htmlspecialchars($day) ?>"
             <?= $scope==='day'?'':'disabled' ?>>

      <input class="form-control" type="month" name="month"
             value="<?= htmlspecialchars($month) ?>"
             <?= $scope==='month'?'':'disabled' ?>>

      <button class="btn btn-outline-secondary"><i class="bi bi-search"></i></button>

      <?php if($q!=='' || $scope!=='today'): ?>
        <a class="btn btn-outline-dark" href="listar.php">Limpiar</a>
      <?php endif; ?>
    </form>

    <?php if ($canWrite): ?>
      <a href="crear.php" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Nuevo
      </a>
    <?php endif; ?>
  </div>
</div>

<div class="table-card mt-3">
  <div class="table-responsive">
    <table class="table table-bordered table-hover align-middle">
      <thead class="table-primary">
        <tr>
          <th>Paciente</th>
          <th>Diagnóstico</th>
          <th>Plan</th>
          <th>Estado</th>
          <th>Inicio</th>
          <th>Fin</th>
          <th class="no-print">Acciones</th>
        </tr>
      </thead>
      <tbody>

        <?php foreach($rows as $r): ?>
        <tr>
          <td><?= htmlspecialchars($r['nombre'].' '.$r['apellido']) ?></td>
          <td><?= htmlspecialchars($r['diagnostico']) ?></td>
          <td><?= nl2br(htmlspecialchars($r['plan_texto'])) ?></td>
          <td><?= htmlspecialchars($r['estado']) ?></td>
          <td><?= htmlspecialchars($r['fecha_inicio']) ?></td>
          <td><?= htmlspecialchars($r['fecha_fin']) ?></td>
          <td class="no-print">
            <?php if ($canWrite): ?>
              <a href="editar.php?id=<?= $r['id'] ?>" class="btn btn-sm btn-outline-secondary">Editar</a>
              <a href="eliminar.php?id=<?= $r['id'] ?>" 
                 class="btn btn-sm btn-outline-danger"
                 onclick="return confirm('¿Eliminar tratamiento?')">Eliminar</a>
            <?php endif; ?>
          </td>
        </tr>
        <?php endforeach; ?>

        <?php if(empty($rows)): ?>
        <tr><td colspan="7" class="text-center text-muted">Sin resultados</td></tr>
        <?php endif; ?>

      </tbody>
    </table>
  </div>

  <!-- ✅ FILTROS RÁPIDOS -->
  <form class="d-flex gap-2 mt-3 no-print" method="get">
    <input type="hidden" name="q" value="<?= htmlspecialchars($q) ?>">
    <label class="form-label m-0 align-self-center">Ver por día/mes:</label>

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
