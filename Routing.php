<?php

require_once 'src/controllers/DashboardController.php';
require_once 'src/controllers/SecurityController.php';
require_once 'src/controllers/ExerciseController.php';
require_once 'src/controllers/AdminController.php';

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
        "admin/exercises" => [
            "controller" => "AdminController",
            "action" => "exercises"
        ],
        "admin/exercises/create" => [
            "controller" => "AdminController",
            "action" => "createExercise"
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
        "exercises/random" => [
            "controller" => "ExerciseController",
            "action" => "random"
        ],
        "exercises/field/(\d+)" => [
            "controller" => "ExerciseController",
            "action" => "field"
        ]
    ];

    public static function run(string $path) {
        $path = ltrim($path, '/');

        $action = explode("?", $path)[0];

        foreach (self::$routes as $url => $config) {
            $pattern = "#^" . $url . "$#";

            if (preg_match($pattern, $action, $matches)) {
                $controllerName = $config['controller'];
                $actionName = $config['action'];

                $controller = new $controllerName();
                
                $argument = isset($matches[1]) ? $matches[1] : null;
                $controller->$actionName($argument);
                return;
            }
        }

        include 'public/views/404.html';
        exit();
    }
}
