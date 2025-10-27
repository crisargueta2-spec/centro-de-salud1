<?php
require_once __DIR__ . '/session.php';

function csrf_token() {
  if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
  }
  return $_SESSION['csrf'];
}

function csrf_field() {
  $t = htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8');
  echo '<input type="hidden" name="csrf" value="'.$t.'">';
}

function csrf_validate($token) {
  return isset($_SESSION['csrf']) && hash_equals($_SESSION['csrf'], $token ?? '');
}
