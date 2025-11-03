<?php
// 🧠 Configuración segura de sesión
if (session_status() === PHP_SESSION_NONE) {
    // Evita errores si ya se enviaron headers
    if (headers_sent() === false) {
        ini_set('session.cookie_httponly', 1);
        ini_set('session.use_strict_mode', 1);
        ini_set('session.use_only_cookies', 1);
    }

    session_start();
}

// 🔐 Forzar un nombre de sesión identificable (opcional)
if (session_name() !== 'centro_salud_session') {
    session_name('centro_salud_session');
}
?>
