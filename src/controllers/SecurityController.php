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

    public function register() {
        if (!$this->isPost()) {
            return $this->render("register");
        }

        // 1. Pobranie danych z formularza (zgodnie z atrybutami 'name' w HTML)
        $username = $_POST['username'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $password2 = $_POST['password2'] ?? ''; // W HTML masz 'password2'
        $firstName = $_POST['firstName'] ?? '';
        $lastName = $_POST['lastName'] ?? '';
        $fullName = $firstName . " " . $lastName;

        // 2. Walidacja
        if (empty($username) || empty($email) || empty($password)) {
            return $this->render("register", ['messages' => ['Proszę wypełnić wymagane pola!']]);
        }

        if ($password !== $password2) {
            return $this->render("register", ['messages' => ['Hasła nie są identyczne!']]);
        }

        // 3. Sprawdzenie czy użytkownik/email już istnieje
        if ($this->usersRepository->getUserByEmail($email)) {
            return $this->render("register", ['messages' => ['Użytkownik o tym adresie email już istnieje!']]);
        }
        
        // Opcjonalnie: sprawdzenie unikalności username
        // if ($this->usersRepository->getUserByUsername($username)) ...

        // 4. Haszowanie hasła
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        // 5. Zapis do bazy danych
        try {
            $this->usersRepository->addUser(
                $username, 
                $email, 
                $hashedPassword, 
                $fullName
            );
        } catch (Exception $e) {
            // Logowanie błędu bazy danych
            return $this->render("register", ['messages' => ['Błąd rejestracji. Spróbuj ponownie później.']]);
        }

        return $this->render("login", ['messages' => ['Rejestracja zakończona sukcesem!']]);
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

    if (!password_verify($password, $user['password'])) {
        return $this->render("login", ['messages' => ['Błędne hasło!']]);
    }

    // --- LOGIKA SESJI ---
    session_regenerate_id(true); // bezpieczeństwo: zapobiega Session Fixation
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_email'] = $user['email'];
    // Możesz dodać więcej danych, np. rolę: $_SESSION['role'] = $user['role'];

    $url = "http://$_SERVER[HTTP_HOST]";
    header("Location: {$url}/dashboard");
    exit();
}

    public function logout()
    {
        session_destroy();
        $url = "http://$_SERVER[HTTP_HOST]";
        header("Location: {$url}/login");
        exit();
    }
}