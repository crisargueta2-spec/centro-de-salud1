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
                    OR a.tipo LIKE ? OR a.descripcion LIKE ?
                    OR DATE(a.fecha_registro)=?)";
  $params = array_merge($params, [$like,$like,$like,$like,$like,$q]);
}

// Fecha (por defecto hoy, por fecha_registro)
switch ($scope) {
  case 'day':
    $whereParts[] = "DATE(a.fecha_registro) = ?";
    $params[] = preg_match('/^\d{4}-\d{2}-\d{2}$/',$day)?$day:date('Y-m-d');
    break;
  case 'month':
    $first = preg_match('/^\d{4}-\d{2}$/',$month)?($month.'-01'):(date('Y-m').'-01');
    $whereParts[] = "DATE(a.fecha_registro) BETWEEN ? AND LAST_DAY(?)";
    $params[] = $first; $params[] = $first;
    break;
  case 'all':
    break;
  case 'today':
  default:
    $whereParts[] = "DATE(a.fecha_registro) = CURDATE()";
    break;
}
$where = $whereParts ? ('WHERE '.implode(' AND ',$whereParts)) : '';

$sql = "SELECT a.id, a.tipo, a.descripcion, a.fecha_registro,
               p.nombre, p.apellido
        FROM antecedentes a
        JOIN pacientes p ON p.id = a.paciente_id
        {$where}
        ORDER BY a.fecha_registro DESC, a.id DESC";
$stmt=$conn->prepare($sql); $stmt->execute($params); $rows=$stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<div class="topbar">
  <h2 class="mb-0">Antecedentes</h2>
  <div class="d-flex gap-2">
    <form class="d-flex gap-2" method="get" action="antecedentes/listar.php">
      <input class="form-control" style="min-width:260px" type="search" name="q" placeholder="Buscar por paciente, tipo, descripción, fecha..."
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
        <a class="btn btn-outline-dark" href="antecedentes/listar.php">Limpiar</a>
      <?php endif; ?>
    </form>
    <a class="btn btn-primary" href="antecedentes/crear.php"><i class="bi bi-plus-circle"></i> Nuevo</a>
  </div>
</div>

<div class="small-muted mb-2">Se muestran por defecto los **antecedentes de hoy**.</div>

<div class="table-card">
  <div class="table-responsive">
    <table class="table table-hover table-bordered align-middle">
      <thead class="table-primary">
        <tr>
          <th>Fecha</th><th>Paciente</th><th>Tipo</th><th>Descripción</th>
          <th class="no-print" style="width:150px">Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($rows as $r): ?>
          <tr>
            <td><?= htmlspecialchars($r['fecha_registro']) ?></td>
            <td><?= htmlspecialchars($r['nombre'].' '.$r['apellido']) ?></td>
            <td><span class="badge text-bg-secondary"><?= htmlspecialchars($r['tipo']) ?></span></td>
            <td><?= nl2br(htmlspecialchars($r['descripcion'])) ?></td>
            <td class="no-print">
              <a class="btn btn-sm btn-outline-secondary" href="antecedentes/editar.php?id=<?= $r['id'] ?>">Editar</a>
              <a class="btn btn-sm btn-outline-danger" href="antecedentes/eliminar.php?id=<?= $r['id'] ?>" onclick="return confirm('¿Eliminar antecedente?')">Eliminar</a>
            </td>
          </tr>
        <?php endforeach; if(empty($rows)): ?>
          <tr><td colspan="5" class="text-center text-muted">Sin resultados</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <!-- Filtros rápidos abajo -->
  <form class="d-flex gap-2 mt-3 no-print" method="get" action="antecedentes/listar.php">
    <input type="hidden" name="q" value="<?= htmlspecialchars($q) ?>">
    <label class="form-label m-0 align-self-center">Ver por día/mes:</label>
    <input class="form-control" type="date"  name="day" value="<?= htmlspecialchars($day) ?>">
    <input type="hidden" name="scope" value="day">
    <button class="btn btn-secondary">Ver día</button>

    <div class="vr mx-2"></div>

    <input class="form-control" type="month" name="month" value="<?= htmlspecialchars($month) ?>">
    <input type="hidden" name="scope" value="month">
    <button class="btn btn-secondary">Ver mes</button>

    <a class="btn btn-outline-dark ms-auto" href="antecedentes/listar.php">Hoy</a>
    <a class="btn btn-outline-dark" href="antecedentes/listar.php?scope=all">Todos</a>
  </form>
</div>
<?php include __DIR__.'/../templates/footer.php'; ?>
