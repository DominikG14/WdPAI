<?php

require_once 'AppController.php';
require_once __DIR__.'/../repositories/UsersRepository.php';

class SecurityController extends AppController {

    private $usersRepository;

    public function __construct()
    {
        // USUNIĘTO: parent::__construct(); -> to powodowało błąd
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
            return $this->render("login", ['messages' => ['Użytkownik o tym adresie nie istnieje!']]);
        }

        // W bazie masz pole 'password', sprawdzamy hash
        if (!password_verify($password, $user['password'])) {
            return $this->render("login", ['messages' => ['Błędne hasło!']]);
        }

        $url = "http://$_SERVER[HTTP_HOST]";
        header("Location: {$url}/dashboard");
        exit();
    }

    public function register() {
        if (!$this->isPost()) {
            return $this->render("register");
        }

        $email = $_POST['email'] ?? '';
        $username = $_POST['username'] ?? ''; // Pamiętaj o dodaniu tego do HTML
        $password = $_POST['password'] ?? '';
        $passwordConfirm = $_POST['password2'] ?? '';
        $firstName = $_POST['firstName'] ?? '';
        $lastName = $_POST['lastName'] ?? '';

        if ($password !== $passwordConfirm) {
            return $this->render("register", ['messages' => ['Hasła nie są identyczne!']]);
        }

        // Przygotowujemy dane zgodnie z Twoim schematem SQL (username, email, password, full_name)
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $fullName = trim($firstName . ' ' . $lastName);

        try {
            $this->usersRepository->createUser(
                $username,
                $email,
                $hashedPassword,
                $fullName
            );
        } catch (\Exception $e) {
            return $this->render("register", ['messages' => ['Email lub login jest już zajęty!']]);
        }

        return $this->render("login", ['messages' => ['Zarejestrowano pomyślnie!']]);
    }
}