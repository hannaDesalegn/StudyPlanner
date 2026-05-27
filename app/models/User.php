<?php

require_once "../config/db.php";

class User
{
    private $pdo;

    public function __construct()
    {
        global $pdo;
        $this->pdo = $pdo;
    }

    public function findByEmail($email)
    {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM users WHERE email = ?"
        );

        $stmt->execute([$email]);

        return $stmt->fetch();
    }

    public function createUser($username, $email, $password)
    {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $this->pdo->prepare(
            "INSERT INTO users(username, email, password)
             VALUES(?, ?, ?)"
        );

        return $stmt->execute([
            $username,
            $email,
            $hashedPassword
        ]);
    }

    public function getLastInsertedId()
    {
        return $this->pdo->lastInsertId();
    }
}