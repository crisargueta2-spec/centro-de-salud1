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

function is_logged() { return !empty($_SESSION['user']); }
function user() { return $_SESSION['user'] ?? null; }

function require_login() {
    if (!is_logged()) {
        header('Location: ' . APP_URL . 'index.php?msg=login');
        exit;
    }
}

function redirect_by_role($role) {
    $base = rtrim(APP_URL, '/') . '/';

    if ($role === 'admin') {
        header('Location: ' . $base . 'admin/index.php');
        exit;
    } elseif ($role === 'medico') {
        header('Location: ' . $base . 'medico/index.php');
        exit;
    } else {
        header('Location: ' . $base . 'usuario/index.php');
        exit;
    }
}

function redirect_by_role($rol) {
    switch ($rol) {
        case 'admin':      header('Location: ' . APP_URL . 'roles/admin_dashboard.php'); break;
        case 'medico':     header('Location: ' . APP_URL . 'roles/medico_dashboard.php'); break;
        case 'secretaria': header('Location: ' . APP_URL . 'roles/secretaria_dashboard.php'); break;
        default:           header('Location: ' . APP_URL . 'index.php');
    }
    exit;
}
?>
