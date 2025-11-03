<?php
require_once __DIR__ . '/../includes/auth.php';
require_role(['admin','secretaria']);
require_once __DIR__ . '/../includes/conexion.php';
include __DIR__ . '/../templates/header.php';

$q      = trim($_GET['q'] ?? '');
$scope  = $_GET['scope'] ?? 'today';
$day    = $_GET['day']   ?? date('Y-m-d');
$month  = $_GET['month'] ?? date('Y-m');

$whereParts = [];
$params = [];

// Búsqueda
if ($q !== '') {
  $like = '%'.str_replace(' ','%',$q).'%';
  $parts = [
    "(p.nombre LIKE ? OR p.apellido LIKE ? OR CONCAT(p.nombre,' ',p.apellido) LIKE ?)",
    "(p.genero LIKE ?)",
    "(p.motivo LIKE ?)"
  ];

  // Si el texto parece una fecha válida (YYYY-MM-DD), solo entonces filtra por fecha
  if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $q)) {
    $parts[] = "(DATE(p.fecha_referencia)=? OR DATE(p.fecha_nacimiento)=?)";
    $params[] = $q;
    $params[] = $q;
  }

  $params = array_merge([$like,$like,$like,$like,$like], $params);

  // Si el texto es numérico pequeño, buscar por edad
  if (ctype_digit($q) && (int)$q <= 120) {
    $parts[] = "TIMESTAMPDIFF(YEAR,p.fecha_nacimiento,CURDATE()) = ?";
    $params[] = (int)$q;
  }

  $whereParts[] = '(' . implode(' OR ', $parts) . ')';
}

// Filtro de fecha
switch ($scope) {
  case 'day':
    $whereParts[] = "DATE(p.fecha_referencia) = ?";
    $params[] = preg_match('/^\d{4}-\d{2}-\d{2}$/', $day) ? $day : date('Y-m-d');
    break;
  case 'month':
    $first = preg_match('/^\d{4}-\d{2}$/', $month) ? ($month.'-01') : (date('Y-m').'-01');
    $whereParts[] = "DATE(p.fecha_referencia) BETWEEN ? AND LAST_DAY(?)";
    $params[] = $first; $params[] = $first;
    break;
  case 'all':
    break;
  default:
    $whereParts[] = "DATE(p.fecha_referencia) = CURDATE()";
    break;
}

$where = $whereParts ? ('WHERE '.implode(' AND ',$whereParts)) : '';

$sql = "SELECT p.id,p.nombre,p.apellido,p.genero,p.fecha_nacimiento,p.fecha_referencia,p.motivo
        FROM pacientes p
        {$where}
        ORDER BY p.id DESC";
$stmt = $conn->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<div class="topbar">
  <h2 class="mb-0">Pacientes</h2>
  <div class="d-flex gap-2">
    <form class="d-flex gap-2" method="get" action="pacientes/listar.php">
      <input class="form-control" style="min-width:260px" type="search" name="q" placeholder="Buscar por nombre, motivo, edad o fecha (YYYY-MM-DD)..."
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
        <a class="btn btn-outline-dark" href="pacientes/listar.php">Limpiar</a>
      <?php endif; ?>
    </form>
    <a class="btn btn-primary" href="pacientes/crear.php"><i class="bi bi-plus-circle"></i> Nuevo</a>
  </div>
</div>

<div class="small-muted mb-2">Se muestran por defecto los pacientes ingresados hoy.</div>

<div class="table-card">
  <div class="table-responsive">
    <table class="table table-hover table-bordered align-middle">
      <thead class="table-primary">
        <tr>
          <th>Paciente</th><th>Género</th><th>Nacimiento</th><th>Ingreso</th><th>Motivo</th>
          <th class="no-print" style="width:150px">Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($rows as $r): ?>
          <tr>
            <td><?= htmlspecialchars($r['nombre'].' '.$r['apellido']) ?></td>
            <td><?= htmlspecialchars($r['genero']) ?></td>
            <td><?= htmlspecialchars($r['fecha_nacimiento']) ?></td>
            <td><?= htmlspecialchars($r['fecha_referencia']) ?></td>
            <td><?= nl2br(htmlspecialchars($r['motivo'])) ?></td>
            <td class="no-print">
              <a class="btn btn-sm btn-outline-secondary" href="pacientes/editar.php?id=<?= $r['id'] ?>">Editar</a>
              <a class="btn btn-sm btn-outline-danger" href="pacientes/eliminar.php?id=<?= $r['id'] ?>" onclick="return confirm('¿Eliminar paciente?')">Eliminar</a>
            </td>
          </tr>
        <?php endforeach; if(empty($rows)): ?>
          <tr><td colspan="6" class="text-center text-muted">Sin resultados</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php include __DIR__.'/../templates/footer.php'; ?>

