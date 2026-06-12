<?php

require_once 'Repository.php';
require_once __DIR__.'/../models/User.php';

class UsersRepository extends Repository {
    private static ?UsersRepository $instance = null;

    /**
     * Keep construction private so the repository is accessed as a singleton.
     */
    private function __construct() {
        parent::__construct();
    }

    /**
     * Return the shared users repository instance.
     *
     * @return UsersRepository Singleton instance.
     */
    public static function getInstance(): UsersRepository {
        if (self::$instance === null) {
            self::$instance = new UsersRepository();
        }

        return self::$instance;
    }

    /**
     * Find a user and password hash by email for authentication.
     *
     * @param string $email Email address to search for.
     * @return User|null Matching user or null when not found.
     */
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
            null,
            $user['id']
        );
    }

    /**
     * Check whether an email address is already registered.
     *
     * @param string $email Email address to check.
     * @return bool True when the email exists.
     */
    public function emailExists(string $email): bool {
        $stmt = $this->database->connect()->prepare('
            SELECT 1 FROM users WHERE email = :email LIMIT 1
        ');
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();

        return (bool)$stmt->fetchColumn();
    }

    /**
     * Insert a new user using a pre-hashed password.
     *
     * @param string $username Public username.
     * @param string $email Email address.
     * @param string $password Password hash.
     * @return void
     */
    public function addUser(string $username, string $email, string $password) {
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

    /**
     * Return users for admin listing without password hashes.
     *
     * @return User[] Users sorted by username.
     */
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
                null,
                $data['id']
            );
        }
        return $result;
    }

    /**
     * Determine whether a user has the ADMIN role.
     *
     * @param int $userId User identifier.
     * @return bool True when the user is an administrator.
     */
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

    /**
     * Delete a user and related rows through database cascade rules.
     *
     * @param int $id User identifier.
     * @return bool True when the delete statement succeeds.
     */
    public function deleteUser(int $id): bool {
        $stmt = $this->database->connect()->prepare('
            DELETE FROM users WHERE id = :id
        ');
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Get a username by user identifier.
     *
     * @param int $id User identifier.
     * @return string|null Username or null when not found.
     */
    public function getUsernameById(int $id): ?string {
        $stmt = $this->database->connect()->prepare('SELECT username FROM users WHERE id = :id');
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchColumn() ?: null;
    }

    /**
     * Search users by username or email without returning password hashes.
     *
     * @param string $search Search text from the admin UI.
     * @return User[] Matching users.
     */
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
