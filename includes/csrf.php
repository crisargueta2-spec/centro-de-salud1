<?php
// Protección CSRF universal
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Genera token CSRF y lo guarda en sesión
 */
function csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Imprime el campo oculto CSRF en formularios
 */
function csrf_field() {
    $t = htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8');
    echo '<input type="hidden" name="csrf_token" value="'.$t.'">';
}

/**
 * Verifica que el token recibido sea válido
 */
function csrf_verify($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token ?? '');
}

/**
 * Alias compatible (por si el código llama csrf_validate)
 */
function csrf_validate($token) {
    return csrf_verify($token);
}
?>
