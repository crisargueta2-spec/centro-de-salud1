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

// 🔍 Búsqueda (incluye medicamentos)
if ($q!=='') {
  $like = '%'.str_replace(' ','%',$q).'%';
  $whereParts[] = "(p.nombre LIKE ? OR p.apellido LIKE ? OR CONCAT(p.nombre,' ',p.apellido) LIKE ?
                    OR t.diagnostico LIKE ? OR u.username LIKE ?
                    OR r.observaciones LIKE ? OR EXISTS (
                        SELECT 1 FROM receta_items ri 
                        WHERE ri.receta_id=r.id 
                        AND (ri.medicamento LIKE ? OR ri.indicaciones LIKE ?)
                    ))";
  $params = array_merge($params, [$like,$like,$like,$like,$like,$like,$like,$like]);
}

// 🗓️ Filtro de fecha con validación segura
switch ($scope) {
  case 'day':
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $day)) {
      $whereParts[] = "DATE(r.fecha_emision) = ?";
      $params[] = $day;
    }
    break;
  case 'month':
    if (preg_match('/^\d{4}-\d{2}$/', $month)) {
      $first = $month.'-01';
      $whereParts[] = "DATE(r.fecha_emision) BETWEEN ? AND LAST_DAY(?)";
      $params[] = $first; $params[] = $first;
    }
    break;
  case 'all':
    break;
  default:
    $whereParts[] = "DATE(r.fecha_emision) = CURDATE()";
    break;
}
$where = $whereParts ? ('WHERE '.implode(' AND ',$whereParts)) : '';

$sql = "SELECT r.id, r.fecha_emision, r.observaciones,
               p.nombre, p.apellido,
               t.diagnostico,
               u.username AS medico
        FROM recetas r
        JOIN pacientes p ON p.id = r.paciente_id
        LEFT JOIN tratamientos t ON t.id = r.tratamiento_id
        LEFT JOIN usuarios u ON u.id = r.medico_id
        {$where}
        ORDER BY r.fecha_emision DESC, r.id DESC";
$stmt=$conn->prepare($sql); 
$stmt->execute($params); 
$rows=$stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<div class="topbar">
  <h2 class="mb-0">Recetas</h2>
  <form class="d-flex gap-2" method="get" action="recetas/listar.php">
    <input class="form-control" style="min-width:260px" type="search" name="q" placeholder="Buscar por paciente, diagnóstico, médico, medicamento, fecha..." value="<?= htmlspecialchars($q) ?>">

    <select class="form-select" name="scope" onchange="this.form.submit()">
      <option value="today" <?= $scope==='today'?'selected':'' ?>>Hoy</option>
      <option value="day"   <?= $scope==='day'?'selected':'' ?>>Un día</option>
      <option value="month" <?= $scope==='month'?'selected':'' ?>>Un mes</option>
      <option value="all"   <?= $scope==='all'?'selected':'' ?>>Todos</option>
    </select>

    <input class="form-control" type="date" name="day" value="<?= htmlspecialchars($day) ?>" <?= $scope==='day'?'':'disabled' ?>>
    <input class="form-control" type="month" name="month" value="<?= htmlspecialchars($month) ?>" <?= $scope==='month'?'':'disabled' ?>>

    <button class="btn btn-outline-secondary" type="submit"><i class="bi bi-search"></i></button>
    <?php if($q!=='' || $scope!=='today'): ?>
      <a class="btn btn-outline-dark" href="recetas/listar.php">Limpiar</a>
    <?php endif; ?>
  </form>
</div>

<div class="small-muted mb-2">Se muestran por defecto las <b>recetas emitidas hoy</b>.</div>

<div class="table-card">
  <div class="table-responsive">
    <table class="table table-hover table-bordered align-middle">
      <thead class="table-primary">
        <tr>
          <th>Fecha</th>
          <th>Paciente</th>
          <th>Diagnóstico (si aplica)</th>
          <th>Médico</th>
          <th>Observaciones</th>
          <th style="width:220px" class="no-print">Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($rows as $r): ?>
          <tr>
            <td><?= htmlspecialchars($r['fecha_emision']) ?></td>
            <td><?= htmlspecialchars($r['nombre'].' '.$r['apellido']) ?></td>
            <td><?= htmlspecialchars($r['diagnostico'] ?? '') ?></td>
            <td><?= htmlspecialchars($r['medico'] ?? '') ?></td>
            <td><?= nl2br(htmlspecialchars($r['observaciones'] ?? '')) ?></td>
            <td class="text-center no-print">
  <a class="btn btn-sm btn-outline-primary" href="/recetas/ver.php?id=<?= $r['id'] ?>">Ver / Imprimir</a>
  <a class="btn btn-sm btn-outline-secondary" href="/recetas/editar.php?id=<?= $r['id'] ?>">Editar</a>
  <a class="btn btn-sm btn-outline-danger" href="/recetas/eliminar.php?id=<?= $r['id'] ?>"
     onclick="return confirm('¿Eliminar receta definitivamente?')">Eliminar</a>
</td>
          </tr>
        <?php endforeach; if(empty($rows)): ?>
          <tr><td colspan="6" class="text-center text-muted">Sin resultados</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <!-- Filtros rápidos abajo -->
  <form class="d-flex gap-2 mt-3 no-print" method="get" action="recetas/listar.php">
    <input type="hidden" name="q" value="<?= htmlspecialchars($q) ?>">
    <label class="form-label m-0 align-self-center">Ver por día/mes:</label>
    <input class="form-control" type="date"  name="day" value="<?= htmlspecialchars($day) ?>">
    <input type="hidden" name="scope" value="day">
    <button class="btn btn-secondary">Ver día</button>

    <div class="vr mx-2"></div>

    <input class="form-control" type="month" name="month" value="<?= htmlspecialchars($month) ?>">
    <input type="hidden" name="scope" value="month">
    <button class="btn btn-secondary">Ver mes</button>

    <a class="btn btn-outline-dark ms-auto" href="recetas/listar.php">Hoy</a>
    <a class="btn btn-outline-dark" href="recetas/listar.php?scope=all">Todos</a>
  </form>
</div>

<?php include __DIR__.'/../templates/footer.php'; ?>
