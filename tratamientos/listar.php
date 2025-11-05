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

// ✅ Búsqueda
if ($q !== '') {
    $like = '%' . str_replace(' ', '%', $q) . '%';

    $parts = [
        "(p.nombre LIKE ? OR p.apellido LIKE ? OR CONCAT(p.nombre,' ',p.apellido) LIKE ?)",
        "(t.diagnostico LIKE ? OR t.plan LIKE ? OR t.estado LIKE ?)"
    ];

    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $q)) {
        $parts[] = "(DATE(t.fecha_inicio)=? OR DATE(t.fecha_fin)=?)";
        $params = array_merge($params, [$like,$like,$like,$like,$like,$like,$q,$q]);
    } else {
        $params = array_merge($params, [$like,$like,$like,$like,$like,$like]);
    }

    $whereParts[] = '(' . implode(' OR ', $parts) . ')';
}

// ✅ Fecha
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

// ✅ Query
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

    <!-- ✅ rutas absolutas -->
    <form class="d-flex gap-2" method="get" action="/tratamientos/listar.php">
      <input class="form-control" style="min-width:260px"
             type="search" name="q"
             value="<?= htmlspecialchars($q) ?>"
             placeholder="Buscar por paciente, diagnóstico...">

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
        <a class="btn btn-outline-dark" href="/tratamientos/lis

