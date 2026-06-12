<?php
require_once "config.php";

class Database {
    private $username;
    private $password;
    private $host;
    private $database;
    private static $instance = null;

    /**
     * Load database connection settings from config constants.
     */
    private function __construct() {
        $this->username = USERNAME;
        $this->password = PASSWORD;
        $this->host = HOST;
        $this->database = DATABASE;
    }

    /**
     * Return the shared database service instance.
     *
     * @return Database Singleton instance.
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    /**
     * Open a new PDO connection to PostgreSQL.
     *
     * @return PDO Active database connection.
     */
    public function connect() {
        try {
            $conn = new PDO(
                "pgsql:host=$this->host;port=5432;dbname=$this->database",
                $this->username,
                $this->password,
                ["sslmode"  => "prefer"]
            );
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $conn;
        } catch(PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }
}
