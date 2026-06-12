<?php

require_once 'AppController.php';
require_once __DIR__.'/../repositories/FieldsRepository.php';
require_once __DIR__.'/../repositories/ProgressRepository.php';

class DashboardController extends AppController {

    /**
     * Render the public home page with available math fields.
     *
     * @return void
     */
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

    /**
     * Render the authenticated user's progress dashboard.
     *
     * @return void
     */
    public function dashboard() {
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            header("Location: /login");
            exit();
        }

        $progressRepository = new ProgressRepository();
        $progress = $progressRepository->getUserProgress($_SESSION['user_id']);
        $progressByField = $progressRepository->getUserProgressByField($_SESSION['user_id']);

        return $this->render("dashboard", [
            "title" => "Twój Panel - MaturaMat",
            "progress" => $progress,
            "progressByField" => $progressByField
        ]);
    }
}
