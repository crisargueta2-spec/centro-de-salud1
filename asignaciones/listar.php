<?php 
require_once __DIR__ . '/../includes/auth.php';
require_role(['admin', 'secretaria', 'medico']);
require_once __DIR__ . '/../includes/conexion.php';
include __DIR__ . '/../templates/header.php';

$q      = trim($_GET['q'] ?? '');
$scope  = $_GET['scope'] ?? 'today';
$day    = $_GET['day']   ?? date('Y-m-d');
$month  = $_GET['month'] ?? date('Y-m');

$whereParts = [];
$params = [];

// 🔍 BÚSQUEDA
if ($q !== '') {

  $like = '%' . str_replace(' ', '%', $q) . '%';

  $parts = [
    "p.nombre LIKE ?",
    "p.apellido LIKE ?",
    "CONCAT(p.nombre,' ',p.apellido) LIKE ?",
    "a.estado LIKE ?",
    "a.prioridad LIKE ?"
  ];

  // buscar por fecha si q es YYYY-MM-DD
  if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $q)) {
    $parts[] = "DATE(a.fecha_cita) = ?";
    $params = array_merge($params, [$like,$like,$like,$like,$like,$q]);
  } else {
    $params = array_merge($params, [$like,$like,$like,$like,$like]);
  }

  if (ctype_digit($q)) {
    $parts[] = "a.id = ?";
    $params[] = (int)$q;
  }

  $whereParts[] = '(' . implode(' OR ', $parts) . ')';
}

// 📅 FILTROS
switch ($scope) {
  case 'day':
    $whereParts[] = "DATE(a.fecha_cita) = ?";
    $params[] = $day;
    break;

  case 'month':
    $first = $month . '-01';
    $whereParts[] = "DATE(a.fecha_cita) BETWEEN ? AND LAST_DAY(?)";
    $params[] = $first;
    $params[] = $first;
    break;

  case 'all':
    break;

  default:
    // HOY
    $whereParts[] = "DATE(a.fecha_cita) = CURDATE()";
    break;
}

$where = $whereParts ? ('WHERE ' . implode(' AND ', $whereParts)) : '';

$sql = "SELECT 
          a.id, a.paciente_id, a.especialista_id, a.fecha_cita, a.prioridad, a.estado,
          p.nombre AS pac_nombre, p.apellido AS pac_apellido
        FROM asignaciones a
        LEFT JOIN pacientes p ON p.id = a.paciente_id
        {$where}
        ORDER BY a.id DESC";

$stmt = $conexion->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fet
