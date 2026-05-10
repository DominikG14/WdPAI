<?php

require_once 'Repository.php';

class UsersRepository extends Repository {

    /**
     * Pobiera wszystkich użytkowników z bazy danych.
     */
    public function getUsers(): array 
    {
        $stmt = $this->database->connect()->prepare('
            SELECT * FROM users
        ');
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Pobiera użytkownika na podstawie adresu email.
     * Wykorzystywane przy logowaniu oraz sprawdzaniu czy email jest zajęty.
     */
    public function getUserByEmail(string $email) 
    {
        $stmt = $this->database->connect()->prepare('
            SELECT * FROM users WHERE email = :email
        ');
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user == false) {
            return null;
        }

        return $user;
    }

    /**
     * Pobiera użytkownika na podstawie nazwy użytkownika (username).
     * Ważne, ponieważ w Twojej bazie to pole ma ograniczenie UNIQUE.
     */
    public function getUserByUsername(string $username) 
    {
        $stmt = $this->database->connect()->prepare('
            SELECT * FROM users WHERE username = :username
        ');
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user == false) {
            return null;
        }

        return $user;
    }

    /**
     * Dodaje nowego użytkownika do bazy danych.
     * Dane są bindowane automatycznie przez execute(), co chroni przed SQL Injection.
     */
    public function addUser(string $username, string $email, string $password, ?string $fullName) 
    {
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