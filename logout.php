<?php
// logout.php
require_once __DIR__ . '/src/auth.php';
auth_logout();
header('Location: /login.php');
exit;
