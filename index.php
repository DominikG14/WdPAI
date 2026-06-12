<?php

$path = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
$path = trim($path, '/');

if ($path !== '' && file_exists($path) && is_file($path)) {
    return false; 
}

require_once "Routing.php";

session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'domain' => '',
    'secure' => true,
    'httponly' => true,
    'samesite' => 'Strict',
]);
ini_set('session.use_strict_mode', '1');
ini_set('session.cookie_httponly', '1');
ini_set('session.cookie_secure', '1');
session_start();

Routing::run($path);