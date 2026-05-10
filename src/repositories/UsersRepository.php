<?php

require_once 'Repository.php';

class UsersRepository extends Repository {

    public function getUsers(): array 
    {
        $stmt = $this->database->connect()->prepare('
            SELECT * FROM users
        ');
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function getUserByEmail(string $email) {
        $stmt = $this->database->connect()->prepare('
            SELECT * FROM users WHERE email = :email
        ');
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function addUser(string $username, string $email, string $password, ?string $fullName) {
    $stmt = $this->database->connect()->prepare('
        INSERT INTO users (username, email, password, full_name)
        VALUES (?, ?, ?, ?)
    ');

    $stmt->execute([
        $username,
        $email,
        $password,
        $fullName
    ]);
}
}