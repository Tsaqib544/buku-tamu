<?php
// auth/logout.php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';

define('BASE_URL', ((!empty($_SERVER['HTTPS'])&&$_SERVER['HTTPS']!=='off')?'https':'http').'://'.$_SERVER['HTTP_HOST'].'/buku-tamu');

logoutUser();
header('Location: ' . BASE_URL . '/auth/login.php?msg=logout');
exit;
