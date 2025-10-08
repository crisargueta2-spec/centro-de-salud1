<?php
require_once __DIR__.'/includes/session.php';

$_SESSION = [];
if (ini_get('session.use_cookies')) {
  $p = session_get_cookie_params();
  setcookie(session_name(), '', time()-42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
}
@session_destroy();

$to = $_GET['to'] ?? 'index';
$dest = ($to === 'index') ? 'index.php?msg=logout' : 'index.php';
header('Location: '.$dest);
exit;
