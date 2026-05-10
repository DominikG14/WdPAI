<?php

require_once 'AppController.php';
require_once __DIR__.'/../repositories/UsersRepository.php';

class SecurityController extends AppController {

    private $usersRepository;

    public function __construct()
    {
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
            return $this->render("login", ['messages' => ['Użytkownik o tym adresie email nie istnieje!']]);
        }

        if (!password_verify($password, $user['password'])) {
            return $this->render("login", ['messages' => ['Błędne hasło!']]);
        }

        // Tutaj możesz ustawić sesję, np.:
        // session_start();
        // $_SESSION['user_id'] = $user['id'];

        $url = "http://$_SERVER[HTTP_HOST]";
        header("Location: {$url}/dashboard");
        exit();
    }

    public function register() {
        if ($this->isPost()) {
            // 1. Pobranie danych z POST
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            $confirmedPassword = $_POST['confirmedPassword'] ?? '';

            // 2. Prosta walidacja (warto ją wydzielić do osobnej klasy/metody)
            if (empty($email) || empty($password)) {
                return $this->render("register", ['messages' => ['Proszę wypełnić wszystkie pola!']]);
            }

            if ($password !== $confirmedPassword) {
                return $this->render("register", ['messages' => ['Hasła nie są identyczne!']]);
            }

            // 3. Haszowanie hasła - NIGDY nie zapisuj czystego tekstu
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

            // 4. Zapis do bazy danych (przykład z użyciem repozytorium/modelu)
            $userRepository = new UserRepository();
            
            // Sprawdź czy użytkownik już istnieje
            if ($userRepository->getUserByEmail($email)) {
                return $this->render("register", ['messages' => ['Użytkownik o tym adresie już istnieje!']]);
            }

            // Tworzenie nowego obiektu User i zapis
            $user = new User($email, $hashedPassword);
            $userRepository->addUser($user);

            // 5. Przekierowanie po sukcesie
            return $this->render("login", ['messages' => ['Rejestracja zakończona sukcesem! Możesz się zalogować.']]);
        }

        return $this->render("register");
    }
}