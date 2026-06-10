<?php

require_once 'src/controllers/DashboardController.php';
require_once 'src/controllers/SecurityController.php';
require_once 'src/controllers/ExerciseController.php';
require_once 'src/controllers/AdminController.php'; // Zaimportowany kontroler admina

class Routing {

    public static $routes = [
        "login" => [
            "controller" => "SecurityController",
            "action" => "login"
        ],
        "" => [
            "controller" => "DashboardController",
            "action" => "index"
        ],
        "index" => [
            "controller" => "DashboardController",
            "action" => "index"
        ],
        "dashboard" => [
            "controller" => "DashboardController",
            "action" => "dashboard"
        ],
        "admin/users" => [
            "controller" => "AdminController",
            "action" => "users"
        ],
        "admin/users/search" => [
            "controller" => "AdminController",
            "action" => "searchUsers"
        ],
        "admin/users/progress/(\d+)" => [
            "controller" => "AdminController",
            "action" => "userProgress"
        ],
        "admin/users/delete/(\d+)" => [
            "controller" => "AdminController",
            "action" => "deleteUser"
        ],
        "register" => [
            "controller" => "SecurityController",
            "action" => "register"
        ],
        "logout" => [
            "controller" => "SecurityController",
            "action" => "logout"
        ],
        "exercises/save" => [
            "controller" => "ExerciseController",
            "action" => "save"
        ],
        "exercises/field/(\d+)" => [
            "controller" => "ExerciseController",
            "action" => "field"
        ]
    ];

    public static function run(string $path) {
        // Zabezpieczenie: Odcinamy ewentualny ukośnik z początku ścieżki (np. /search -> search)
        $path = ltrim($path, '/');

        // Wyciągamy czysty adres bez parametrów zapytania query string (?param=val)
        $action = explode("?", $path)[0];

        foreach (self::$routes as $url => $config) {
            $pattern = "#^" . $url . "$#";

            if (preg_match($pattern, $action, $matches)) {
                $controllerName = $config['controller'];
                $actionName = $config['action'];

                $controller = new $controllerName();
                
                // Przekazujemy ewentualny złapany z regexa identyfikator (\d+) jako argument do metody
                $argument = isset($matches[1]) ? $matches[1] : null;
                $controller->$actionName($argument);
                return;
            }
        }

        // Jeśli żadna ścieżka nie pasuje, wyświetlamy błąd 404
        include 'public/views/404.html';
        exit();
    }
}
