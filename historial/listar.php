<?php
require_once __DIR__.'/../includes/auth.php';
require_role(['admin','secretaria','medico']);
require_once __DIR__.'/../includes/conexion.php';
include __DIR__.'/../templates/header.php';

$q      = trim($_GET['q'] ?? '');
$scope  = $_GET['scope'] ?? 'today';  // today | day | month | all
$day    = $_GET['day']   ?? date('Y-m-d');
$month  = $_GET['month'] ?? date('Y-m');

$whereParts = [];
$params = [];

/* ==============================
   🔍 BÚSQUEDA GENERAL
============================== */
if ($q !== '') {
  $like = '%'.str_replace(' ', '%', $q).'%';
  $parts = [
    "(p.nombre LIKE ? OR p.apellido LIKE ? OR CONCAT(p.nombre,' ',p.apellido) LIKE ?)",
    "(p.motivo LIKE ?)"
  ];
  $params = array_merge($params, [$like,$like,$like,$like]);

  // Fecha exacta (si el texto parece una fecha válida)
  if (preg_match('/^\d{4}-\d{2}-\d{2}$/',$q)) {
    $parts[] = "(DATE(p.fecha_referencia)=?)";
    $params[] = $q;
  }

  // Edad (si el texto es un número de edad válido)
  if (ctype_digit($q) && (int)$q <= 120) {
    $parts[] = "(TIMESTAMPDIFF(YEAR, p.fecha_nacimiento, CURDATE()) = ?)";
    $params[] = (int)$q;
  }

  $whereParts[] = '(' . implode(' OR ', $parts) . ')';
}

/* ==============================
   📅 FILTRO POR FECHAS
============================== */
switch ($scope) {
  case 'day':
    $whereParts[] = "DATE(COALESCE(p.fecha_referencia, p.created_at, NOW())) = ?";
    $params[] = preg_match('/^\d{4}-\d{2}-\d{2}$/',$day) ? $day : date('Y-m-d');
    break;

  case 'month':
    $first = preg_match('/^\d{4}-\d{2}$/',$month) ? ($month.'-01') : (date('Y-m').'-01');
    $whereParts[] = "DATE(COALESCE(p.fecha_referencia, p.created_at, NOW())) BETWEEN ? AND LAST_DAY(?)";
    $params[] = $first; 
    $params[] = $first;
    break;

  case 'all':
    // sin filtro (mostrar todos)
    break;

  case 'today':
  default:
    // Mostrar por defecto los ingresados hoy o recientemente (últimos 3 días)
    $whereParts[] = "DATE(COALESCE(p.fecha_referencia, p.created_at, NOW())) >= CURDATE() - INTERVAL 3 DAY";
    break;
}

$where = $whereParts ? ('WHERE '.implode(' AND ',$whereParts)) : '';

/* ==============================
   🧠 CONSULTA PRINCIPAL
============================== */
$sql = "
SELECT
  p.id,
  p.nombre, p.apellido, p.genero, p.fecha_nacimiento,
  p.fecha_referencia AS ingreso,
  p.motivo AS sintomas,
  s2.resultado AS ultimo_resultado,
  s2.fecha_registro AS ultima_fecha,
  (SELECT COUNT(*) FROM asignaciones a WHERE a.paciente_id = p.id) AS total_asignaciones,
  (SELECT COUNT(*) FROM seguimientos s WHERE s.paciente_id = p.id) AS total_seguimientos
FROM pacientes p
LEFT JOIN (
  SELECT s.*
  FROM seguimientos s
  INNER JOIN (
    SELECT paciente_id, MAX(fecha_registro) AS max_fecha
    FROM seguimientos GROUP BY paciente_id
  ) t ON t.paciente_id = s.paciente_id AND t.max_fecha = s.fecha_registro
) s2 ON s2.paciente_id = p.id
{$where}
ORDER BY p.id DESC
";

