<?php

class AppController {
    private $request;

    public function __construct()
    {
        $this->request = $_SERVER['REQUEST_METHOD'];
    }

    protected function isGet(): bool { return $this->request === 'GET'; }
    protected function isPost(): bool { return $this->request === 'POST'; }


    protected function requireLogin()
    {
        if (!isset($_SESSION['user_id'])) {
            $url = "http://$_SERVER[HTTP_HOST]";
            header("Location: {$url}/login");
            exit();
        }
    }

    protected function render(string $template = null, array $variables = [])
    {
        $templatePath = 'public/views/'. $template.'.html';
                 
        if(file_exists($templatePath)){
            extract($variables);
            ob_start();
            include $templatePath;
            $output = ob_get_clean();
        } else {
            die("Template not found: $templatePath");
        }

        echo $output;
        exit(); 
    }
}