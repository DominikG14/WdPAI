<?php

require_once 'src/controllers/SecurityController.php';
require_once 'src/controllers/DashboardController.php';

class Routing {
    public static $routes = [
        "login" => [
            "controller" => "SecurityController",
            "action" => "login"
        ],
        "dashboard" => [
            "controller" => "DashboardController",
            "action" => "dashboard"
        ],
        "dashboard/([0-9]+)" => [
            "controller" => "DashboardController",
            "action" => "dashboard"
        ],
        "" => [
            "controller" => "SecurityController",
            "action" => "login"
        ],
        "index" => [
            "controller" => "DashboardController",
            "action" => "index"
        ],
    ];

    private static $instances = [];

    public static function run(string $path) {
        foreach (self::$routes as $url => $config) {
            $pattern = "#^" . $url . "$#";

            if (preg_match($pattern, $path, $matches)) {
                $controllerName = $config["controller"];
                $action = $config["action"];

                if (!isset(self::$instances[$controllerName])) {
                    self::$instances[$controllerName] = new $controllerName();
                }
                
                $controllerObj = self::$instances[$controllerName];

                $id = $matches[1] ?? null;

                $controllerObj->$action($id);
                return;
            }
        }

        http_response_code(404);
        include 'public/views/404.html';
    }
}