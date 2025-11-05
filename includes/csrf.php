<?php
/**
 * Protección CSRF (Cross-Site Request Forgery)
 * Compatible con login y formularios internos
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Genera el campo oculto con token CSRF
 */
function csrf_field() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    echo '<input type="hidden" name="csrf_token" value="' . $_SESSION['csrf_token'] . '">';
}

/**
 * Verifica el token CSRF
 */
function csrf_verify($token) {
    if (session_status() === PHP_SESSION_NONE) session_start();
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Alias compatible con versiones anteriores
 */
function csrf_validate($token) {
    return csrf_verify($token);
}
?>
