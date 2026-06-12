<?php

require_once 'AppController.php';
require_once __DIR__ . '/../repositories/UsersRepository.php';

class SecurityController extends AppController {
    private $usersRepository;

    private const MAX_EMAIL_LENGTH = 255;
    private const MAX_USERNAME_LENGTH = 50;
    private const MAX_PASSWORD_LENGTH = 128;
    private const MIN_PASSWORD_LENGTH = 8;

    public function __construct() {
        parent::__construct();
        $this->usersRepository = UsersRepository::getInstance();
    }

    private function auditFailedLogin(string $email, string $reason): void {
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $safeEmail = substr(str_replace(["\r", "\n"], '', $email), 0, self::MAX_EMAIL_LENGTH);
        error_log(sprintf('[AUTH] failed_login email="%s" ip="%s" reason="%s"', $safeEmail, $ip, $reason));
    }

    private function isPasswordStrong(string $password): bool {
        return strlen($password) >= self::MIN_PASSWORD_LENGTH
            && strlen($password) <= self::MAX_PASSWORD_LENGTH
            && preg_match('/[a-z]/', $password)
            && preg_match('/[A-Z]/', $password)
            && preg_match('/\d/', $password);
    }

    private function redirectAfterAuth(?string $returnUrl): void {
        $target = $this->sanitizeReturnUrl($returnUrl) ?? '/index';
        header("Location: {$target}");
        exit();
    }

    public function login() {
        $this->requireHttps();

        if (!$this->isPost()) {
            return $this->render("login");
        }

        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (
            $email === ''
            || $password === ''
            || strlen($email) > self::MAX_EMAIL_LENGTH
            || strlen($password) > self::MAX_PASSWORD_LENGTH
            || !filter_var($email, FILTER_VALIDATE_EMAIL)
        ) {
            http_response_code(400);
            $this->auditFailedLogin($email, 'invalid_input');
            return $this->render("login", ['messages' => ['Niepoprawne dane logowania.']]);
        }

        $user = $this->usersRepository->getUserByEmail($email);

        if (!$user) {
            http_response_code(401);
            $this->auditFailedLogin($email, 'user_not_found');
            return $this->render("login", ['messages' => ['Niepoprawny email lub haslo.']]);
        }

        if (!password_verify($password, $user->getPassword())) {
            http_response_code(401);
            $this->auditFailedLogin($email, 'invalid_password');
            return $this->render("login", ['messages' => ['Niepoprawny email lub haslo.']]);
        }

        session_regenerate_id(true);
        $_SESSION['user_id'] = $user->getId();
        $_SESSION['user_email'] = $user->getEmail();
        $_SESSION['is_admin'] = $this->usersRepository->isAdmin($user->getId());
        $_SESSION['justLoggedIn'] = true;

        $this->redirectAfterAuth($_POST['returnUrl'] ?? null);
    }

    public function register() {
        $this->requireHttps();

        if (!$this->isPost()) {
            return $this->render("register");
        }

        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $password2 = $_POST['password2'] ?? '';

        if (
            $username === ''
            || $email === ''
            || $password === ''
            || strlen($username) > self::MAX_USERNAME_LENGTH
            || strlen($email) > self::MAX_EMAIL_LENGTH
            || strlen($password) > self::MAX_PASSWORD_LENGTH
            || !filter_var($email, FILTER_VALIDATE_EMAIL)
        ) {
            http_response_code(400);
            return $this->render("register", ['messages' => ['Podaj poprawne dane rejestracji.']]);
        }

        if ($password !== $password2) {
            http_response_code(400);
            return $this->render("register", ['messages' => ['Hasla roznia sie.']]);
        }

        if (!$this->isPasswordStrong($password)) {
            http_response_code(400);
            return $this->render("register", [
                'messages' => ['Haslo musi miec co najmniej 8 znakow oraz zawierac mala litere, wielka litere i cyfre.']
            ]);
        }

        if ($this->usersRepository->emailExists($email)) {
            http_response_code(409);
            return $this->render("register", ['messages' => ['Email jest juz zajety.']]);
        }

        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $this->usersRepository->addUser($username, $email, $hashedPassword);

        $newUser = $this->usersRepository->getUserByEmail($email);
        if ($newUser) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $newUser->getId();
            $_SESSION['user_email'] = $newUser->getEmail();
            $_SESSION['is_admin'] = $this->usersRepository->isAdmin($newUser->getId());
            $_SESSION['justLoggedIn'] = true;

            $this->redirectAfterAuth($_POST['returnUrl'] ?? null);
        }

        return $this->render("login", ['messages' => ['Zarejestrowano pomyslnie. Sprobuj sie zalogowac.']]);
    }

    public function logout() {
        if (session_status() == PHP_SESSION_ACTIVE) {
            session_unset();
            session_destroy();
            setcookie(session_name(), '', [
                'expires' => time() - 3600,
                'path' => '/',
                'domain' => '',
                'secure' => true,
                'httponly' => true,
                'samesite' => 'Strict',
            ]);
        }

        header("Location: /index");
        exit();
    }
}
