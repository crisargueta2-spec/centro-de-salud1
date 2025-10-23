<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Genera un token CSRF y lo imprime como campo oculto del formulario.
 */
function csrf_field() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    echo '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($_SESSION['csrf_token']) . '">';
}

/**
 * Verifica que el token CSRF del formulario sea válido.
 */
function csrf_verify($token) {
    if (empty($_SESSION['csrf_token']) || $token !== $_SESSION['csrf_token']) {
        return false;
    }
    // Invalida el token usado para evitar reutilización
    unset($_SESSION['csrf_token']);
    return true;
}
?>
