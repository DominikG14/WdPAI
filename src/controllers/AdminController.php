<?php

require_once 'AppController.php';
require_once __DIR__.'/../repositories/UsersRepository.php';

class AdminController extends AppController {
    private $usersRepository;

    public function __construct() {
        parent::__construct();
        $this->usersRepository = new UsersRepository();
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

    public function deleteUser($id) {
        $this->checkAdmin();
        
        // Zabezpieczenie przed usunięciem samego siebie
        if ((int)$id === (int)$_SESSION['user_id']) {
            return $this->users(); // Albo przekierowanie z komunikatem błędu
        }

        $this->usersRepository->deleteUser((int)$id);
        
        $url = "http://$_SERVER[HTTP_HOST]";
        header("Location: {$url}/admin/users");
        exit();
    }
}