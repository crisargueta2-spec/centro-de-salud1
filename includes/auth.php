<?php
require_once __DIR__ . '/session.php';

/**
 * Devuelve el usuario actual (o null si no hay sesión)
 */
function user(): ?array {
    return $_SESSION['user'] ?? null;
}

/**
 * Verifica si el usuario tiene sesión activa
 */
function is_logged_in(): bool {
    return isset($_SESSION['user']);
}

/**
 * Verifica que el usuario tenga el rol especificado.
 * Si no tiene sesión o no tiene permisos, redirige al login.
 */
function require_role(string|array $roles): void {
    $user = user();

    if (!$user) {
        header('Location: /index.php?err=nologin');
        exit;
    }

    // Aceptar tanto string como array de roles permitidos
    $rol = strtolower($user['rol'] ?? $user['role'] ?? '');
    if (is_array($roles)) {
        $permitido = in_array($rol, array_map('strtolower', $roles));
    } else {
        $permitido = ($rol === strtolower($roles));
    }

    if (!$permitido) {
        header('Location: /index.php?err=denied');
        exit;
    }
}

/**
 * Redirige al dashboard según el rol del usuario
 */
function redirect_by_role(string $rol): void {
    $rol = strtolower($rol);
    $map = [
        'admin'      => '/roles/admin_dashboard.php',
        'secretaria' => '/roles/secretaria_dashboard.php',
        'medico'     => '/roles/medico_dashboard.php'
    ];
    header('Location: ' . ($map[$rol] ?? '/index.php'));
    exit;
}

