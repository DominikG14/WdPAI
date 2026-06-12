<?php

class AppController {
    private $request;

    public function __construct()
    {
        $this->request = $_SERVER['REQUEST_METHOD'];
    }

    protected function isGet(): bool { return $this->request === 'GET'; }
    protected function isPost(): bool { return $this->request === 'POST'; }

    protected function isSecureRequest(): bool
    {
        if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
            return true;
        }

        return isset($_SERVER['HTTP_X_FORWARDED_PROTO'])
            && strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https';
    }

    protected function requireHttps(): void
    {
        if ($this->isSecureRequest()) {
            return;
        }

        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        if (preg_match('/^(localhost|127\.0\.0\.1):8080$/', $host, $matches)) {
            $host = $matches[1] . ':8443';
        }

        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        header("Location: https://{$host}{$uri}", true, 308);
        exit();
    }

    protected function sanitizeReturnUrl(?string $returnUrl): ?string
    {
        if (!$returnUrl || $returnUrl[0] !== '/' || str_starts_with($returnUrl, '//')) {
            return null;
        }

        return preg_match('/[\r\n]/', $returnUrl) ? null : $returnUrl;
    }

    protected function requireLogin()
    {
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            header("Location: /login");
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
