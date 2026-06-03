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
session_start();

// 3. Uruchamiamy routing dla ścieżek, które NIE są plikami (np. podstrony aplikacji)
Routing::run($path);