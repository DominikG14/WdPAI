<?php

require_once "Routing.php";

// START SESJI
session_start();

$path = trim($_SERVER["REQUEST_URI"], '/');
$path = parse_url($path, PHP_URL_PATH);

Routing::run($path);