$stmt = $conexion->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<style>
@media print {
  .sidebar,.topbar,.toggle-btn,.no-print{display:none!important}
  .content{padding:0!important}.table-card{box-shadow:none!important}
}
.small-muted{font-size:.9rem;color:#6c757d}
</style>

<div class="topbar">
  <h2 class="mb-0">Historial médico</h2>
  <form class="d-flex gap-2" method="get" action="../historial/listar.php">
    <input class="form-control" style="min-width:260px" type="search" name="q"
           placeholder="Buscar por nombre, síntoma, fecha (YYYY-MM-DD), edad..."
           value="<?= htmlspecialchars($q) ?>">

    <select class="form-select" name="scope" onchange="this.form.submit()">
      <option value="today" <?= $scope==='today'?'selected':'' ?>>Recientes</option>
      <option value="day"   <?= $scope==='day'?'selected':'' ?>>Un día</option>
      <option value="month" <?= $scope==='month'?'selected':'' ?>>Un mes</option>
      <option value="all"   <?= $scope==='all'?'selected':'' ?>>Todos</option>
    </select>

    <input class="form-control" type="date" name="day" value="<?= htmlspecialchars($day) ?>" <?= $scope==='day'?'':'disabled' ?>>
    <input class="form-control" type="month" name="month" value="<?= htmlspecialchars($month) ?>" <?= $scope==='month'?'':'disabled' ?>>

    <button class="btn btn-outline-secondary" type="submit"><i class="bi bi-search"></i></button>
    <?php if($q!=='' || $scope!=='today'): ?>
      <a class="btn btn-outline-dark" href="../historial/listar.php">Limpiar</a>
    <?php endif; ?>
  </form>
</div>

<div class="small-muted mb-2">Por defecto se muestran los **pacientes recientes (últimos 3 días)**.</div>

<div class="table-card">
  <div class="table-responsive">
    <table class="table table-hover table-bordered align-middle">
      <thead class="table-primary">
        <tr>
          <th>Paciente</th>
          <th>Ingreso</th>
          <th>Síntomas / Motivo</th>
          <th>Último seguimiento</th>
          <th>Asignaciones</th>
          <th>Seguimientos</th>
          <th class="no-print">Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($rows as $r): ?>
          <tr>
            <td><?= htmlspecialchars($r['nombre'].' '.$r['apellido']) ?></td>
            <td><?= htmlspecialchars($r['ingreso'] ?: '—') ?></td>
            <td><?= nl2br(htmlspecialchars($r['sintomas'] ?: '—')) ?></td>
            <td>
              <?php if (!empty($r['ultimo_resultado'])): ?>
                <div><small class="text-muted"><?= htmlspecialchars($r['ultima_fecha']) ?></small></div>
                <?= nl2br(htmlspecialchars($r['ultimo_resultado'])) ?>
              <?php else: ?><span class="text-muted">—</span><?php endif; ?>
            </td>
            <td class="text-center"><?= (int)$r['total_asignaciones'] ?></td>
            <td class="text-center"><?= (int)$r['total_seguimientos'] ?></td>
            <td class="no-print text-center">
              <a class="btn btn-sm btn-outline-primary" href="../historial/ficha.php?id=<?= $r['id'] ?>">Ver ficha</a>
            </td>
          </tr>
        <?php endforeach; if(empty($rows)): ?>
          <tr><td colspan="7" class="text-center text-muted">Sin resultados</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <!-- 📅 Atajo inferior -->
  <form class="d-flex gap-2 mt-3 no-print" method="get" action="../historial/listar.php">
    <input type="hidden" name="q" value="<?= htmlspecialchars($q) ?>">
    <label class="form-label m-0 align-self-center">Ver por día/mes:</label>

    <input class="form-control" type="date" name="day" value="<?= htmlspecialchars($day) ?>">
    <input type="hidden" name="scope" value="day">
    <button class="btn btn-secondary">Ver día</button>

    <div class="vr mx-2"></div>

    <input class="form-control" type="month" name="month" value="<?= htmlspecialchars($month) ?>">
    <input type="hidden" name="scope" value="month">
    <button class="btn btn-secondary">Ver mes</button>

    <a class="btn btn-outline-dark ms-auto" href="../historial/listar.php">Recientes</a>
    <a class="btn btn-outline-dark" href="../historial/listar.php?scope=all">Todos</a>
  </form>
</div>

<?php include __DIR__.'/../templates/footer.php'; ?>
