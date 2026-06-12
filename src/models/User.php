<?php

class User {
    private $id;
    private $username;
    private $email;
    private $password;
    private $fullName;

    /**
     * Create a user data object.
     *
     * @param string $username Public username.
     * @param string $email Email address.
     * @param string|null $password Password hash when loaded for authentication.
     * @param string|null $fullName Optional display name.
     * @param int|null $id Database identifier.
     */
    public function __construct($username, $email, $password, $fullName, $id = null) {
        $this->username = $username;
        $this->email = $email;
        $this->password = $password;
        $this->fullName = $fullName;
        $this->id = $id;
    }

    /** @return int|null User identifier. */
    public function getId() { return $this->id; }

    /** @return string Public username. */
    public function getUsername() { return $this->username; }

    /** @return string Email address. */
    public function getEmail() { return $this->email; }

    /** @return string|null Password hash when available. */
    public function getPassword() { return $this->password; }

    /** @return string|null Optional full name. */
    public function getFullName() { return $this->fullName; }
}
