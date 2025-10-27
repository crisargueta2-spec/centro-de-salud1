<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/config.php';

function safe_back_url(string $fallback): string {
  $ref = $_GET['back'] ?? ($_SERVER['HTTP_REFERER'] ?? '');
  if ($ref && (strpos($ref, APP_URL) === 0)) {
    return $ref;
  }
  if ($ref && !preg_match('#^[a-z]+://#i', $ref)) {
    return APP_URL . ltrim($ref, '/');
  }
  return APP_URL . ltrim($fallback, '/');
}

function back_link(string $fallback, string $label = '← Volver', string $class = 'btn-back') {
  $href = safe_back_url($fallback);
  echo '<a class="'.htmlspecialchars($class, ENT_QUOTES).'" href="'.htmlspecialchars($href, ENT_QUOTES).'">'.$label.'</a>';
}
