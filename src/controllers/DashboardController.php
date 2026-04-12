<?php

require_once 'AppController.php';

class DashboardController extends AppController {

    public function dashboard($id = null) {
        // TODO pobieranie danych z bazy
        // wstawianie zmiennych na widok

        return $this->render("dashboard", [
            'id' => $id,
        ]);
    }

    public function index($id = null) {

        return $this->render("index");
    }
}