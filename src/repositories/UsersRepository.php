<?php

require_once 'Repository.php';
require_once __DIR__.'/../models/User.php';

class UsersRepository extends Repository {

    public function getUserByEmail(string $email): ?User {
        $stmt = $this->database->connect()->prepare('
            SELECT * FROM users WHERE email = :email
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
            $user['full_name'],
            $user['id']
        );
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

    public function getUsers(): array {
        $stmt = $this->database->connect()->prepare('SELECT * FROM users');
        $stmt->execute();
        $usersData = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $result = [];
        foreach ($usersData as $data) {
            $result[] = new User(
                $data['username'],
                $data['email'],
                $data['password'],
                $data['full_name'],
                $data['id']
            );
        }
        return $result;
    }
}