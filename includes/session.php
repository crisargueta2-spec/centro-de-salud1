<?php
if (session_status() === PHP_SESSION_NONE) {
    $sessionPath = sys_get_temp_dir();
    if (!is_writable($sessionPath)) {
        $sessionPath = '/tmp';
    }
    session_save_path($sessionPath);

    ini_set('session.gc_maxlifetime', 3600);
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_strict_mode', 1);
    ini_set('session.use_only_cookies', 1);

    session_name('centro_salud_session');
    session_start();
}
