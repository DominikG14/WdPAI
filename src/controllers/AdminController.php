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

    public function searchUsers() {
        $this->checkAdmin();

        if (!$this->isPost()) {
            http_response_code(405);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Method not allowed']);
            exit();
        }

        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        $payload = [];

        if (stripos($contentType, 'application/json') !== false) {
            $payload = json_decode(file_get_contents('php://input'), true) ?: [];
        } else {
            $payload = $_POST;
        }

        $search = trim($payload['search'] ?? '');
        $users = $this->usersRepository->searchUsers($search);

        header('Content-Type: application/json');
        echo json_encode(array_map(function (User $user) {
            return [
                'id' => $user->getId(),
                'username' => $user->getUsername(),
                'email' => $user->getEmail()
            ];
        }, $users));
        exit();
    }
}
