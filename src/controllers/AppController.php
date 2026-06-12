<?php

class AppController {
    private $request;

    /**
     * Store the current HTTP request method for controller helpers.
     */
    public function __construct()
    {
        $this->request = $_SERVER['REQUEST_METHOD'];
    }

    /**
     * Check whether the current request uses GET.
     *
     * @return bool True when the request method is GET.
     */
    protected function isGet(): bool { return $this->request === 'GET'; }

    /**
     * Check whether the current request uses POST.
     *
     * @return bool True when the request method is POST.
     */
    protected function isPost(): bool { return $this->request === 'POST'; }

    /**
     * Detect HTTPS directly or through a reverse-proxy header.
     *
     * @return bool True when the request is considered secure.
     */
    protected function isSecureRequest(): bool
    {
        if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
            return true;
        }

        return isset($_SERVER['HTTP_X_FORWARDED_PROTO'])
            && strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https';
    }

    /**
     * Redirect insecure requests to HTTPS while preserving method and path.
     *
     * @return void
     */
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

    /**
     * Validate an internal return URL and reject open redirects/header injection.
     *
     * @param string|null $returnUrl URL supplied by a form or query parameter.
     * @return string|null Safe internal URL, or null when invalid.
     */
    protected function sanitizeReturnUrl(?string $returnUrl): ?string
    {
        if (!$returnUrl || $returnUrl[0] !== '/' || str_starts_with($returnUrl, '//')) {
            return null;
        }

        return preg_match('/[\r\n]/', $returnUrl) ? null : $returnUrl;
    }

    /**
     * Require an authenticated user before running a controller action.
     *
     * @return void
     */
    protected function requireLogin()
    {
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            header("Location: /login");
            exit();
        }
    }

    /**
     * Render a PHP-backed HTML template with extracted variables.
     *
     * @param string|null $template Template name from public/views without extension.
     * @param array $variables Variables exposed to the template.
     * @return void
     */
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
