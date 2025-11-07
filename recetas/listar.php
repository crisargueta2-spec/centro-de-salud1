// Fecha (por defecto hoy por fecha_emision)
switch ($scope) {
  case 'day':
    $whereParts[] = "DATE(r.fecha_emision) = ?";
    $params[] = preg_match('/^\d{4}-\d{2}-\d{2}$/',$day)?$day:date('Y-m-d');
    break;
  case 'month':
    $first = preg_match('/^\d{4}-\d{2}$/',$month)?($month.'-01'):(date('Y-m').'-01');
    $whereParts[] = "DATE(r.fecha_emision) BETWEEN ? AND LAST_DAY(?)";
    $params[] = $first; $params[] = $first;
    break;
  case 'all':
    break;
  case 'today':
  default:
    $whereParts[] = "DATE(r.fecha_emision) = CURDATE()";
    break;
}
