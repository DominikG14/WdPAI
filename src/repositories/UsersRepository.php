<?php

require_once 'Repository.php';
require_once __DIR__.'/../models/User.php';

class UsersRepository extends Repository {
    private static ?UsersRepository $instance = null;

    private function __construct() {
        parent::__construct();
    }

    public static function getInstance(): UsersRepository {
        if (self::$instance === null) {
            self::$instance = new UsersRepository();
        }

        return self::$instance;
    }

    public function getUserByEmail(string $email): ?User {
        $stmt = $this->database->connect()->prepare('
            SELECT id, username, email, password FROM users WHERE email = :email
        ');
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            return null;
        }

        return new User(
            $user['username'],
            $user['email'],
            $user['password'],
            null, // Usunięto błąd: kolumna full_name nie istnieje w bazie, przekazujemy null
            $user['id']
        );
    }

    public function emailExists(string $email): bool {
        $stmt = $this->database->connect()->prepare('
            SELECT 1 FROM users WHERE email = :email LIMIT 1
        ');
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();

        return (bool)$stmt->fetchColumn();
    }

    public function addUser(string $username, string $email, string $password) {
        // Dopasowano INSERT dokładnie do kolumn w Twojej bazie danych
        $stmt = $this->database->connect()->prepare('
            INSERT INTO users (username, email, password)
            VALUES (?, ?, ?)
        ');

        $stmt->execute([
            $username,
            $email,
            $password
        ]);
    }

    public function getUsers(): array {
        $stmt = $this->database->connect()->prepare('SELECT id, username, email FROM users ORDER BY username ASC');
        $stmt->execute();
        $usersData = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $result = [];
        foreach ($usersData as $data) {
            $result[] = new User(
                $data['username'],
                $data['email'],
                null,
                null, // Usunięto błąd: przekazujemy null zamiast nieistniejącego klucza full_name
                $data['id']
            );
        }
        return $result;
    }

    public function isAdmin(int $userId): bool {
        $stmt = $this->database->connect()->prepare('
            SELECT COUNT(*) FROM user_roles ur
            JOIN roles r ON ur.role_id = r.id
            WHERE ur.user_id = :userId AND r.name = \'ADMIN\'
        ');
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchColumn() > 0;
    }

    public function deleteUser(int $id): bool {
        $stmt = $this->database->connect()->prepare('
            DELETE FROM users WHERE id = :id
        ');
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function getUsernameById(int $id): ?string {
        $stmt = $this->database->connect()->prepare('SELECT username FROM users WHERE id = :id');
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchColumn() ?: null;
    }

    public function searchUsers(string $search): array {
        $search = trim($search);

        if ($search === '') {
            return $this->getUsers();
        }

        $stmt = $this->database->connect()->prepare('
            SELECT id, username, email FROM users
            WHERE LOWER(username) LIKE LOWER(:search)
               OR LOWER(email) LIKE LOWER(:search)
            ORDER BY username ASC
        ');

        $searchPattern = '%' . $search . '%';
        $stmt->bindParam(':search', $searchPattern, PDO::PARAM_STR);
        $stmt->execute();

        $usersData = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $result = [];

        foreach ($usersData as $data) {
            $result[] = new User(
                $data['username'],
                $data['email'],
                null,
                null,
                $data['id']
            );
        }

        return $result;
    }
}
