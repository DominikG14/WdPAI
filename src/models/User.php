<?php

class User {
    private $id;
    private $username;
    private $email;
    private $password;
    private $fullName;

    public function __construct($username, $email, $password, $fullName, $id = null) {
        $this->username = $username;
        $this->email = $email;
        $this->password = $password;
        $this->fullName = $fullName;
        $this->id = $id;
    }

    public function getId() { return $this->id; }
    public function getUsername() { return $this->username; }
    public function getEmail() { return $this->email; }
    public function getPassword() { return $this->password; }
    public function getFullName() { return $this->fullName; }
}