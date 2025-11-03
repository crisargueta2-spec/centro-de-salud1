<?php
// 🧠 Iniciar sesión de forma segura y silenciosa

if (session_status() === PHP_SESSION_NONE) {
    // Configurar solo si los encabezados no se enviaron aún
    if (!headers_sent()) {
        ini_set('session.cookie_httponly', 1);
        ini_set('session.use_strict_mode', 1);
        ini_set('session.use_only_cookies', 1);
    }

    // Asignar nombre de sesión antes de iniciarla
    session_name('centro_salud_session');
    session_start();
}
?>
