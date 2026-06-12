<?php

require_once __DIR__."/../../Database.php";

class Repository {

    protected $database;

    /**
     * Attach the shared database service to the repository.
     */
    public function __construct() {
        $this->database = Database::getInstance();
    }

    /**
     * Get a PDO connection from the shared database service.
     *
     * @return PDO Active database connection.
     */
    protected function getPDO(): PDO {
        return $this->database->connect(); 
    }
}
