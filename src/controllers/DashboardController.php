<?php

require_once 'AppController.php';
require_once __DIR__.'/../repositories/FieldsRepository.php';
require_once __DIR__.'/../repositories/ProgressRepository.php';

class DashboardController extends AppController {

    // Strona główna z listą działów do rozwiązywania
    public function index() {
        $fieldsRepository = new FieldsRepository();
        $fields = $fieldsRepository->getFields();
        $isLoggedIn = isset($_SESSION['user_id']); 

        return $this->render("index", [
            "title" => "MaturaMat - Strona Główna", 
            "fields" => $fields,
            "isLoggedIn" => $isLoggedIn
        ]);
    }

    // Osobna podstrona profilu i postępów użytkownika (/dashboard)
    public function dashboard() {
        // Zabezpieczenie: jeśli użytkownik nie jest zalogowany, wyrzuć go do logowania
        if (!isset($_SESSION['user_id'])) {
            $url = "http://$_SERVER[HTTP_HOST]";
            header("Location: {$url}/login");
            exit();
        }

        $progressRepository = new ProgressRepository();
        // Pobieramy historię wszystkich podejść zalogowanego użytkownika
        $progress = $progressRepository->getUserProgress($_SESSION['user_id']);
        $progressByField = $progressRepository->getUserProgressByField($_SESSION['user_id']);

        return $this->render("dashboard", [
            "title" => "Twój Panel - MaturaMat",
            "progress" => $progress,
            "progressByField" => $progressByField
        ]);
    }
}
