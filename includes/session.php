<?php
// 🧠 Sesión segura, compatible con cualquier contexto (Render o local)

// Si la sesión ya está activa, no hacer nada
if (session_status() === PHP_SESSION_NONE) {
    // Configurar solo si los headers no fueron enviados
    if (!headers_sent()) {
        ini_set('session.cookie_httponly', 1);
        ini_set('session.use_strict_mode', 1);
        ini_set('session.use_only_cookies', 1);
        session_name('centro_salud_session');
        session_start();
    } else {
        // Si ya se enviaron headers, intenta recuperar sesión existente
        @session_start();
    }
}
?>
