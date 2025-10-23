<?php
require_once __DIR__ . '/session.php';
require_once __DIR__ . '/conexion.php';
require_once __DIR__ . '/config.php';

function login($username, $password) {
  global $conn;

  $stmt = $conn->prepare("SELECT id, username, password, role FROM usuarios WHERE username = ?");
  $stmt->execute([$username]);
  $u = $stmt->fetch(PDO::FETCH_ASSOC);

  if (!$u) return false;
  if (!password_verify($password, $u['password'])) return false;

  $_SESSION['user'] = [
    'id' => (int)$u['id'],
    'username' => $u['username'],
    'rol' => $u['role']
  ];

  return true;
}

function logout() {
  $_SESSION = [];
  if (ini_get("session.use_cookies")) {
    $p = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $p["path"], $p["domain"], $p["secure"], $p["httponly"]);
  }
  session_destroy();
}

function is_logged() {
  return !empty($_SESSION['user']);
}

function user() {
  return $_SESSION['user'] ?? null;
}

function require_login() {
  if (!is_logged()) {
    header('Location: ' . APP_URL . 'index.php?msg=login');
    exit;
  }
}

function require_role($roles) {
  require_login();
  $roles = (array)$roles;
  if (!in_array($_SESSION['user']['rol'], $roles, true)) {
    http_response_code(403);
    echo "No tiene permiso para acceder.";
    exit;
  }
}

/**
 * 🚨 DEBUG MODE ACTIVADO 🚨
 * Esta versión muestra la información del rol detectado y la URL antes de redirigir,
 * para comprobar qué está fallando en Railway.
 */
function redirect_by_role($rol) {
  require_once __DIR__ . '/config.php';
  echo "<pre>";
  echo "ROL DETECTADO: " . htmlspecialchars($rol) . "\n";
  echo "APP_URL: " . APP_URL . "\n";

  switch ($rol) {
    case 'admin':
      $url = APP_URL . 'roles/admin_dashboard.php';
      break;
    case 'medico':
      $url = APP_URL . 'roles/medico_dashboard.php';
      break;
    case 'secretaria':
      $url = APP_URL . 'roles/secretaria_dashboard.php';
      break;
    default:
      $url = APP_URL . 'index.php';
  }

  echo "REDIRIGIENDO A: " . $url . "\n";
  echo "Si ves esto, el header no se envió aún (modo debug activado).";
  exit;
}
?>
