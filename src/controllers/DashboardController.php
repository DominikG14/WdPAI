<?php

require_once 'AppController.php';
require_once __DIR__.'/../repositories/FieldsRepository.php'; // Ładujemy nowe repozytorium

class DashboardController extends AppController {

    public function index() {
        // $this->requireLogin();
        
        $title = "MaturaMat - Strona Główna";
        
        // Tworzymy obiekt repozytorium dla działów
        $fieldsRepository = new FieldsRepository();
        $fields = $fieldsRepository->getFields(); // Pobieramy 14 działów z bazy

        // Sprawdzamy stan zalogowania (załóżmy, że masz taką zmienną/metodę w sesji)
        $isLoggedIn = isset($_SESSION['user_id']); 

        // Przekazujemy "fields" oraz "isLoggedIn" bezpośrednio do widoku
        return $this->render("index", [
            "title" => $title, 
            "fields" => $fields,
            "isLoggedIn" => $isLoggedIn
        ]);
    }
}