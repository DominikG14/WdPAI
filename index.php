<?php

// 1. Wyciągamy czystą ścieżkę z adresu URL (np. "public/images/1/1.png" lub "exercises/field/1")
$path = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
$path = trim($path, '/');

// 2. NOWOŚĆ: Jeśli ta ścieżka to fizyczny plik na dysku (np. obrazek), zwróć go i zakończ skrypt
if ($path !== '' && file_exists($path) && is_file($path)) {
    return false; 
}

require_once "Routing.php";

// START SESJI
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

// 3. Uruchamiamy routing dla ścieżek, które NIE są plikami (np. podstrony aplikacji)
Routing::run($path);
