<?php
require_once __DIR__ . '/session.php';
require_once __DIR__ . '/conexion.php';
require_once __DIR__ . '/config.php';

function login($username, $password) {
    global $conn;

    try {
        $stmt = $conn->prepare("SELECT id, username, password, role FROM usuarios WHERE username = ?");
        $stmt->execute([$username]);
        $u = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$u) {
            error_log("❌ Usuario no encontrado: $username");
            return false;
        }

        $hash = trim($u['password']);

        if (empty($hash)) {
            error_log("⚠️ El usuario $username tiene campo password vacío");
            return false;
        }

        // Comparación segura
        if (password_verify($password, $hash)) {
            $_SESSION['user'] = [
                'id' => (int)$u['id'],
                'username' => $u['username'],
                'rol' => $u['role']
            ];
            error_log("✅ Login exitoso para $username");
            return true;
        } else {
            error_log("❌ Contraseña incorrecta para $username");
            return false;
        }

    } catch (PDOException $e) {
        error_log("❌ Error en login(): " . $e->getMessage());
        return false;
    }
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
/**
 * Verifica si el usuario tiene un rol específico.
 */
function require_role($rol) {
    if (!is_logged()) {
        header('Location: ' . APP_URL . 'index.php?msg=login');
        exit;
    }

    if ($_SESSION['user']['rol'] !== $rol) {
        header('HTTP/1.1 403 Forbidden');
        echo "<h1 style='text-align:center;color:red;margin-top:50px'>🚫 Acceso denegado</h1>";
        echo "<p style='text-align:center'>No tienes permiso para acceder a esta sección.</p>";
        exit;
    }
}

?>
