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

/**
 * Redirige al usuario según su rol.
 * Compatible con Railway (usa rutas absolutas basadas en APP_URL)
 */
function redirect_by_role($rol) {
    $base = rtrim(APP_URL, '/') . '/';
    
    switch ($rol) {
        case 'admin':
            header('Location: ' . $base . 'roles/admin_dashboard.php');
            break;
        case 'medico':
            header('Location: ' . $base . 'roles/medico_dashboard.php');
            break;
        case 'secretaria':
            header('Location: ' . $base . 'roles/secretaria_dashboard.php');
            break;
        default:
            header('Location: ' . $base . 'index.php');
    }
    exit;
}
?>
