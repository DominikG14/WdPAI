<?php

require_once 'AppController.php';
require_once __DIR__.'/../repositories/UsersRepository.php';
require_once __DIR__.'/../repositories/ProgressRepository.php'; // Import repozytorium postępów

class AdminController extends AppController {
    private $usersRepository;
    private $progressRepository;

    public function __construct() {
        parent::__construct();
        $this->usersRepository = new UsersRepository();
        $this->progressRepository = new ProgressRepository(); // Inicjalizacja
    }

    private function checkAdmin() {
        if (!isset($_SESSION['user_id']) || !$this->usersRepository->isAdmin($_SESSION['user_id'])) {
            $url = "http://$_SERVER[HTTP_HOST]";
            header("Location: {$url}/index");
            exit();
        }
    }

    public function users() {
        $this->checkAdmin();
        $users = $this->usersRepository->getUsers();

        return $this->render("admin-users", [
            "title" => "Panel Administratora - Użytkownicy",
            "users" => $users
        ]);
    }

    // NOWA METODA: Widok postępów konkretnego użytkownika
    public function userProgress($id) {
        $this->checkAdmin();

        $targetUsername = $this->usersRepository->getUsernameById((int)$id);
        if (!$targetUsername) {
            $url = "http://$_SERVER[HTTP_HOST]";
            header("Location: {$url}/admin/users");
            exit();
        }

        // Używamy stworzonej wcześniej metody z ProgressRepository
        $progress = $this->progressRepository->getUserProgress((int)$id);

        return $this->render("admin-user-progress", [
            "title" => "Postępy użytkownika " . $targetUsername,
            "targetUsername" => $targetUsername,
            "progress" => $progress
        ]);
    }

    public function deleteUser($id) {
        $this->checkAdmin();
        
        if ((int)$id === (int)$_SESSION['user_id']) {
            return $this->users();
        }

        $this->usersRepository->deleteUser((int)$id);
        
        $url = "http://$_SERVER[HTTP_HOST]";
        header("Location: {$url}/admin/users");
        exit();
    }
}