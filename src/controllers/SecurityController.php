<?php

require_once 'AppController.php';
require_once __DIR__.'/../repositories/UsersRepository.php';

class SecurityController extends AppController {
    private $usersRepository;

    public function __construct() {
        parent::__construct();
        $this->usersRepository = new UsersRepository();
    }

    public function login() {
        if (!$this->isPost()) {
            return $this->render("login");
        }

        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        $user = $this->usersRepository->getUserByEmail($email);

        if (!$user) {
            return $this->render("login", ['messages' => ['Użytkownik nie istnieje!']]);
        }

        if (!password_verify($password, $user->getPassword())) {
            return $this->render("login", ['messages' => ['Błędne hasło!']]);
        }

        session_regenerate_id(true);
        $_SESSION['user_id'] = $user->getId();
        $_SESSION['user_email'] = $user->getEmail();
        $_SESSION['is_admin'] = $this->usersRepository->isAdmin($user->getId());
        
        // ZMIANA: Przekierowanie na /index zamiast /dashboard
        $url = "http://$_SERVER[HTTP_HOST]";
        header("Location: {$url}/index");
        exit();
    }

    public function register() {
        if (!$this->isPost()) {
            return $this->render("register");
        }

        $username = $_POST['username'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $password2 = $_POST['password2'] ?? '';

        if (empty($username) || empty($email) || empty($password)) {
            return $this->render("register", ['messages' => ['Uzupełnij pola!']]);
        }

        if ($password !== $password2) {
            return $this->render("register", ['messages' => ['Hasła różnią się!']]);
        }

        if ($this->usersRepository->getUserByEmail($email)) {
            return $this->render("register", ['messages' => ['Email zajęty!']]);
        }

        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $this->usersRepository->addUser($username, $email, $hashedPassword);

        return $this->render("login", ['messages' => ['Zarejestrowano pomyślnie!']]);
    }
}