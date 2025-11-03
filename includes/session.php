<?php
// 🧠 Sesión persistente para entornos como Render

// Directorio temporal donde PHP puede guardar las sesiones
$sessionPath = sys_get_temp_dir();
if (!is_writable($sessionPath)) {
    $sessionPath = '/tmp'; // fallback por si acaso
}
session_save_path($sessionPath);

// Configuración de seguridad
ini_set('session.gc_maxlifetime', 3600);
ini_set('session.cookie_httponly', 1);
ini_set('session.use_strict_mode', 1);
ini_set('session.use_only_cookies', 1);
session_name('centro_salud_session');

// Evitar re-iniciar si ya está activa
if (session_status() === PHP_SESSION_NONE) {
    @session_start();
}
?>

